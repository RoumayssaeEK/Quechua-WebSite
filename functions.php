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

/**
 * Fonction pour nettoyer les données d'entrée
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Fonction pour valider un fichier uploadé
 */
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
    
    // Vérifier le type MIME
    if (!in_array($file['type'], $allowedTypes[$type])) {
        $errors[] = "Type de fichier non autorisé pour " . $type;
    }
    
    // Vérifier la taille
    if ($file['size'] > $maxSizes[$type]) {
        $errors[] = "Fichier trop volumineux pour " . $type;
    }
    
    return $errors;
}

/**
 * Fonction pour uploader un fichier
 */
function uploadFile($file, $destination) {
    $uploadDir = 'uploads/' . $destination . '/';
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Générer un nom de fichier unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = time() . '_' . uniqid() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return $fileName;
    }
    
    return false;
}

/**
 * Fonction pour ajouter une chanson
 */
function ajouterChanson($data, $files) {
    global $pdo;
    
    try {
        $data = sanitizeInput($data);
        
        // Validation des données requises
        $required = ['titre', 'titre_fr', 'paroles_quechua', 'paroles_fr'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return ['success' => false, 'error' => "Le champ $field est requis."];
            }
        }
        
        // Initialiser les chemins des fichiers
        $audioPath = null;
        $karaokePath = null;
        
        // Upload fichier audio
        if (isset($files['audio']) && $files['audio']['error'] == UPLOAD_ERR_OK) {
            $errors = validateFile($files['audio'], 'audio');
            if (empty($errors)) {
                $audioPath = uploadFile($files['audio'], 'audio');
            } else {
                return ['success' => false, 'error' => implode(', ', $errors)];
            }
        }
        
        // Upload fichier karaoké
        if (isset($files['karaoke']) && $files['karaoke']['error'] == UPLOAD_ERR_OK) {
            $errors = validateFile($files['karaoke'], 'audio');
            if (empty($errors)) {
                $karaokePath = uploadFile($files['karaoke'], 'karaoke');
            } else {
                return ['success' => false, 'error' => implode(', ', $errors)];
            }
        }
        
        // Insertion en base de données
        $sql = "INSERT INTO chansons (titre, titre_fr, paroles_quechua, paroles_fr, audio, karaoke) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['titre'],
            $data['titre_fr'],
            $data['paroles_quechua'],
            $data['paroles_fr'],
            $audioPath,
            $karaokePath
        ]);
        
        $chansonId = $pdo->lastInsertId();
        
        // Log de l'action
        logAction(
            $_SESSION['user_id'] ?? 'système',
            'ajout_chanson',
            'chansons',
            $chansonId,
            "Titre: " . $data['titre']
        );
        
        return ['success' => true, 'id' => $chansonId];
        
    } catch (PDOException $e) {
        error_log("Erreur ajout chanson: " . $e->getMessage());
        return ['success' => false, 'error' => 'Erreur lors de l\'ajout de la chanson.'];
    }
}

/**
 * Fonction pour modifier une chanson
 */
function modifierChanson($data, $files) {
    global $pdo;
    
    try {
        $data = sanitizeInput($data);
        
        if (empty($data['id'])) {
            return ['success' => false, 'error' => 'ID de chanson manquant.'];
        }
        
        // Récupérer les données actuelles
        $stmt = $pdo->prepare("SELECT * FROM chansons WHERE id = ?");
        $stmt->execute([$data['id']]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$current) {
            return ['success' => false, 'error' => 'Chanson introuvable.'];
        }
        
        // Conserver les fichiers actuels
        $audioPath = $current['audio'];
        $karaokePath = $current['karaoke'];
        
        // Upload des nouveaux fichiers si présents
        if (isset($files['audio']) && $files['audio']['error'] == UPLOAD_ERR_OK) {
            $errors = validateFile($files['audio'], 'audio');
            if (empty($errors)) {
                $newAudio = uploadFile($files['audio'], 'audio');
                if ($newAudio) {
                    // Supprimer l'ancien fichier
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
                    // Supprimer l'ancien fichier
                    if ($karaokePath && file_exists('uploads/karaoke/' . $karaokePath)) {
                        unlink('uploads/karaoke/' . $karaokePath);
                    }
                    $karaokePath = $newKaraoke;
                }
            } else {
                return ['success' => false, 'error' => implode(', ', $errors)];
            }
        }
        
        // Mise à jour en base de données
        $sql = "UPDATE chansons SET titre = ?, titre_fr = ?, paroles_quechua = ?, paroles_fr = ?, audio = ?, karaoke = ? WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['titre'],
            $data['titre_fr'],
            $data['paroles_quechua'],
            $data['paroles_fr'],
            $audioPath,
            $karaokePath,
            $data['id']
        ]);
        
        // Log de l'action
        logAction(
            $_SESSION['user_id'] ?? 'système',
            'modification_chanson',
            'chansons',
            $data['id'],
            "Titre: " . $data['titre']
        );
        
        return ['success' => true, 'id' => $data['id']];
        
    } catch (PDOException $e) {
        error_log("Erreur modification chanson: " . $e->getMessage());
        return ['success' => false, 'error' => 'Erreur lors de la modification de la chanson.'];
    }
}

/**
 * Fonction pour supprimer une chanson
 */
function supprimerChanson($id) {
    global $pdo;
    
    try {
        // Validation de l'ID
        if (empty($id) || !is_numeric($id)) {
            return ['success' => false, 'error' => 'ID de chanson invalide.'];
        }
        
        // Récupérer les informations de la chanson
        $stmt = $pdo->prepare("SELECT * FROM chansons WHERE id = ?");
        $stmt->execute([$id]);
        $chanson = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$chanson) {
            return ['success' => false, 'error' => 'Chanson introuvable.'];
        }
        
        // Supprimer les fichiers associés
        if ($chanson['audio'] && file_exists('uploads/audio/' . $chanson['audio'])) {
            unlink('uploads/audio/' . $chanson['audio']);
        }
        
        if ($chanson['karaoke'] && file_exists('uploads/karaoke/' . $chanson['karaoke'])) {
            unlink('uploads/karaoke/' . $chanson['karaoke']);
        }
        
        // Supprimer l'enregistrement de la base de données
        $stmt = $pdo->prepare("DELETE FROM chansons WHERE id = ?");
        $stmt->execute([$id]);
        
        // Vérifier si la suppression a réussi
        if ($stmt->rowCount() > 0) {
            // Log de l'action
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

/**
 * Fonction pour récupérer toutes les chansons
 */
function obtenirChansons($limit = null, $offset = 0) {
    global $pdo;
    
    try {
        $sql = "SELECT * FROM chansons ORDER BY titre ASC";
        
        if ($limit !== null) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur récupération chansons: " . $e->getMessage());
        return [];
    }
}

/**
 * Fonction pour récupérer une chanson par ID
 */
function obtenirChanson($id) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM chansons WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur récupération chanson: " . $e->getMessage());
        return null;
    }
}

/**
 * Fonction pour rechercher des chansons
 */
function rechercherChansons($terme) {
    global $pdo;
    
    try {
        $terme = '%' . $terme . '%';
        $sql = "SELECT * FROM chansons WHERE titre LIKE ? OR titre_fr LIKE ? OR paroles_quechua LIKE ? OR paroles_fr LIKE ? ORDER BY titre ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$terme, $terme, $terme, $terme]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Erreur recherche chansons: " . $e->getMessage());
        return [];
    }
}

/**
 * Fonction pour créer les répertoires nécessaires
 */
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

// Initialiser les répertoires au chargement du script
creerRepertoires();

?>