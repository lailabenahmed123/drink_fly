<?php

session_start();
include("config.php");

$success = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    if(empty($name) || empty($email) || empty($message)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse email invalide.";
    } else {
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        if($stmt->execute()){
            $stmt->close();
            header("Location: contact.php?success=1");
            exit();
        } else {
            $error = "Erreur : " . $stmt->error;
        }
    }
}

if(isset($_GET['success']) && $_GET['success'] == 1) {
    $success = true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Drink & Fly</title>
    <link rel="icon" type="image/x-icon" href="pictures/favicon.ico">
    <link rel="stylesheet" href="contact.css">
</head>
<body>
    <header>
        <nav class="nav-container">
            <div class="logo">
                <img src="pictures/logo.png" alt="Logo Drink & Fly">
                <p>Drink & Fly</p>
            </div>
            <ul>
                <li><a href="home.php" class="nav-link">Accueil</a></li>
                <li><a href="menu.php" class="nav-link">Menu</a></li>
                <li><a href="contact.php" class="nav-link">Contact</a></li>
                <?php if(isset($_SESSION['client'])): ?>
        <li><a href="logout.php">👋 <?= $_SESSION['client'] ?> | Déconnexion</a></li>
    <?php else: ?>
        <li><a href="login.php">Connexion</a></li>
    <?php endif; ?>
            </ul>

            
        </nav>
    </header>
    <main class="container">
        <h1>Contactez-nous</h1>
        <p class="subtitle">Nous serions ravis de vous entendre</p>
        <div class="contact-content">
            <section class="contact-form-section">
                <h2>Envoyez-nous un message</h2>
                <?php if($error): ?>
                    <p style="color:#e74c3c;padding:12px;background:#fde;border-radius:8px;margin-bottom:16px;">
                        <?= $error ?>
                    </p>
                <?php endif; ?>
                <form id="contactForm" class="contact-form" method="POST">
                    <div class="form-group">
                        <label for="name">Nom complet <span class="required">*</span></label>
                        <input type="text" id="name" name="name" placeholder="Votre nom"
                            value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required>
                        <span class="error-message" id="nameError"></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="votre.email@exemple.com"
                            value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                        <span class="error-message" id="emailError"></span>
                    </div>
                    <div class="form-group">
                        <label for="message">Message <span class="required">*</span></label>
                        <textarea id="message" name="message" rows="6" placeholder="Votre message..." required
                        ><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
                        <span class="error-message" id="messageError"></span>
                    </div>
                    <button type="submit" class="submit-btn">Envoyer le message</button>
                </form>
                <div id="confirmationMessage" class="confirmation-message <?= $success ? 'show' : '' ?>">
                    <div class="confirmation-content">
                        <span class="success-icon">✓</span>
                        <h3>Message envoyé avec succès !</h3>
                        <p>Merci pour votre message. Nous vous répondrons dans les plus brefs délais.</p>
                    </div>
                </div>
            </section>
            <section class="contact-info">
                <h2>Nos coordonnées</h2>
                <div class="info-card">
                    <div class="info-icon">📍</div>
                    <div class="info-content">
                        <h3>Adresse</h3>
                        <p>123 Rue du Café<br>Sousse, Tunisie</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon">📞</div>
                    <div class="info-content">
                        <h3>Téléphone</h3>
                        <p>+216 25 123 456</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon">✉️</div>
                    <div class="info-content">
                        <h3>Email</h3>
                        <p>contact@drinkandfly.tn</p>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-icon">🕒</div>
                    <div class="info-content">
                        <h3>Horaires d'ouverture</h3>
                        <p>Lundi - Vendredi: 7h - 20h<br>Samedi - Dimanche: 8h - 22h</p>
                    </div>
                </div>
            </section>
        </div>
        <section class="map-section">
            <h2>Trouvez-nous</h2>
            <div id="map" class="map-container">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3240.!2d10.8376618!3d35.7698148!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1302133fcb65b517%3A0x9d7a2602a88b9954!2sCaf%C3%A9%20Latino!5e0!3m2!1sfr!2stn!4v1"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
        </section>
    </main>
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h3>Drink & Fly</h3>
                <p>Votre pause café parfaite au cœur de la ville.</p>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>📍 123 Rue du Café, Sousse, Tunisie</p>
                <p>📞 +216 25 123 456</p>
                <p>✉️ contact@drinkandfly.tn</p>
            </div>
            <div class="footer-section">
                <h3>Horaires</h3>
                <p>Lundi - Vendredi: 7h - 20h</p>
                <p>Samedi - Dimanche: 8h - 22h</p>
            </div>
            <div class="footer-section">
                <h3>Suivez-nous</h3>
                <div class="social-links">
                    <a href="#" title="Facebook"><img src="pictures/facebook.png" alt="facebook"></a>
                    <a href="#" title="Instagram"><img src="pictures/instagram.png" alt="instagram"></a>
                    <a href="#" title="Tik-Tok"><img src="pictures/tik-tok.png" alt="tik-tok"></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Drink & Fly. Tous droits réservés.</p>
        </div>
    </footer>
    
</body>
</html>