<?php

require_once 'config/database.php';

// Système de traduction
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'fr';
$allowed_langs = ['fr', 'es'];

if (!in_array($lang, $allowed_langs)) {
    $lang = 'fr';
}

// Charger le fichier de traduction
$translations = include "lang/{$lang}.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: liste_chansons.php?lang=' . $lang);
    exit;
}

$database = new Database();
$chanson = $database->getChansonWithLangueById($_GET['id']);

if (!$chanson) {
    header('Location: liste_chansons.php?lang=' . $lang);
    exit;
}

$page_title = htmlspecialchars($chanson['titre']) . $translations['song_page_title_suffix'];

include 'includes/header.php';
?>

<?php include 'includes/navigation.php'; ?>

<main>
    
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb justify-content-center bg-transparent">
                            <li class="breadcrumb-item">
                                <a href="index.php?lang=<?php echo $lang; ?>" class="text-white-50">
                                    <i class="fas fa-home"></i> <?php echo $translations['breadcrumb_home']; ?>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="liste_chansons.php?lang=<?php echo $lang; ?>" class="text-white-50">
                                    <i class="fas fa-music"></i> <?php echo $translations['breadcrumb_songs']; ?>
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-white" aria-current="page">
                                <?php echo htmlspecialchars($chanson['titre_quechua']); ?>
                            </li>
                        </ol>
                    </nav>
                    
                    <h1 class="display-4 mb-3">
                        <i class="fas fa-music"></i> 
                        <?php echo htmlspecialchars($chanson['titre_quechua']); ?>
                    </h1>
                    
                    <?php if (!empty($chanson['titre_langue'])): ?>
                        <h2 class="h5 text-muted fst-italic mb-3 text-force-white">
                            <?php echo htmlspecialchars($chanson['titre_langue']); ?>
                        </h2>
                    <?php endif; ?>

                    <?php if (!empty($chanson['nom_auteur']) || !empty($chanson['nom_interprete'])): ?>
                        <p class="text-white mb-2">
                          <?php if (!empty($chanson['nom_auteur'])): ?>
                              <i class="fas fa-pen"></i> <?php echo $translations['author_label']; ?> <strong><?php echo htmlspecialchars($chanson['nom_auteur']); ?></strong><br>
                          <?php endif; ?>
                          <?php if (!empty($chanson['nom_interprete'])): ?>
                              <i class="fas fa-microphone-alt"></i> <?php echo $translations['interpreter_label']; ?> <strong><?php echo htmlspecialchars($chanson['nom_interprete']); ?></strong>
                          <?php endif; ?>
                        </p>
                    <?php endif; ?>
                    
                </div>
            </div>
        </div>
    </section>

    
    <div class="container">
        
        <?php if (isset($chanson['audio']) && !empty($chanson['audio'])): ?>
            <section class="content-wrapper p-4 mb-4">
                <div class="text-center">
                    <h3 class="mb-4">
                        <i class="fas fa-play-circle text-primary"></i> 
                        <?php echo $translations['listen_song_title']; ?>
                    </h3>
                    <div class="audio-player bg-light p-4 rounded-3">
                        <audio controls class="w-100" style="max-width: 500px;">
                            <source src="media/audios/<?php echo htmlspecialchars($chanson['audio']); ?>" type="audio/mpeg">
                            <source src="media/audios/<?php echo htmlspecialchars($chanson['audio']); ?>" type="audio/wav">
                            <?php echo $translations['audio_not_supported']; ?>
                        </audio>
                    </div>
                </div>
                <div class="text-center mt-4">
                   <button class="btn btn-outline-danger" id="like-btn">
                     <i class="fas fa-heart"></i> <?php echo $translations['like_button']; ?> 
                     (<span id="like-count"><?php echo htmlspecialchars($chanson['likes']); ?></span>)
                    </button>
                </div>
            </section>
        <?php endif; ?>

        <!-- Paroles -->
        <section class="content-wrapper py-3 px-2">
            <div class="row g-3">
                <div class="col-md-6">
                    <h4 class="text-center text-primary">
                        <i class="fas fa-quote-left"></i> 
                        <?php echo $translations['quechua_lyrics_title']; ?>
                    </h4>
                    <div class="paroles-text bg-light p-3 rounded-2">
                        <?php echo nl2br(htmlspecialchars($chanson['paroles_quechua'])); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <h4 class="text-center text-success">
                        <i class="fas fa-language"></i> 
                        <?php echo $translations['lyrics_in']; ?> <?php echo htmlspecialchars($chanson['nom_langue']); ?>
                    </h4>
                    <div class="paroles-text bg-light p-3 rounded-2" style="font-style: italic;">
                        <?php echo nl2br(htmlspecialchars($chanson['paroles_langue'])); ?>
                    </div>
                </div>
            </div>
        </section>

       
        <?php if (isset($chanson['karaoke']) && !empty($chanson['karaoke'])): ?>
            <section class="content-wrapper p-4 mb-4">
                <div class="text-center">
                    <h3 class="mb-4">
                        <i class="fas fa-microphone text-danger"></i> 
                        <?php echo $translations['karaoke_video_title']; ?>
                    </h3>
                    <div class="video-player bg-light p-4 rounded-3">
                        <video controls class="w-100" style="max-width: 720px; border-radius: 12px;">
                            <source src="media/karaoke/<?php echo htmlspecialchars($chanson['karaoke']); ?>" type="video/mp4">
                            <?php echo $translations['video_not_supported']; ?>
                        </video>
                    </div>
                </div>
            </section>
        <?php endif; ?>

    </div>
</main>

<style>

.paroles-section {
    transition: transform 0.3s ease;
}

.paroles-section:hover {
    transform: translateY(-5px);
}

.paroles-content {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: box-shadow 0.3s ease;
}

.paroles-content:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.paroles-text {
   font-size: 0.95rem;
    line-height: 1.35;
    font-family: 'Georgia', serif;
    white-space: pre-line;
}

.text-force-white {
    color: #fff !important;
}

.paroles-text::-webkit-scrollbar {
    width: 8px;
}

.paroles-text::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.paroles-text::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.paroles-text::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.audio-player {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.video-player {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

audio {
    height: 50px;
    border-radius: 10px;
}

.breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,0.5);
}


.content-wrapper {
    animation: fadeInUp 0.6s ease-out;
}

.content-wrapper:nth-child(2) {
    animation-delay: 0.1s;
}

.content-wrapper:nth-child(3) {
    animation-delay: 0.2s;
}

.content-wrapper:nth-child(4) {
    animation-delay: 0.3s;
}


.badge {
    font-size: 0.75rem;
}

.badge i {
    margin-right: 0.3rem;
}

</style>

<script>
// Gestion du lecteur audio
document.addEventListener('DOMContentLoaded', function() {
    const audio = document.querySelector('audio');
    if (audio) {
        audio.addEventListener('loadstart', function() {
            console.log('Chargement de l\'audio...');
        });
        
        audio.addEventListener('error', function() {
            console.log('Erreur de chargement audio');
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const likeBtn = document.getElementById("like-btn");
    const likeCount = document.getElementById("like-count");

    if (likeBtn) {
        likeBtn.addEventListener("click", function () {
            fetch("like_chanson.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: "id=<?php echo $chanson['id_chanson']; ?>"
            })
            .then(response => response.text())
            .then(data => {
                likeCount.textContent = data;
                likeBtn.disabled = true;
                likeBtn.innerHTML = `<i class="fas fa-heart"></i> <?php echo $translations['like_thanks']; ?> (<span id="like-count">${data}</span>)`;
            })
            .catch(error => {
                console.error("Erreur:", error);
            });
        });
    }
});

</script>

<?php include 'includes/footer.php'; ?>