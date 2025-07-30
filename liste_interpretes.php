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
$interpretes = $database->getAllInterpretes();

$page_title = $translations['interpreters_page_title'];
include 'includes/header.php';
?>

<?php include 'includes/navigation.php'; ?>

<main>
    <!-- Hero Section -->
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
                            <li class="breadcrumb-item active text-white" aria-current="page">
                                <i class="fas fa-users"></i> <?php echo $translations['nav_interpreters']; ?>
                            </li>
                        </ol>
                    </nav>
                    
                    <h1><i class="fas fa-users"></i> <?php echo $translations['interpreters_title']; ?></h1>
                    <p class="lead"><?php echo $translations['interpreters_subtitle']; ?></p>
                    
                    <div class="mt-4">
                        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                            <i class="fas fa-microphone"></i> 
                            <?php echo count($interpretes); ?> <?php echo $translations['talented_interpreters']; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section des interprètes -->
    <div class="container">
        <section class="content-wrapper p-5">
            <?php if (empty($interpretes)): ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-users text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-muted"><?php echo $translations['no_interpreters_title']; ?></h3>
                    <p class="text-muted"><?php echo $translations['no_interpreters_text']; ?></p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($interpretes as $index => $interprete): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm border-0 interprete-card" style="animation-delay: <?php echo ($index * 0.1); ?>s;">
                                <div class="card-body d-flex flex-column text-center p-4">
                                    <div class="interprete-avatar mb-3">
                                        <div class="avatar-circle mx-auto">
                                            <i class="fas fa-user-circle"></i>
                                        </div>
                                    </div>
                                    
                                    <h5 class="card-title text-dark mb-3 fw-bold">
                                        <?php echo htmlspecialchars($interprete['nom']); ?>
                                    </h5>
                                    
                                    <p class="card-text text-muted mb-4 flex-grow-1">
                                        <?php echo $translations['discover_songs_by']; ?> 
                                        <strong><?php echo htmlspecialchars($interprete['nom']); ?></strong>
                                        <?php echo $translations['in_our_collection']; ?>
                                    </p>
                                    
                                    <div class="mt-auto">
                                        <a href="interprete.php?id=<?php echo $interprete['id_interprete']; ?>&lang=<?php echo $lang; ?>" 
                                           class="btn btn-primary w-100 btn-lg">
                                            <i class="fas fa-music me-2"></i>
                                            <?php echo $translations['view_songs']; ?>
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
/* Styles pour les cartes d'interprètes */
.interprete-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
    animation: fadeInUp 0.6s ease-out;
}

.interprete-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
}

.interprete-avatar {
    position: relative;
}

.avatar-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, #f39c12, #e67e22);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2.5rem;
    box-shadow: 0 4px 15px rgba(243, 156, 18, 0.3);
    transition: all 0.3s ease;
}

.interprete-card:hover .avatar-circle {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(243, 156, 18, 0.4);
}

.card-title {
    font-weight: 600;
    font-size: 1.3rem;
    color: #2c3e50;
}

.card-text {
    font-size: 0.95rem;
    line-height: 1.6;
}

.btn-primary {
    background: linear-gradient(45deg, #f39c12, #e67e22);
    border: none;
    border-radius: 10px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #e67e22, #d35400);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(243, 156, 18, 0.4);
}

.btn-primary:active {
    transform: translateY(0);
}


.interprete-card:nth-child(1) { animation-delay: 0s; }
.interprete-card:nth-child(2) { animation-delay: 0.1s; }
.interprete-card:nth-child(3) { animation-delay: 0.2s; }
.interprete-card:nth-child(4) { animation-delay: 0.3s; }
.interprete-card:nth-child(5) { animation-delay: 0.4s; }
.interprete-card:nth-child(6) { animation-delay: 0.5s; }
.interprete-card:nth-child(7) { animation-delay: 0.6s; }
.interprete-card:nth-child(8) { animation-delay: 0.7s; }
.interprete-card:nth-child(9) { animation-delay: 0.8s; }


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


.breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,0.5);
}

.text-white-50 {
    color: rgba(255,255,255,0.7) !important;
}

.text-white-50:hover {
    color: rgba(255,255,255,1) !important;
}


.content-wrapper {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
}


.badge {
    font-size: 0.75rem;
}

.badge i {
    margin-right: 0.3rem;
}


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


@media (max-width: 768px) {
    .interprete-card:hover {
        transform: translateY(-5px);
    }
    
    .avatar-circle {
        width: 60px;
        height: 60px;
        font-size: 2rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
}

@media (max-width: 576px) {
    .content-wrapper {
        padding: 2rem 1rem !important;
    }
    
    .hero-section h1 {
        font-size: 2rem;
    }
    
    .hero-section .lead {
        font-size: 1rem;
    }
    
    .btn-lg {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
    }
}


.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<?php include 'includes/footer.php'; ?>