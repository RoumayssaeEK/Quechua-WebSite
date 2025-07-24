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

try {
    $database = new Database();
    $idInterprete = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    // Validation de l'ID
    if ($idInterprete <= 0) {
        header('Location: liste_interpretes.php?lang=' . $lang);
        exit;
    }

    // Récupérer les infos de l'interprète
    $interprete = $database->getInterpreteById($idInterprete);

    // Si l'interprète n'existe pas, rediriger
    if (!$interprete) {
        header('Location: liste_interpretes.php?lang=' . $lang);
        exit;
    }

    // Récupérer les chansons associées à cet interprète
    $chansons = $database->getChansonsByInterprete($idInterprete);
    
    // Compter le nombre total de chansons
    $totalChansons = count($chansons);

    $page_title = $translations['interpreter_page_title_prefix'] . htmlspecialchars($interprete['nom']) . $translations['interpreter_page_title_suffix'];
    include 'includes/header.php';
    include 'includes/navigation.php';

} catch (Exception $e) {
    error_log("Erreur page interprète: " . $e->getMessage());
    header('Location: liste_interpretes.php?lang=' . $lang);
    exit;
}
?>

<main>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-10 mx-auto">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb justify-content-center bg-transparent">
                            <li class="breadcrumb-item">
                                <a href="index.php?lang=<?php echo $lang; ?>" class="text-white-50">
                                    <i class="fas fa-home"></i> <?php echo $translations['breadcrumb_home']; ?>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="liste_interpretes.php?lang=<?php echo $lang; ?>" class="text-white-50">
                                    <i class="fas fa-users"></i> <?php echo $translations['nav_interpreters']; ?>
                                </a>
                            </li>
                            <li class="breadcrumb-item active text-white" aria-current="page">
                                <?php echo htmlspecialchars($interprete['nom']); ?>
                            </li>
                        </ol>
                    </nav>
                    
                    <div class="text-center">
                        <div class="interprete-avatar mb-3">
                            <div class="avatar-circle-large mx-auto">
                                <i class="fas fa-microphone-alt"></i>
                            </div>
                        </div>
                        
                        <h1 class="display-5 mb-2 text-white">
                            <?php echo htmlspecialchars($interprete['nom']); ?>
                        </h1>
                        
                        <div class="mt-4">
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                <i class="fas fa-music"></i> 
                                <?php echo $totalChansons; ?> 
                                <?php echo $totalChansons > 1 ? $translations['songs_plural'] : $translations['song_singular']; ?>
                                <?php echo $totalChansons > 1 ? $translations['interpreted_plural'] : $translations['interpreted_singular']; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenu principal -->
    <div class="container">
        
        <!-- Biographie si disponible -->
        <?php if (!empty($interprete['biographie'])): ?>
            <section class="content-wrapper p-4 mb-4">
                <div class="biographie-section">
                    <h3 class="section-title mb-4">
                        <i class="fas fa-quote-left text-primary me-2"></i>
                        <?php echo $translations['about_title']; ?>
                    </h3>
                    <div class="biographie-content">
                        <p class="lead"><?php echo nl2br(htmlspecialchars($interprete['biographie'])); ?></p>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Liste des chansons -->
        <section class="content-wrapper p-5">
            <div class="section-header mb-4">
                <h3 class="section-title">
                    <i class="fas fa-music text-primary me-2"></i>
                    <?php echo $translations['songs_by']; ?> <?php echo htmlspecialchars($interprete['nom']); ?>
                </h3>
            </div>

            <?php if (empty($chansons)): ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-music text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-muted"><?php echo $translations['no_songs_available_title']; ?></h3>
                    <p class="text-muted">
                        <?php echo $translations['no_songs_interpreter_text']; ?>
                    </p>
                    <a href="liste_interpretes.php?lang=<?php echo $lang; ?>" class="btn btn-primary mt-3">
                        <i class="fas fa-arrow-left me-2"></i>
                        <?php echo $translations['back_to_interpreters']; ?>
                    </a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($chansons as $index => $chanson): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm border-0 chanson-card" style="animation-delay: <?php echo ($index * 0.1); ?>s;">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-dark mb-1 fw-bold">
                                        <?php echo htmlspecialchars($chanson['titre_quechua']); ?>
                                    </h5>
                                    
                                    <?php if (!empty($chanson['titre_langue'])): ?>
                                        <h6 class="card-subtitle text-muted mb-2 fst-italic">
                                            <?php echo htmlspecialchars($chanson['titre_langue']); ?>
                                        </h6>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($chanson['nom_langue'])): ?>
                                        <div class="mb-3">
                                            <span class="badge bg-info text-white">
                                                <i class="fas fa-language"></i> 
                                                <?php echo htmlspecialchars($chanson['nom_langue']); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-auto">
                                        <a href="chanson.php?id=<?php echo $chanson['id']; ?>&lang=<?php echo $lang; ?>" 
                                           class="btn btn-primary w-100 btn-lg">
                                            <i class="fas fa-headphones"></i> 
                                            <?php echo $translations['listen_song']; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>
</main>


<style>
/* Styles pour les cartes de chansons */
.chanson-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
    animation: fadeInUp 0.6s ease-out;
}

.chanson-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
}

.chanson-card .btn-primary {
    background: linear-gradient(45deg, #f39c12, #e67e22);
    border: none;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.chanson-card .btn-primary:hover {
    background: linear-gradient(45deg, #e67e22, #d35400);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(243, 156, 18, 0.4);
}

.breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,0.5);
}

/* Navigation links avec couleur de survol corrigée */
.breadcrumb-item a.text-white-50 {
    color: rgba(255,255,255,0.7) !important;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb-item a.text-white-50:hover {
    color: rgba(255,255,255,1) !important;
}

/* S'assurer que les liens soient cliquables */
.breadcrumb-item a {
    pointer-events: auto;
    cursor: pointer;
}

/* Avatar de l'interprète adapté au style du site */
.avatar-circle-large {
    width: 100px;
    height: 100px;
    background: linear-gradient(45deg, #fff, #f8f9fa);
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #f39c12;
    font-size: 2.5rem;
    box-shadow: 0 8px 32px rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
}

/* Content wrappers avec style unifié */
.content-wrapper {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

/* Sections */
.section-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.section-header {
    text-align: center;
}

/* Biographie avec style cohérent */
.biographie-content {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 8px;
    border-left: 4px solid #f39c12;
}

.biographie-content .lead {
    color: #555;
    line-height: 1.8;
    margin-bottom: 0;
}

/* Couleur primaire adaptée au thème */
.text-primary {
    color: #f39c12 !important;
}

.btn-primary {
    background: linear-gradient(45deg, #f39c12, #e67e22);
    border: none;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #e67e22, #d35400);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(243, 156, 18, 0.4);
}

/* Card styling cohérent */
.card {
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
}

.card-title {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1.1rem;
    line-height: 1.3;
}

.card-subtitle {
    font-size: 0.9rem;
}

/* Badge styling cohérent */
.badge {
    font-size: 0.75rem;
}

.badge i {
    margin-right: 0.3rem;
}

/* Animations décalées pour les cartes */
.chanson-card:nth-child(1) { animation-delay: 0s; }
.chanson-card:nth-child(2) { animation-delay: 0.1s; }
.chanson-card:nth-child(3) { animation-delay: 0.2s; }
.chanson-card:nth-child(4) { animation-delay: 0.3s; }
.chanson-card:nth-child(5) { animation-delay: 0.4s; }
.chanson-card:nth-child(6) { animation-delay: 0.5s; }
.chanson-card:nth-child(7) { animation-delay: 0.6s; }
.chanson-card:nth-child(8) { animation-delay: 0.7s; }
.chanson-card:nth-child(9) { animation-delay: 0.8s; }

/* Animation keyframes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Effet de survol subtil pour les cartes */
.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(243, 156, 18, 0.05), rgba(230, 126, 34, 0.05));
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 15px;
}

.card:hover::before {
    opacity: 1;
}

.card-body {
    position: relative;
    z-index: 1;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-section {
        padding: 3rem 0;
    }
    
    .display-5 {
        font-size: 1.8rem;
    }
    
    .avatar-circle-large {
        width: 80px;
        height: 80px;
        font-size: 2rem;
    }
    
    .chanson-card:hover {
        transform: translateY(-5px);
    }
}

@media (max-width: 576px) {
    .content-wrapper {
        padding: 2rem 1rem !important;
    }
    
    .hero-section h1 {
        font-size: 2rem;
    }
    
    .biographie-content {
        padding: 1.5rem;
    }
    
    .btn-lg {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }
}

/* Amélioration générale de l'interface - couleurs chaudes */
.bg-gradient {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
}
</style>

<?php include 'includes/footer.php'; ?>