<?php

require_once 'config/database.php';

// Système de traduction
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'fr'; 
$allowed_langs = ['fr', 'es']; 


if (!in_array($lang, $allowed_langs)) {
    $lang = 'fr';
}


$translations = include "lang/{$lang}.php";

$database = new Database();
$total_chansons = $database->getTotalChansons();

$page_title = $translations['page_title'];

include 'includes/header.php';
?>

<?php include 'includes/navigation.php'; ?>

<main>
    
    <section class="hero-section">
        <div class="container">
            <h1><?php echo $translations['hero_title']; ?></h1>
            <p class="lead"><?php echo $translations['hero_lead']; ?></p>
            <a href="liste_chansons.php?lang=<?php echo $lang; ?>" class="cta-button">
                <i class="fas fa-play"></i> <?php echo $translations['cta_button']; ?>
            </a>
        </div>
    </section>

    
    <div class="container">
        <section class="content-wrapper p-5">
             <h2 class="text-center mb-5 display-5 text-dark"><?php echo $translations['unique_experience_title']; ?></h2>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card p-4 h-100 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-language"></i>
                        </div>
                        <h3 class="h4 mb-3 text-dark"><?php echo $translations['feature_1_title']; ?></h3>
                        <p class="text-muted"><?php echo $translations['feature_1_text']; ?></p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card p-4 h-100 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-headphones"></i>
                        </div>
                        <h3 class="h4 mb-3 text-dark"><?php echo $translations['feature_2_title']; ?></h3>
                        <p class="text-muted"><?php echo $translations['feature_2_text']; ?></p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card p-4 h-100 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-microphone"></i>
                        </div>
                        <h3 class="h4 mb-3 text-dark"><?php echo $translations['feature_3_title']; ?></h3>
                        <p class="text-muted"><?php echo $translations['feature_3_text']; ?></p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="container">
        <section class="stats-section p-5 text-center">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="p-3">
                        <span class="stat-number d-block"><?php echo $total_chansons; ?></span>
                        <p class="mb-0"><?php echo $translations['stats_chansons']; ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="p-3">
                        <span class="stat-number d-block">500</span>
                        <p class="mb-0"><?php echo $translations['stats_history']; ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="p-3">
                        <span class="stat-number d-block">10</span>
                        <p class="mb-0"><?php echo $translations['stats_speakers']; ?></p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="p-3">
                        <span class="stat-number d-block">3</span>
                        <p class="mb-0"><?php echo $translations['stats_countries']; ?></p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    
    <div class="container">
        <section class="content-wrapper p-5">
            <h2 class="text-center mb-4 display-5 text-dark"><?php echo $translations['why_title']; ?></h2>
            <div class="text-center mx-auto" style="max-width: 800px;">
                <p class="lead text-muted mb-4">
                    <?php echo $translations['why_text']; ?>
                </p>
                <div class="row g-4 mt-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="p-4 bg-light rounded">
                            <h4 class="h5 mb-3" style="color: #e67e22;"><?php echo $translations['heritage_title']; ?></h4>
                            <p class="mb-0 text-muted"><?php echo $translations['heritage_text']; ?></p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="p-4 bg-light rounded">
                            <h4 class="h5 mb-3" style="color: #e67e22;"><?php echo $translations['art_title']; ?></h4>
                            <p class="mb-0 text-muted"><?php echo $translations['art_text']; ?></p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="p-4 bg-light rounded">
                            <h4 class="h5 mb-3" style="color: #e67e22;"><?php echo $translations['wisdom_title']; ?></h4>
                            <p class="mb-0 text-muted"><?php echo $translations['wisdom_text']; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    
   <div class="container my-5">
    <h2 class="text-center mb-4 display-5" style="color: #f8f9fa;"><?php echo $translations['visual_journey_title']; ?></h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="image-card">
                <img src="media/images/inca.jpeg" alt="Inca" class="img-fluid rounded">
                <div class="image-caption text-center mt-2">
                    <strong style="color:#f8f9fa; font-size:1.2rem;"><?php echo $translations['inca_title']; ?></strong>
                    <p style="color:#f8f9fa; font-size:1rem;"><?php echo $translations['inca_text']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="image-card">
                <img src="media/images/machu pichu.jpg" alt="Machu Picchu" class="img-fluid rounded">
                <div class="image-caption text-center mt-2">
                    <strong style="color:#f8f9fa; font-size:1.2rem;"><?php echo $translations['machu_title']; ?></strong>
                    <p style="color:#f8f9fa; font-size:1rem;"><?php echo $translations['machu_text']; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="image-card">
                <img src="media/images/perou.jpg" alt="Culture Quechua" class="img-fluid rounded">
                <div class="image-caption text-center mt-2">
                    <strong style="color:#f8f9fa; font-size:1.2rem;"><?php echo $translations['quechua_life_title']; ?></strong>
                    <p style="color:#f8f9fa; font-size:1rem;"><?php echo $translations['quechua_life_text']; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

</main>

<style>
   .image-card img {
    height: 230px;
    width: 100%;
    object-fit: cover;
}

.image-caption strong {
    color: #f8f9fa;
    font-size: 1.2rem;
}

.image-caption p {
    color: #f8f9fa;
    font-size: 1rem;
}

/* Style pour le sélecteur de langue */
.lang-selector {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
}

.lang-selector a {
    display: inline-block;
    padding: 5px 10px;
    margin: 0 2px;
    background: rgba(255,255,255,0.9);
    color: #333;
    text-decoration: none;
    border-radius: 3px;
    font-weight: bold;
}

.lang-selector a.active {
    background: #e67e22;
    color: white;
}

.lang-selector a:hover {
    background: #d35400;
    color: white;
}
</style>

<!-- Sélecteur de langue -->
<div class="lang-selector">
    <a href="?lang=fr" class="<?php echo $lang === 'fr' ? 'active' : ''; ?>">FR</a>
    <a href="?lang=es" class="<?php echo $lang === 'es' ? 'active' : ''; ?>">ES</a>
</div>

<?php include 'includes/footer.php'; ?>