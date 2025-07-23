<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-feather"></i> Ma chanson en quechua
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-home"></i> Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'liste_chansons.php') ? 'active' : ''; ?>" href="liste_chansons.php">
                        <i class="fas fa-music"></i> Chansons
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'liste_interpretes.php') ? 'active' : ''; ?>" href="liste_interpretes.php">
                       <i class="fas fa-users"></i> Interprètes
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="contact.php">
                        <i class="fas fa-envelope"></i> Contact
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="?lang=fr" title="Français">
                    <img src="media/images/fr.png" alt="Français" width="24">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?lang=es" title="Español">
                    <img src="media/images/es.png" alt="Espagnol" width="24">
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<style>
   .navbar-nav .nav-item img {
    border-radius: 3px;
    margin-left: 5px;
}

</style>
