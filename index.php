<?php

require_once 'config/database.php';


$database = new Database();
$total_chansons = $database->getTotalChansons();


$page_title = "Ma chanson en quechua";

include 'includes/header.php';
?>

<?php include 'includes/navigation.php'; ?>

<main>
    
    <section class="hero-section">
        <div class="container">
            <h1>Ma chanson en quechua</h1>
            <p class="lead">J'apprends le quechua en chantant</p>
            <a href="liste_chansons.php" class="cta-button">
                <i class="fas fa-play"></i> Découvrir les chansons
            </a>
        </div>
    </section>

    
    <div class="container">
        <section class="content-wrapper p-5">
            <h2 class="text-center mb-5 display-5 text-dark">Une expérience culturelle unique</h2>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card p-4 h-100 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-language"></i>
                        </div>
                        <h3 class="h4 mb-3 text-dark">Paroles bilingues</h3>
                        <p class="text-muted">Des paroles qui voyagent entre le quechua et d'autres langues, en toute harmonie.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card p-4 h-100 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-headphones"></i>
                        </div>
                        <h3 class="h4 mb-3 text-dark">Audio authentique</h3>
                        <p class="text-muted">Une immersion sonore à travers des interprétations diverses de la culture andine.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6">
                    <div class="feature-card p-4 h-100 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-microphone"></i>
                        </div>
                        <h3 class="h4 mb-3 text-dark">Mode karaoké</h3>
                        <p class="text-muted">Apprenez à chanter en quechua grâce à nos pistes karaoké et perfectionnez votre prononciation de cette langue millénaire.</p>
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
                        <p class="mb-0">Chansons</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="p-3">
                        <span class="stat-number d-block">500</span>
                        <p class="mb-0">Ans d'histoire musicale</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="p-3">
                        <span class="stat-number d-block">10</span>
                        <p class="mb-0">Millions locuteurs quechua dans le monde</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="p-3">
                        <span class="stat-number d-block">3</span>
                        <p class="mb-0">Pays des Andes représentés</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    
    <div class="container">
        <section class="content-wrapper p-5">
            <h2 class="text-center mb-4 display-5 text-dark">Pourquoi le quechua ?</h2>
            <div class="text-center mx-auto" style="max-width: 800px;">
                <p class="lead text-muted mb-4">
                    Le quechua, parlé par plus de 10 millions de personnes à travers les Andes, est bien plus qu'une langue : c'est un pont vers une civilisation ancestrale riche en sagesse, en poésie et en harmonie avec la nature.
                </p>
                <div class="row g-4 mt-4">
                    <div class="col-lg-4 col-md-6">
                        <div class="p-4 bg-light rounded">
                            <h4 class="h5 mb-3" style="color: #e67e22;"> Patrimoine vivant</h4>
                            <p class="mb-0 text-muted">Une tradition orale millénaire transmise de génération en génération</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="p-4 bg-light rounded">
                            <h4 class="h5 mb-3" style="color: #e67e22;"> Expression artistique</h4>
                            <p class="mb-0 text-muted">Des mélodies qui racontent l'histoire des peuples des Andes</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="p-4 bg-light rounded">
                            <h4 class="h5 mb-3" style="color: #e67e22;"> Sagesse ancestrale</h4>
                            <p class="mb-0 text-muted">Une philosophie de vie en harmonie avec la Pachamama (Terre-Mère)</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    
   <div class="container my-5">
    <h2 class="text-center mb-4 display-5" style="color: #f8f9fa;">Voyage visuel dans les Andes</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="image-card">
                <img src="media/images/inca.jpeg" alt="Inca" class="img-fluid rounded">
                <div class="image-caption text-center mt-2">
                    <strong style="color:#f8f9fa; font-size:1.2rem;">Empires Incas</strong>
                    <p style="color:#f8f9fa; font-size:1rem;">Plongez dans la grandeur d'une civilisation mythique.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="image-card">
                <img src="media/images/machu pichu.jpg" alt="Machu Picchu" class="img-fluid rounded">
                <div class="image-caption text-center mt-2">
                    <strong style="color:#f8f9fa; font-size:1.2rem;">Machu Picchu</strong>
                    <p style="color:#f8f9fa; font-size:1rem;">Le joyau architectural caché dans les sommets andins.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="image-card">
                <img src="media/images/perou.jpg" alt="Culture Quechua" class="img-fluid rounded">
                <div class="image-caption text-center mt-2">
                    <strong style="color:#f8f9fa; font-size:1.2rem;">Vie quechua</strong>
                    <p style="color:#f8f9fa; font-size:1rem;">Couleurs, musique et traditions des peuples andins.</p>
                </div>
            </div>
        </div>
    </div>
</div>


</main>
<style>
   .image-card img {
    height: 230px;
    width: 100%; /* Ajouté pour uniformiser */
    object-fit: cover;
}

/* Si tu veux, tu peux aussi styliser le texte dans CSS plutôt qu'en inline */
.image-caption strong {
    color: #f8f9fa;
    font-size: 1.2rem;
}

.image-caption p {
    color: #f8f9fa;
    font-size: 1rem;
}


</style>

<?php include 'includes/footer.php'; ?>