<?php

// Système de traduction
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'fr';
$allowed_langs = ['fr', 'es'];

if (!in_array($lang, $allowed_langs)) {
    $lang = 'fr';
}


$translations = include "lang/{$lang}.php";


$page_title = "Contact - Ma chanson en quechua";
include 'includes/header.php';
?>

<?php include 'includes/navigation.php'; ?>

<main>
    
    <section class="hero-section">
        <div class="container">
            <h1><?php echo $translations['contact_us']; ?></h1>
            <p class="lead"><?php echo $translations['contact_subtitle']; ?></p>
        </div>
    </section>

    <!-- Formulaire de contact -->
    <div class="container">
        <section class="content-wrapper p-5">
            <div class="row g-4">
                <div class="col-lg-8 mx-auto">
                    <div class="feature-card p-4">
                        <h2 class="text-center mb-4 display-5 text-dark"><?php echo $translations['send_message']; ?></h2>
                        
                        <form action="traitement_contact.php" method="POST" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nom" class="form-label"><?php echo $translations['last_name']; ?> *</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required>
                                    <div class="invalid-feedback">
                                        <?php echo $translations['please_enter_last_name']; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="prenom" class="form-label"><?php echo $translations['first_name']; ?> *</label>
                                    <input type="text" class="form-control" id="prenom" name="prenom" required>
                                    <div class="invalid-feedback">
                                        <?php echo $translations['please_enter_first_name']; ?>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <label for="email" class="form-label"><?php echo $translations['email']; ?> *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                    <div class="invalid-feedback">
                                        <?php echo $translations['please_enter_valid_email']; ?>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <label for="sujet" class="form-label"><?php echo $translations['subject']; ?> *</label>
                                    <select class="form-select" id="sujet" name="sujet" required>
                                        <option value=""><?php echo $translations['choose_subject']; ?></option>
                                        <option value="question_generale"><?php echo $translations['general_question']; ?></option>
                                        <option value="suggestion_chanson"><?php echo $translations['song_suggestion']; ?></option>
                                        <option value="probleme_technique"><?php echo $translations['technical_problem']; ?></option>
                                        <option value="partenariat"><?php echo $translations['partnership']; ?></option>
                                        <option value="autre"><?php echo $translations['other']; ?></option>
                                    </select>
                                    <div class="invalid-feedback">
                                        <?php echo $translations['please_select_subject']; ?>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <label for="message" class="form-label"><?php echo $translations['message']; ?>*</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                                    <div class="invalid-feedback">
                                       <?php echo $translations['please_enter_message']; ?>
                                    </div>
                                </div>
                                
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="cta-button">
                                        <i class="fas fa-paper-plane"></i> <?php echo $translations['send_button']; ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    
    <div class="container">
        <section class="content-wrapper p-3">
            <h2 class="text-center mb-3 display-7 text-dark"><?php echo $translations['or_contact_directly']; ?></h2>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-5 col-md-7 col-sm-10">
                    <a href="mailto:centre_de_etude_du_quechua@yahoo.com" class="text-decoration-none">
                        <div class="feature-card email-card p-2  text-center">
                        <div class="feature-card p-2 h-100 text-center">
                            <div class="feature-icon mb-2">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h3 class="h5 mb-2 text-dark"><?php echo $translations['email']; ?></h3>
                            <p class="text-muted mb-0">centre_de_etudes_du_quechua@yahoo.com</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </div>
    </div>
</main>


<!-- Script de validation du formulaire -->
<script>
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>

<style>
.email-card {
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
}

.email-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border-color: #e67e22;
}

.email-card:hover .feature-icon i {
    color: #e67e22;
    transform: scale(1.1);
}

.email-card:hover h3 {
    color: #e67e22 !important;
}

.email-card .feature-icon i {
    font-size: 2.5rem;
    color: #6c757d;
    transition: all 0.3s ease;
}

a:hover {
    text-decoration: none !important;
}
</style>

<?php include 'includes/footer.php'; ?>