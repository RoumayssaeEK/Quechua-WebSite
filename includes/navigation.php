<nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php?lang=<?php echo $lang; ?>">
            <i class="fas fa-feather"></i> <?php echo $translations['navbar_brand']; ?>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php?lang=<?php echo $lang; ?>">
                        <i class="fas fa-home"></i> <?php echo $translations['nav_home']; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'liste_chansons.php') ? 'active' : ''; ?>" href="liste_chansons.php?lang=<?php echo $lang; ?>">
                        <i class="fas fa-music"></i> <?php echo $translations['nav_songs']; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'liste_interpretes.php') ? 'active' : ''; ?>" href="liste_interpretes.php?lang=<?php echo $lang; ?>">
                       <i class="fas fa-users"></i> <?php echo $translations['nav_interpreters']; ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="contact.php?lang=<?php echo $lang; ?>">
                        <i class="fas fa-envelope"></i> <?php echo $translations['nav_contact']; ?>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link <?php echo ($lang === 'fr') ? 'active-lang' : ''; ?>" href="?lang=fr" title="<?php echo $translations['lang_french_title']; ?>">
                    <img src="media/images/fr.png" alt="<?php echo $translations['lang_french_title']; ?>" width="24">
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($lang === 'es') ? 'active-lang' : ''; ?>" href="?lang=es" title="<?php echo $translations['lang_spanish_title']; ?>">
                    <img src="media/images/es.png" alt="<?php echo $translations['lang_spanish_title']; ?>" width="24">
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

.nav-link.active-lang {
    background-color: rgba(230, 126, 34, 0.2);
    border-radius: 5px;
}

.nav-link.active-lang img {
    border: 2px solid #e67e22;
}
</style>