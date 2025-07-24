<?php
session_start();
require_once 'config/database.php';

if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    
    if (!isset($_SESSION['liked']) || !in_array($id, $_SESSION['liked'])) {
        $db = new Database();
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare("UPDATE chansons SET likes = likes + 1 WHERE id = ?");
        $stmt->execute([$id]);

        
        $_SESSION['liked'][] = $id;
    }

    
    $stmt = $pdo->prepare("SELECT likes FROM chansons WHERE id = ?");
    $stmt->execute([$id]);
    echo $stmt->fetchColumn();
}
?>
