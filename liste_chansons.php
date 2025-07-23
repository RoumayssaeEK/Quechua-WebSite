<?php 

require_once 'config/database.php';

$database = new Database();

$limit = 9;

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;

$offset = ($page - 1) * $limit;

$totalChansons = $database->getTotalChansons();
$chansons = $database->getChansonsWithLanguePaginated($limit, $offset);
$totalPages = ceil($totalChansons / $limit);

$search = $_GET['search'] ?? null;

if ($search) {
    $chansons = $database->searchChansonsByTitre($search);
    $totalChansons = count($chansons);
    $totalPages = 1; 
} else {
    $totalChansons = $database->getTotalChansons();
    $chansons = $database->getChansonsWithLanguePaginated($limit, $offset);
    $totalPages = ceil($totalChansons / $limit);
}

$page_title = "Liste des Chansons - Ma chanson en quechua";

include 'includes/header.php';
?>

<?php include 'includes/navigation.php'; ?>

<main>
    <section class="hero-section">
        <div class="container">
            <h1><i class="fas fa-music"></i> Collection de Chansons</h1>
            <p class="lead">Découvrez notre collection multilingue avec traductions en quechua</p>
            
            <div class="mt-4">
                <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                    <i class="fas fa-compact-disc"></i> 
                    <?php echo count($chansons); ?> chansons disponibles
                </span>
            </div>
            
            <!-- Formulaire de recherche et dropdown interprètes -->
            <form method="get" action="" class="mt-4">
                <div class="row justify-content-center align-items-end g-3">
                    <!-- Champ de recherche -->
                    <div class="col-lg-6 col-md-7">
                        <div class="input-group input-group-lg">
                            <input 
                                type="text" 
                                class="form-control" 
                                name="search" 
                                placeholder="Rechercher un titre..." 
                                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                            >
                            <button class="btn btn-warning" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            
        </div>  
    </section>    
    
    <div class="container">
        <section class="content-wrapper p-5">
            <?php if (empty($chansons)): ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-music text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="text-muted">Aucune chanson disponible</h3>
                    <p class="text-muted">La collection sera bientôt enrichie avec des chansons de différentes langues et leurs traductions.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($chansons as $chanson): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100 shadow-sm border-0 chanson-card">
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
                                        <a href="chanson.php?id=<?php echo $chanson['id']; ?>" 
                                           class="btn btn-primary w-100 btn-lg">
                                            <i class="fas fa-headphones"></i> 
                                            Écouter la chanson
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Pagination -->
            <nav aria-label="Pagination">
                <ul class="pagination justify-content-center mt-4">
                    <?php $searchQuery = isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''; ?>

                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo max(1, $page - 1) . $searchQuery; ?>" aria-label="Page précédente">
                            &laquo;
                        </a>
                    </li>
                    
                    <?php
                    $range = 2; 
                    $start = max(1, $page - $range);
                    $end = min($totalPages, $page + $range);

                    if ($start > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?page=1' . $searchQuery . '">1</a></li>';
                        if ($start > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        echo '<li class="page-item">';
                        echo '<a class="page-link" href="?page=' . $i . $searchQuery . '" style="' . ($i == $page ? 'font-weight:bold; background-color:transparent; border:none;' : '') . '">' . $i . '</a>';
                        echo '</li>';
                    }

                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . $searchQuery . '">' . $totalPages . '</a></li>';
                    }
                    ?>
                    
                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo min($totalPages, $page + 1) . $searchQuery; ?>" aria-label="Page suivante">
                            &raquo;
                        </a>
                    </li>   
                </ul>
            </nav>
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

.chanson-card .card-img-top {
    transition: all 0.3s ease;
}

.chanson-card:hover .card-img-top {
    transform: scale(1.05);
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

/* Styles pour les badges */
.badge {
    font-size: 0.75rem;
}

.badge i {
    margin-right: 0.3rem;
}

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

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
   
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
    
    .hero-section .lead {
        font-size: 1rem;
    }
}

/* Amélioration générale de l'interface */
.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Style pour la section content-wrapper */
.content-wrapper {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
}
</style>

<?php include 'includes/footer.php'; ?>