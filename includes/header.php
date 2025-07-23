<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Ma chanson en quechua '; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
   
    
    <style>
        body {
            font-family:'Times New Roman', Times, serif;
            background-image: url('/Quechua/media/images/inca2.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        .navbar {
            background: rgba(44, 62, 80, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.3);
        }

        .navbar-brand {
            font-family: Georgia, serif;
            color: #fffacd !important;
            font-weight: bold;
            font-size: 1.4rem;
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: #f39c12 !important;
        }

        .content-wrapper {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            margin: 2rem 0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .hero-section {
            background: transparent;
            color: white;
            text-align: center;
            padding: 4rem 0;
            margin-top: 80px;
        }

        .hero-section h1 {
            font-size: 3.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
            animation: fadeInUp 1s ease-out;
        }

        .hero-section p {
            font-size: 1.3rem;
            opacity: 0.95;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .cta-button {
            background: linear-gradient(45deg, #f39c12, #e67e22);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: none;
            animation: fadeInUp 1s ease-out 0.4s both;
            text-decoration: none;
            display: inline-block;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(243, 156, 18, 0.6);
            color: white;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feature-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border-radius: 15px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }

        .feature-card:hover {
            transform: none;
            box-shadow: none;
        }

        .feature-icon {
            font-size: 3rem;
            color: #e67e22;
        }

        .stats-section {
            background: rgba(44, 62, 80, 0.95);
            color: white;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: #f39c12;
        }

        footer {
            background: rgba(44, 62, 80, 0.95);
            color: white;
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>