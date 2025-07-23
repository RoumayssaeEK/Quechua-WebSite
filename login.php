<?php
ob_start();

if (isset($_POST['remember_me'])) {
    
    session_set_cookie_params([
        'lifetime' => 7 * 24 * 60 * 60,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => false, 
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
} else {
    
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}


session_start();
require_once 'config/database.php';

$database = new Database();
$pdo = $database->getConnection();


if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        try {
           
            $stmt = $pdo->prepare("SELECT id, username, password FROM utilisateur WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]); // Passer le même username pour les deux paramètres
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Définir les sessions correctement
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                 
                header('Location: admin.php');
                exit;
            } else {
                $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $error = 'Erreur de connexion à la base de données: ' . $e->getMessage();
             error_log("Erreur DB: " . $e->getMessage());
        }
    }
}
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Ma chanson en quechua</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background-image: url('media/images/inca2.jpg'); 
            background-size: cover; 
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 0;
        }
        
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.4); 
            z-index: -1; 
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
        }
        
        .login-box {
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
            min-height: 420px;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            padding: 10px 10px;
            text-align: center;
        }
        
        .login-header i {
            font-size: 40px;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .login-header h1 {
            font-size: 24px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .login-header p {
            font-size: 13px;
            opacity: 0.8;
            font-weight: 300;
        }
        
        .login-form {
            padding: 30px 25px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        
        .form-group label i {
            margin-right: 8px;
            color: #3498db;
            width: 16px;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }
        
        .alert-error {
            background: #fee;
            color: #c53030;
            border: 1px solid #fed7d7;
        }
        
        .alert-error i {
            color: #e53e3e;
        }
        
        .login-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            border-top: 1px solid #e1e8ed;
            text-align: center;
        }
        
        .login-footer a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease;
        }
        
        .login-footer a:hover {
            color: #3498db;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 10px;
            }
            
            .login-header {
                padding: 30px 20px;
            }
            
            .login-form {
                padding: 30px 20px;
            }
            
            .login-footer {
                padding: 15px 20px;
            }
        }
        
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .loading .btn {
            background: #bdc3c7;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <i class="fas fa-music"></i>
                <h1>Ma chanson en quechua</h1>
                <p>Administration</p>
            </div>
            
            <form method="POST" class="login-form" id="loginForm">
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i>
                        Nom d'utilisateur ou Email
                    </label>
                    <input type="text" id="username" name="username" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                           required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i>
                        Mot de passe
                    </label>
                    <input type="password" id="password" name="password" required>
                </div>
                <label>
                <input type="checkbox" name="remember_me"> Rester connecté
                </label>
                
                <button type="submit" class="btn" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    Se connecter
                </button>
            </form>
            
            <div class="login-footer">
                <a href="index.php">
                    <i class="fas fa-arrow-left"></i>
                    Retour au site
                </a>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            
            // Animation de soumission
            loginForm.addEventListener('submit', function() {
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Connexion...';
                loginForm.classList.add('loading');
            });
            
            // Effet de focus amélioré
            const inputs = [usernameInput, passwordInput];
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
            
            // Validation en temps réel
            loginForm.addEventListener('input', function() {
                const username = usernameInput.value.trim();
                const password = passwordInput.value;
                
                if (username && password) {
                    loginBtn.style.opacity = '1';
                    loginBtn.style.cursor = 'pointer';
                } else {
                    loginBtn.style.opacity = '0.7';
                    loginBtn.style.cursor = 'not-allowed';
                }
            });
            
            // Gestion des erreurs visuelles
            const errorAlert = document.querySelector('.alert-error');
            if (errorAlert) {
                setTimeout(() => {
                    errorAlert.style.opacity = '0';
                    setTimeout(() => {
                        errorAlert.remove();
                    }, 300);
                }, 5000);
            }
            
            // Raccourci clavier
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && (usernameInput.value.trim() && passwordInput.value)) {
                    loginForm.submit();
                }
            });
            
            console.log('Interface de connexion admin chargée');
        });
    </script>
</body>
</html>
