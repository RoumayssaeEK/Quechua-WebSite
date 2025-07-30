<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $sujet = htmlspecialchars(trim($_POST['sujet']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    
    if (empty($nom) || empty($prenom) || empty($email) || empty($sujet) || empty($message)) {
        header('Location: contact.php?error=champs_manquants');
        exit;
    }
    
   
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: contact.php?error=email_invalide');
        exit;
    }
    
    
    $sujets = [
        'question_generale' => 'Question générale',
        'suggestion_chanson' => 'Suggestion de chanson',
        'probleme_technique' => 'Problème technique',
        'partenariat' => 'Partenariat',
        'autre' => 'Autre'
    ];
    $sujet_texte = isset($sujets[$sujet]) ? $sujets[$sujet] : 'Autre';
    
    
    $destinataire = 'contact@quechua-chante.com';
    $objet = 'Nouveau message depuis Ma chanson en quechua - ' . $sujet_texte;
    
    $contenu = "Nouveau message reçu depuis le site Ma chanson en quechua\n\n";
    $contenu .= "Nom: " . $nom . "\n";
    $contenu .= "Prénom: " . $prenom . "\n";
    $contenu .= "Email: " . $email . "\n";
    $contenu .= "Sujet: " . $sujet_texte . "\n";
    $contenu .= "Date: " . date('d/m/Y à H:i') . "\n\n";
    $contenu .= "Message:\n" . $message . "\n\n";
    $contenu .= "---\n";
    $contenu .= "Ce message a été envoyé depuis le formulaire de contact de Ma chanson en quechua";
    
   
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    
    if (mail($destinataire, $objet, $contenu, $headers)) {
        
        header('Location: contact.php?success=1');
    } else {
        
        header('Location: contact.php?error=envoi_echoue');
    }
    
} else {
   
    header('Location: contact.php');
}
exit;
?>