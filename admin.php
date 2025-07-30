<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once 'config/database.php';

$database = new Database();
$pdo = $database->getConnection();

$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$totalChansons = $database->getTotalChansons();
$chansons = $database->getChansonsWithLanguePaginated($limit, $offset);
$totalPages = ceil($totalChansons / $limit);


$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'ajouter':
            $message = ajouterChanson($pdo);
            break;
        case 'modifier':
            $message = modifierChanson($pdo);
            break;
        case 'supprimer':
            $message = supprimerChanson($pdo);
            break;
    }
}

// Fonction pour ajouter une chanson
function ajouterChanson($pdo) {
    try {
        $titre_quechua = $_POST['titre_quechua'] ?? '';
        $titre_langue = $_POST['titre_langue'] ?? '';
        $paroles_quechua = $_POST['paroles_quechua'] ?? '';
        $paroles_langue = $_POST['paroles_langue'] ?? '';
        $id_langue = $_POST['id_langue'] ?? null;
        
        // Gestion des fichiers audio et karaoké
        $audio_path = '';
        $karaoke_path = '';
        
        // Upload audio
        if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
            $audio_path = uploadFile($_FILES['audio'], 'audio');
        }
        
        // Upload karaoké
        if (isset($_FILES['karaoke']) && $_FILES['karaoke']['error'] === UPLOAD_ERR_OK) {
            $karaoke_path = uploadFile($_FILES['karaoke'], 'karaoke');
        }

         // Vérifier ou insérer l'auteur
        $nomAuteur = trim($_POST['nom_auteur']);
        $stmt = $pdo->prepare("SELECT id_auteur FROM auteur WHERE LOWER(nom) = LOWER(:nom)");
        $stmt->execute([':nom' => $nomAuteur]);
        $auteur = $stmt->fetch();

        if ($auteur) {
          $idAuteur = $auteur['id_auteur'];
        } else {
        $stmt = $pdo->prepare("INSERT INTO auteur (nom) VALUES (:nom)");
        $stmt->execute([':nom' => $nomAuteur]);
        $idAuteur = $pdo->lastInsertId();
        }

        // Vérifier ou insérer l’interprète
        $nomInterprete = trim($_POST['nom_interprete']);
        $stmt = $pdo->prepare("SELECT id_interprete FROM interprete WHERE LOWER(nom) = LOWER(:nom)");
        $stmt->execute([':nom' => $nomInterprete]);
        $interprete = $stmt->fetch();

        if ($interprete) {
         $idInterprete = $interprete['id_interprete'];
        } else {
        $stmt = $pdo->prepare("INSERT INTO interprete (nom) VALUES (:nom)");
        $stmt->execute([':nom' => $nomInterprete]);
        $idInterprete = $pdo->lastInsertId();
        }

        // Vérifier ou insérer le traducteur
        $nomTraducteur = trim($_POST['nom_traducteur']);
        $idTraducteur = null;

        if (!empty($nomTraducteur)) {
         $stmt = $pdo->prepare("SELECT id_traducteur FROM traducteur WHERE LOWER(nom) = LOWER(:nom)");
         $stmt->execute([':nom' => $nomTraducteur]);
         $traducteur = $stmt->fetch();

           if ($traducteur) {
             $idTraducteur = $traducteur['id_traducteur'];
           } else {
           $stmt = $pdo->prepare("INSERT INTO traducteur (nom) VALUES (:nom)");
           $stmt->execute([':nom' => $nomTraducteur]);
           $idTraducteur = $pdo->lastInsertId();
           }
        }
        
        $query = "INSERT INTO chansons (titre_quechua, titre_langue, paroles_quechua, paroles_langue, 
                  id_langue, audio, karaoke, id_interprete, id_auteur, id_traducteur) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $titre_quechua, $titre_langue, $paroles_quechua, $paroles_langue,
            $id_langue, $audio_path, $karaoke_path,$idInterprete,$idAuteur,$idTraducteur
        ]);
        
        return "Chanson ajoutée avec succès !";
    } catch (Exception $e) {
        return "Erreur lors de l'ajout : " . $e->getMessage();
    }
}

// Fonction pour modifier une chanson
function modifierChanson($pdo) {
    try {
        $id = $_POST['id'] ?? 0;
        $titre_quechua = $_POST['titre_quechua'] ?? '';
        $titre_langue = $_POST['titre_langue'] ?? '';
        $paroles_quechua = $_POST['paroles_quechua'] ?? '';
        $paroles_langue = $_POST['paroles_langue'] ?? '';
        $id_langue = $_POST['id_langue'] ?? null;
        $nomAuteur = $_POST['nom_auteur'] ?? null;
        $nomInterprete = $_POST['nom_interprete'] ?? null;
        $nomTraducteur = $_POST['nom_traducteur'] ?? null;

        
        
        $stmt = $pdo->prepare("SELECT audio, karaoke FROM chansons WHERE id = ?");
        $stmt->execute([$id]);
        $chanson = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $audio_path = $chanson['audio'];
        $karaoke_path = $chanson['karaoke'];

        if (isset($_POST['delete_audio']) && $_POST['delete_audio'] == '1') {
            if ($audio_path && file_exists("media/audios/" . $audio_path)) {
                unlink("media/audios/" . $audio_path);
            }
            $audio_path = null;  
        }
        
        // Supprimer karaoke si demandé
        if (isset($_POST['delete_karaoke']) && $_POST['delete_karaoke'] == '1') {
            if ($karaoke_path && file_exists("media/karaoke/" . $karaoke_path)) {
                unlink("media/karaoke/" . $karaoke_path);
            }
            $karaoke_path = null; 
        }
        
        // Upload nouveaux fichiers si fournis
        if (isset($_FILES['audio']) && $_FILES['audio']['error'] === UPLOAD_ERR_OK) {
            // Supprimer l'ancien fichier
            if ($audio_path && file_exists("media/audios/" . $audio_path)) {
                unlink("media/audios/" . $audio_path);
            }
            $audio_path = uploadFile($_FILES['audio'], 'audio');
        }
        
        if (isset($_FILES['karaoke']) && $_FILES['karaoke']['error'] === UPLOAD_ERR_OK) {
            // Supprimer l'ancien fichier
            if ($karaoke_path && file_exists("media/karaoke/" . $karaoke_path)) {
                unlink("media/karaoke/" . $karaoke_path);
            }
            $karaoke_path = uploadFile($_FILES['karaoke'], 'karaoke');
        }

        // Gérer l’auteur
        if ($nomAuteur) {
            $stmtAuteur = $pdo->prepare("SELECT id_auteur FROM auteur WHERE LOWER(nom) = LOWER(:nom)");
            $stmtAuteur->execute([':nom' => $nomAuteur]);
            $auteur = $stmtAuteur->fetch();

        if ($auteur) {
            $idAuteur = $auteur['id_auteur'];
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO auteur (nom) VALUES (:nom)");
            $stmtInsert->execute([':nom' => $nomAuteur]);
            $idAuteur = $pdo->lastInsertId();
        }
        } else {
            $idAuteur = null;
        }

      // Gérer l’interprète
       if ($nomInterprete) {
           $stmtInterprete = $pdo->prepare("SELECT id_interprete FROM interprete WHERE LOWER(nom) = LOWER(:nom)");
           $stmtInterprete->execute([':nom' => $nomInterprete]);
           $interprete = $stmtInterprete->fetch();

        if ($interprete) {
            $idInterprete = $interprete['id_interprete'];
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO interprete (nom) VALUES (:nom)");
            $stmtInsert->execute([':nom' => $nomInterprete]);
            $idInterprete = $pdo->lastInsertId();
        }
        } else {
            $idInterprete = null;
        }

        // Gérer le traducteur
        if (!empty($_POST['nom_traducteur'])) {
           $nomTraducteur = trim($_POST['nom_traducteur']);
           $stmtTraducteur = $pdo->prepare("SELECT id_traducteur FROM traducteur WHERE LOWER(nom) = LOWER(:nom)");
           $stmtTraducteur->execute([':nom' => $nomTraducteur]);
           $traducteur = $stmtTraducteur->fetch();

        if ($traducteur) {
            $idTraducteur = $traducteur['id_traducteur'];
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO traducteur (nom) VALUES (:nom)");
            $stmtInsert->execute([':nom' => $nomTraducteur]);
            $idTraducteur = $pdo->lastInsertId();
        }
        } else {
            $idTraducteur = null;
        }

        
        $query = "UPDATE chansons SET titre_quechua = ?, titre_langue = ?, paroles_quechua = ?, 
                  paroles_langue = ?, id_langue = ?, audio = ?, karaoke = ?, id_interprete = ?, id_auteur = ?, id_traducteur = ? WHERE id = ?";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            $titre_quechua, $titre_langue, $paroles_quechua, $paroles_langue,
            $id_langue, $audio_path, $karaoke_path, $idInterprete, $idAuteur, $idTraducteur, $id
        ]);
        
        return "Chanson modifiée avec succès !";
    } catch (Exception $e) {
        return "Erreur lors de la modification : " . $e->getMessage();
    }
}

// Fonction pour supprimer une chanson
function supprimerChanson($pdo) {
    try {
        $id = $_POST['id'] ?? 0;
        
        
        $stmt = $pdo->prepare("SELECT audio, karaoke FROM chansons WHERE id = ?");
        $stmt->execute([$id]);
        $chanson = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Supprimer les fichiers
        if ($chanson['audio'] && file_exists("media/audios/" . $chanson['audio'])) {
            unlink("media/audios/" . $chanson['audio']);
        }
        if ($chanson['karaoke'] && file_exists("media/karaoke/" . $chanson['karaoke'])) {
            unlink("media/karaoke/" . $chanson['karaoke']);
        }
        
        // Supprimer de la base de données
        $stmt = $pdo->prepare("DELETE FROM chansons WHERE id = ?");
        $stmt->execute([$id]);
        
        return "Chanson supprimée avec succès !";
    } catch (Exception $e) {
        return "Erreur lors de la suppression : " . $e->getMessage();
    }
}

// Fonction pour upload des fichiers
function uploadFile($file, $type) {
    
    if ($type === 'audio') {
        $upload_dir = "media/audios/";
        $allowed_types = ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp3'];
        $allowed_extensions = ['mp3', 'wav', 'ogg'];
    } elseif ($type === 'karaoke') {
        $upload_dir = "media/karaoke/";
        $allowed_types = ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/mp3', 'video/mp4'];
        $allowed_extensions = ['mp3', 'wav', 'ogg', 'mp4'];
    } else {
        throw new Exception("Type de fichier non supporté");
    }
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception("Impossible de créer le dossier : " . $upload_dir);
        }
    }
    
    // Vérifier l'extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowed_extensions)) {
        throw new Exception("Extension de fichier non autorisée. Extensions autorisées : " . implode(', ', $allowed_extensions));
    }
    
    
    if (!in_array($file['type'], $allowed_types)) {
        
        error_log("Type MIME non reconnu : " . $file['type'] . " pour le fichier : " . $file['name']);
    }
    
    
    $max_size = 50 * 1024 * 1024; // 50MB
    if ($file['size'] > $max_size) {
        throw new Exception("Le fichier est trop volumineux. Taille maximale : 50MB");
    }

    $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $clean_name = preg_replace("/[^a-zA-Z0-9\-_]/", "_", $original_name);
    
    
    $filename = $clean_name . '_' . uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
   
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        throw new Exception("Erreur lors de l'upload du fichier");
    }
    
    return $filename;
}

// Récupération des données pour l'affichage
$search = $_GET['search'] ?? null;

if ($search) {
    $chansons = $database->searchChansonsByTitre($search);
} else {
    $chansons = $database->getChansonsWithDetailsPaginated($limit, $offset);
}


$langues = $database->getLangues();

$page_title = "Administration - Ma chanson en quechua";
include 'includes/header.php';
?>

<style>
.admin-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.admin-header {
    background: linear-gradient(135deg, #3498db, #2c3e50);
    color: white;
    padding: 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    text-align: center;
}

.admin-section {
    background: white;
    border-radius: 10px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #2c3e50;
}

.form-control {
    width: 100%;
    padding: 10px;
    border: 2px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

.form-control:focus {
    border-color: #3498db;
    outline: none;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    margin-right: 10px;
    margin-bottom: 10px;
}

.btn-primary {
    background: #3498db;
    color: white;
}

.btn-success {
    background: #27ae60;
    color: white;
}

.btn-danger {
    background: #e74c3c;
    color: white;
}

.btn-warning {
    background: #f39c12;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}

.songs-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.songs-table th,
.songs-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.songs-table th {
    background: #f8f9fa;
    font-weight: bold;
    color: #2c3e50;
}

.songs-table tr:hover {
    background: #f8f9fa;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

.logout-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    background: #e74c3c;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
}

.logout-btn:hover {
    background: #c0392b;
    color: white;
}

.file-info {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

.file-preview {
    margin-top: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
}
</style>

<div class="admin-container">
    <div class="admin-header">
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
        <h1><i class="fas fa-cog"></i> Administration Ma chanson en quechua</h1>
        <p>Gérez les chansons de votre collection</p>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

     <div class="admin-section add-section">
        <h2><i class="fas fa-plus"></i> Ajouter une chanson</h2>
        <button class="btn btn-primary btn-add" onclick="openModal('addModal')">
            <i class="fas fa-plus"></i> Nouvelle chanson
        </button>
    </div>
    <div class="admin-section">
        <form method="get" action="" class="search-form">
            <input type="text" name="search" placeholder="Rechercher un titre..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit">
                <i class="fas fa-search"></i> Rechercher
            </button>
        </form>
    </div>


    <div class="admin-section">
        <h2><i class="fas fa-list"></i> Liste des chansons (<?php echo count($chansons); ?>)</h2>
        
        <table class="songs-table">
            <thead>
                <tr>
                    <th>Titre Quechua</th>
                    <th>Titre Langue</th>
                    <th>Langue</th>
                    <th>Audio</th>
                    <th>Karaoké</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($chansons as $chanson): ?>
                <tr>
                    <td><?php echo htmlspecialchars($chanson['titre_quechua']); ?></td>
                    <td><?php echo htmlspecialchars($chanson['titre_langue']); ?></td>
                    <td><?php echo htmlspecialchars($chanson['nom_langue'] ?? 'Non défini'); ?></td>
                    <td>
                        <?php if ($chanson['audio']): ?>
                            <i class="fas fa-check text-success"></i>
                            <small>MP3</small>
                        <?php else: ?>
                            <i class="fas fa-times text-danger"></i>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($chanson['karaoke']): ?>
                            <i class="fas fa-check text-success"></i>
                            <small><?php echo strtoupper(pathinfo($chanson['karaoke'], PATHINFO_EXTENSION)); ?></small>
                        <?php else: ?>
                            <i class="fas fa-times text-danger"></i>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-warning" onclick="editSong(<?php echo $chanson['id']; ?>)">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="deleteSong(<?php echo $chanson['id']; ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <nav aria-label="Pagination">
                <ul class="pagination justify-content-center mt-4">
                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo max(1, $page - 1); ?>" aria-label="Page précédente">
                            &laquo;
                        </a>
                    </li>
                    <?php
                    $range = 2; 
                    $start = max(1, $page - $range);
                    $end = min($totalPages, $page + $range);

                    if ($start > 1) {
                      echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                      if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        echo '<li class="page-item">';
                        echo '<a class="page-link" href="?page=' . $i . '" style="' . ($i == $page ? 'font-weight:bold; background-color:transparent; border:none;' : '') . '">' . $i . '</a>';
                        echo '</li>';
                    }

                    if ($end < $totalPages) {
                       if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                       echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
                    }
                    ?>
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                       <a class="page-link" href="?page=<?php echo min($totalPages, $page + 1); ?>" aria-label="Page suivante">
                          &raquo;
                       </a>
                    </li>   
                </ul>
             </nav>
   
    </div>
</div>


<!-- Modal d'ajout -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('addModal')">&times;</span>
        <h3>Ajouter une nouvelle chanson</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="ajouter">
            
            <div class="form-group">
                <label for="titre_quechua">Titre en Quechua *</label>
                <input type="text" class="form-control" name="titre_quechua" required>
            </div>
            
            <div class="form-group">
                <label for="titre_langue">Titre dans la langue *</label>
                <input type="text" class="form-control" name="titre_langue" required>
            </div>
            
            <div class="form-group">
                <label for="id_langue">Langue</label>
                <select class="form-control" name="id_langue">
                    <option value="">Sélectionner une langue</option>
                    <?php foreach ($langues as $langue): ?>
                        <option value="<?php echo $langue['id_langue']; ?>">
                            <?php echo htmlspecialchars($langue['nom_langue']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="paroles_quechua">Paroles en Quechua *</label>
                <textarea class="form-control" name="paroles_quechua" rows="5" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="paroles_langue">Paroles dans la langue</label>
                <textarea class="form-control" name="paroles_langue" rows="5"></textarea>
            </div>

            <!-- Nom de l’auteur -->
           <div class="mb-3">
               <label for="nom_auteur" class="form-label">Nom de l’auteur</label>
               <input type="text" name="nom_auteur" id="nom_auteur" class="form-control" required>
           </div>

          <!-- Nom de l’interprète -->
           <div class="mb-3">
               <label for="nom_interprete" class="form-label">Nom de l’interprète</label>
               <input type="text" name="nom_interprete" id="nom_interprete" class="form-control" required>
           </div>

           <!-- Nom du traducteur -->
           <div class="mb-3">
              <label for="nom_traducteur" class="form-label">Nom du traducteur</label>
              <input type="text" name="nom_traducteur" id="nom_traducteur" class="form-control">
          </div>

            
            <div class="form-group">
                <label for="audio">Fichier Audio (MP3, WAV, OGG)</label>
                <input type="file" class="form-control" name="audio" accept="audio/*">
                <div class="file-info">
                    <i class="fas fa-info-circle"></i> Formats acceptés : MP3, WAV, OGG (max 50MB)
                </div>
            </div>
            
            <div class="form-group">
                <label for="karaoke">Fichier Karaoké (MP3, WAV, OGG, MP4)</label>
                <input type="file" class="form-control" name="karaoke" accept="audio/*,video/mp4">
                <div class="file-info">
                    <i class="fas fa-info-circle"></i> Formats acceptés : MP3, WAV, OGG, MP4 (max 50MB)
                </div>
            </div>
            
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Ajouter la chanson
            </button>
        </form>
    </div>
</div>



<!-- Modal de modification -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editModal')"><i class="fas fa-times"></i></span>
        <h3>Modifier la chanson</h3>
        <form method="POST" enctype="multipart/form-data" id="editForm">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" name="id" id="edit_id">
            
            <div class="form-group">
                <label for="edit_titre_quechua">Titre en Quechua *</label>
                <input type="text" class="form-control" name="titre_quechua" id="edit_titre_quechua" required>
            </div>
            
            <div class="form-group">
                <label for="edit_titre_langue">Titre dans la langue *</label>
                <input type="text" class="form-control" name="titre_langue" id="edit_titre_langue" required>
            </div>
            
            <div class="form-group">
                <label for="edit_id_langue">Langue</label>
                <select class="form-control" name="id_langue" id="edit_id_langue">
                    <option value="">Sélectionner une langue</option>
                    <?php foreach ($langues as $langue): ?>
                        <option value="<?php echo $langue['id_langue']; ?>">
                            <?php echo htmlspecialchars($langue['nom_langue']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="edit_paroles_quechua">Paroles en Quechua *</label>
                <textarea class="form-control" name="paroles_quechua" id="edit_paroles_quechua" rows="5" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_paroles_langue">Paroles dans la langue</label>
                <textarea class="form-control" name="paroles_langue" id="edit_paroles_langue" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="edit_nom_auteur">Nom de l’auteur</label>
                <input type="text" class="form-control" name="nom_auteur" id="edit_nom_auteur">
           </div>

           <div class="form-group">
               <label for="edit_nom_interprete">Nom de l’interprète</label>
               <input type="text" class="form-control" name="nom_interprete" id="edit_nom_interprete">
           </div>

           <div class="form-group">
               <label for="edit_nom_traducteur">Nom du traducteur</label>
               <input type="text" class="form-control" name="nom_traducteur" id="edit_nom_traducteur">
          </div>


            <div class="form-group">
                <label for="edit_audio">Nouveau fichier Audio </label>
                <input type="file" class="form-control" name="audio" accept="audio/*" ondblclick="this.value=''">   
                <div class="file-info">
                    <i class="fas fa-info-circle"></i> Laissez vide pour conserver le fichier actuel
                </div>
                <label>
                <input type="checkbox" name="delete_audio" value="1"> Supprimer le fichier audio existant
                </label>
            </div>
            
            <div class="form-group">
                <label for="edit_karaoke">Nouveau fichier Karaoké </label>
                <input type="file" class="form-control" name="karaoke" accept="audio/*,video/mp4" ondblclick="this.value=''">
                <div class="file-info">
                    <i class="fas fa-info-circle"></i> Laissez vide pour conserver le fichier actuel
                </div>
                <label>
                 <input type="checkbox" name="delete_karaoke" value="1"> Supprimer le fichier karaoke existant
                </label>
            </div>        

            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Modifier la chanson
            </button>
        </form>
    </div>
</div>
<style>
.close {
  position: absolute;
  top: 15px;
  right: 20px;
  font-size: 20px;
  cursor: pointer;
  color: #444;
}

.close:hover {
  color: #000;
}

</style>


<script>
// Données des chansons pour JavaScript
const chansonsData = <?php echo json_encode($chansons); ?>;

function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function editSong(id) {
    const chanson = chansonsData.find(c => c.id == id);
    if (chanson) {
        document.getElementById('edit_id').value = chanson.id;
        document.getElementById('edit_titre_quechua').value = chanson.titre_quechua || '';
        document.getElementById('edit_titre_langue').value = chanson.titre_langue || '';
        document.getElementById('edit_id_langue').value = chanson.id_langue || '';
        document.getElementById('edit_paroles_quechua').value = chanson.paroles_quechua || '';
        document.getElementById('edit_paroles_langue').value = chanson.paroles_langue || '';
        document.getElementById('edit_nom_auteur').value = chanson.nom_auteur || '';
        document.getElementById('edit_nom_interprete').value = chanson.nom_interprete || '';
        document.getElementById('edit_nom_traducteur').value = chanson.nom_traducteur || '';
       
        
        openModal('editModal');
    }
}

function deleteSong(id) {
    const chanson = chansonsData.find(c => c.id == id);
    if (chanson && confirm(`Êtes-vous sûr de vouloir supprimer la chanson "${chanson.titre_quechua}" ?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="supprimer">
            <input type="hidden" name="id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Fermer les modales en cliquant à l'extérieur
window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for (let i = 0; i < modals.length; i++) {
        if (event.target == modals[i]) {
            modals[i].style.display = 'none';
        }
    }
}

// Validation des fichiers côté client
document.addEventListener('DOMContentLoaded', function() {
    const audioInputs = document.querySelectorAll('input[name="audio"]');
    const karaokeInputs = document.querySelectorAll('input[name="karaoke"]');
    
    audioInputs.forEach(input => {
        input.addEventListener('change', function() {
            validateAudioFile(this);
        });
    });
    
    karaokeInputs.forEach(input => {
        input.addEventListener('change', function() {
            validateKaraokeFile(this);
        });
    });
});

function validateAudioFile(input) {
    const file = input.files[0];
    if (file) {
        const allowedTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg'];
        const allowedExtensions = ['mp3', 'wav', 'ogg'];
        const extension = file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(extension)) {
            alert('Format audio non autorisé. Utilisez MP3, WAV ou OGG.');
            input.value = '';
            return;
        }
        
        if (file.size > 50 * 1024 * 1024) {
            alert('Le fichier audio est trop volumineux (max 50MB).');
            input.value = '';
            return;
        }
    }
}

function validateKaraokeFile(input) {
    const file = input.files[0];
    if (file) {
        const allowedExtensions = ['mp3', 'wav', 'ogg', 'mp4'];
        const extension = file.name.split('.').pop().toLowerCase();
        
        if (!allowedExtensions.includes(extension)) {
            alert('Format karaoké non autorisé. Utilisez MP3, WAV, OGG ou MP4.');
            input.value = '';
            return;
        }
        
        if (file.size > 50 * 1024 * 1024) {
            alert('Le fichier karaoké est trop volumineux (max 50MB).');
            input.value = '';
            return;
        }
    }
}


</script>

<?php include 'includes/footer.php'; ?>