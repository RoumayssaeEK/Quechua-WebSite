<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    // Si la chanson a déjà été likée dans cette session, on bloque
    if (!isset($_SESSION['liked']) || !in_array($id, $_SESSION['liked'])) {
        $db = new Database();
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare("UPDATE chansons SET likes = likes + 1 WHERE id = ?");
        $stmt->execute([$id]);

        // On stocke que l’utilisateur a liké cette chanson
        $_SESSION['liked'][] = $id;
    }

    // Retourner le nombre de likes mis à jour
    $stmt = $pdo->prepare("SELECT likes FROM chansons WHERE id = ?");
    $stmt->execute([$id]);
    echo $stmt->fetchColumn();
}
?>
