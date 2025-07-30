<?php

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function logAction($utilisateur_id, $action, $table_affectee = null, $id_enregistrement = null, $details = null) {
    $log_entry = date('Y-m-d H:i:s') . " - User: $utilisateur_id - Action: $action - Table: $table_affectee - ID: $id_enregistrement - Details: $details\n";
    file_put_contents('logs/admin.log', $log_entry, FILE_APPEND | LOCK_EX);
    return true;
}

function getStatistiques() {
    global $pdo;
    
    try {
        $stats = [];
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM chansons");
        $stats['total_chansons'] = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM chansons WHERE audio IS NOT NULL AND audio != ''");
        $stats['chansons_avec_audio'] = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM chansons WHERE karaoke IS NOT NULL AND karaoke != ''");
        $stats['chansons_avec_karaoke'] = $stmt->fetchColumn();
        
        return $stats;
    } catch (PDOException $e) {
        error_log("Erreur statistiques: " . $e->getMessage());
        return [
            'total_chansons' => 0,
            'chansons_avec_audio' => 0,
            'chansons_avec_karaoke' => 0
        ];
    }
}


function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}


function validateFile($file, $type = 'audio') {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Erreur lors de l'upload du fichier.";
        return $errors;
    }
    
    $allowedTypes = [
        'audio' => ['audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg', 'audio/mp4'],
        'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
    ];
    
    $maxSizes = [
        'audio' => 50 * 1024 * 1024, // 50MB
        'image' => 5 * 1024 * 1024   // 5MB
    ];
    
    
    if (!in_array($file['type'], $allowedTypes[$type])) {
        $errors[] = "Type de fichier non autorisé pour " . $type;
    }
    
    
    if ($file['size'] > $maxSizes[$type]) {
        $errors[] = "Fichier trop volumineux pour " . $type;
    }
    
    return $errors;
}


function uploadFile($file, $destination) {
    $uploadDir = 'uploads/' . $destination . '/';
    
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = time() . '_' . uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return $fileName;
    }
    
    return false;
}


function ajouterChanson($data, $files) {
    global $pdo;
    
    try {
        $data = sanitizeInput($data);
        
        
        $required = ['titre_quechua', 'titre_langue', 'paroles_quechua'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'error' => "Le champ $field est requis."];
            }
        }
        
        
        $audioPath = null;
        $karaokePath = null;
        
       
        if (isset($files['audio']) && $files['audio']['error'] == UPLOAD_ERR_OK) {
            $errors = validateFile($files['audio'], 'audio');
            if (empty($errors)) {
                $audioPath = uploadFile($files['audio'], 'audio');
            } else {
                return ['success' => false, 'error' => implode(', ', $errors)];
            }
        }
        
        
        if (isset($files['karaoke']) && $files['karaoke']['error'] == UPLOAD_ERR_OK) {
            $errors = validateFile($files['karaoke'], 'audio');
            if (empty($errors)) {
                $karaokePath = uploadFile($files['karaoke'], 'karaoke');
            } else {
                return ['success' => false, 'error' => implode(', ', $errors)];
            }
        }
        
        
        $idLangue = !empty($data['id_langue']) ? (int)$data['id_langue'] : null;
        $idInterprete = !empty($data['id_interprete']) ? (int)$data['id_interprete'] : null;
        $idAuteur = !empty($data['id_auteur']) ? (int)$data['id_auteur'] : null;
        $idTraducteur = !empty($data['id_traducteur']) ? (int)$data['id_traducteur'] : null;
        $parolesLangue = !empty($data['paroles_langue']) ? $data['paroles_langue'] : null;
        
        
        $sql = "INSERT INTO chansons (titre_quechua, titre_langue, paroles_quechua, paroles_langue, audio, karaoke, id_langue, id_interprete, id_auteur, id_traducteur, likes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['titre_quechua'],
            $data['titre_langue'],
            $data['paroles_quechua'],
            $parolesLangue,
            $audioPath,
            $karaokePath,
            $idLangue,
            $idInterprete,
            $idAuteur,
            $idTraducteur
        ]);
        
        $chansonId = $pdo->lastInsertId();
        
        
        logAction(
            $_SESSION['user_id'] ?? 'système',
            'ajout_chanson',
            'chansons',
            $chansonId,
            "Titre: " . $data['titre_quechua']
        );
        
        return ['success' => true, 'id' => $chansonId];
        
    } catch (PDOException $e) {
        error_log("Erreur ajout chanson: " . $e->getMessage());
        return ['success' => false, 'error' => 'Erreur lors de l\'ajout de la chanson.'];
    }
}


function modifierChanson($data, $files) {
    global $pdo;
    
    try {
        $data = sanitizeInput($data);
        
        if (empty($data['id'])) {
            return ['success' => false, 'error' => 'ID de chanson manquant.'];
        }
        
        
        $stmt = $pdo->prepare("SELECT * FROM chansons WHERE id = ?");
        $stmt->execute([$data['id']]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current) {
            return ['success' => false, 'error' => 'Chanson introuvable.'];
        }
        
        
        $audioPath = $current['audio'];
        $karaokePath = $current['karaoke'];
        
      
        if (isset($files['audio']) && $files['audio']['error'] == UPLOAD_ERR_OK) {
            $errors = validateFile($files['audio'], 'audio');
            if (empty($errors)) {
                $newAudio = uploadFile($files['audio'], 'audio');
                if ($newAudio) {
                    
                    if ($audioPath && file_exists('uploads/audio/' . $audioPath)) {
                        unlink('uploads/audio/' . $audioPath);
                    }
                    $audioPath = $newAudio;
                }
            } else {
                return ['success' => false, 'error' => implode(', ', $errors)];
            }
        }
        
        if (isset($files['karaoke']) && $files['karaoke']['error'] == UPLOAD_ERR_OK) {
            $errors = validateFile($files['karaoke'], 'audio');
            if (empty($errors)) {
                $newKaraoke = uploadFile($files['karaoke'], 'karaoke');
                if ($newKaraoke) {
                   
                    if ($karaokePath && file_exists('uploads/karaoke/' . $karaokePath)) {
                        unlink('uploads/karaoke/' . $karaokePath);
                    }
                    $karaokePath = $newKaraoke;
                }
            } else {
                return ['success' => false, 'error' => implode(', ', $errors)];
            }
        }
        
        
        $idLangue = !empty($data['id_langue']) ? (int)$data['id_langue'] : null;
        $idInterprete = !empty($data['id_interprete']) ? (int)$data['id_interprete'] : null;
        $idAuteur = !empty($data['id_auteur']) ? (int)$data['id_auteur'] : null;
        $idTraducteur = !empty($data['id_traducteur']) ? (int)$data['id_traducteur'] : null;
        $parolesLangue = !empty($data['paroles_langue']) ? $data['paroles_langue'] : null;
        
        $sql = "UPDATE chansons SET titre_quechua = ?, titre_langue = ?, paroles_quechua = ?, paroles_langue = ?, audio = ?, karaoke = ?, id_langue = ?, id_interprete = ?, id_auteur = ?, id_traducteur = ? WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['titre_quechua'],
            $data['titre_langue'],
            $data['paroles_quechua'],
            $parolesLangue,
            $audioPath,
            $karaokePath,
            $idLangue,
            $idInterprete,
            $idAuteur,
            $idTraducteur,
            $data['id']
        ]);
        
        logAction(
            $_SESSION['user_id'] ?? 'système',
            'modification_chanson',
            'chansons',
            $data['id'],
            "Titre: " . $data['titre_quechua']
        );
        
        return ['success' => true, 'id' => $data['id']];
        
    } catch (PDOException $e) {
        error_log("Erreur modification chanson: " . $e->getMessage());
        return ['success' => false, 'error' => 'Erreur lors de la modification de la chanson.'];
    }
}


function supprimerChanson($id) {
    global $pdo;
    
    try {
       
        if (empty($id) || !is_numeric($id)) {
            return ['success' => false, 'error' => 'ID de chanson invalide.'];
        }
        
        
        $stmt = $pdo->prepare("SELECT * FROM chansons WHERE id = ?");
        $stmt->execute([$id]);
        $chanson = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$chanson) {
            return ['success' => false, 'error' => 'Chanson introuvable.'];
        }
        
       
        if ($chanson['audio'] && file_exists('uploads/audio/' . $chanson['audio'])) {
            unlink('uploads/audio/' . $chanson['audio']);
        }
        
        if ($chanson['karaoke'] && file_exists('uploads/karaoke/' . $chanson['karaoke'])) {
            unlink('uploads/karaoke/' . $chanson['karaoke']);
        }
        
        
        $stmt = $pdo->prepare("DELETE FROM chansons WHERE id = ?");
        $stmt->execute([$id]);
        
       
        if ($stmt->rowCount() > 0) {
           
            logAction(
                $_SESSION['user_id'] ?? 'système',
                'suppression_chanson',
                'chansons',
                $id,
                "Titre: " . $chanson['titre']
            );
            
            return ['success' => true, 'message' => 'Chanson supprimée avec succès.'];
        } else {
            return ['success' => false, 'error' => 'Erreur lors de la suppression de la chanson.'];
        }
        
    } catch (PDOException $e) {
        error_log("Erreur suppression chanson: " . $e->getMessage());
        return ['success' => false, 'error' => 'Erreur lors de la suppression de la chanson.'];
    }
}



function creerRepertoires() {
    $directories = [
        'uploads/audio',
        'uploads/karaoke',
        'logs'
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}


creerRepertoires();

?>