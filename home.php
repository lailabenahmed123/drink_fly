<?php
include("config.php");

$produits = $conn->query("SELECT * FROM produits");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $total = $_POST['total'];

    $stmt = $conn->prepare("INSERT INTO commandes (total) VALUES (?)");
    $stmt->bind_param("d", $total);
    $stmt->execute();

    $commande_id = $stmt->insert_id;

    if(isset($_POST['produits'])){
        foreach($_POST['produits'] as $produit_id){
            $stmt2 = $conn->prepare("INSERT INTO details_commande (commande_id, produit_id) VALUES (?, ?)");
            $stmt2->bind_param("ii", $commande_id, $produit_id);
            $stmt2->execute();
        }
    }

    echo "<script>alert('Commande enregistrée !');</script>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drink & Fly - Votre Pause Café Parfaite</title>
    <link rel="icon" type="image/x-icon" href="pictures/favicon.ico">
    <link rel="stylesheet" href="home.css">
</head>
<body>

    <!-- En-tête avec navigation -->
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
            </ul>
        </nav>
    </header>

    <!-- Bannière principale avec slideshow -->
    <section id="home" class="hero">
        <div class="slideshow-container">
            <div class="slide active">
                <img src="pictures/enter_corner.jpg" alt="Café Ambiance 1">
            </div>
            <div class="slide">
                <img src="pictures/friend_corner.jpg" alt="Café Ambiance 2">
            </div>
            <div class="slide">
                <img src="pictures/book_corner.png" alt="Café Ambiance 3">
            </div>
        </div>
        <div class="hero-content">
            <h1 class="animated-title">Bienvenue chez Drink & Fly</h1>
            <p class="animated-subtitle">Venez savourer un moment de tranquillité</p>
        </div>
    </section>

    <!-- Présentation du café -->
    <section id="about" class="about">
        <div class="about-container">
            <div class="about-text">
                <h2>Découvrez Notre Histoire</h2>
                <p>Bienvenue chez Drink & Fly, un café familial au cœur de la ville. 
                    Chacun de nos produits est préparé avec amour et passion, créant 
                    une atmosphère chaleureuse où chaque gorgée est une petite évasion.</p>
                <p>Nos boissons phares incluent l'espresso artisanal, les lattes faites 
                    main, et des thés de spécialité provenant de producteurs locaux.</p>
            </div>
            <div class="about-image">
                <img src="pictures/friend_corner.jpg" alt="Intérieur du café">
            </div>
        </div>
    </section>

    <!-- Nos valeurs -->
    <section id="values" class="values">
        <div class="values-container">
            <h2>Nos Valeurs</h2>
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">🌾</div>
                    <h3>Produits Locaux</h3>
                    <p>Nous privilégions les producteurs locaux pour vous offrir des produits frais et de qualité.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🤝</div>
                    <h3>Service Amical</h3>
                    <p>Notre équipe passionnée vous accueille avec le sourire et un service chaleureux.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">🛋️</div>
                    <h3>Ambiance Conviviale</h3>
                    <p>Un lieu où chacun se sent chez soi, parfait pour se détendre ou rencontrer des amis.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta">
        <h2>Prêt à découvrir nos délices ?</h2>
        <a href="menu.php" class="cta-button">Découvrez Notre Menu</a>
    </section>

    <!-- Section commande et facture -->
    <section id="order" class="order">
        <div class="order-container">
            <h2>Passez Votre Commande</h2>
            <form method="POST" id="orderForm">
                <div class="order-content">

                    <!-- Produits depuis la base de données -->
                    <div class="menu-items">
                        <h3>Sélectionnez Vos Articles</h3>

                        <?php while($p = $produits->fetch_assoc()): ?>
                            <div class="menu-item">
                                <input type="checkbox"
                                       class="item-checkbox"
                                       name="produits[]"
                                       id="prod_<?= $p['id'] ?>"
                                       value="<?= $p['prix'] ?>"
                                       data-id="<?= $p['id'] ?>"
                                       data-price="<?= $p['prix'] ?>">
                                <label for="prod_<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nom']) ?> - <?= $p['prix'] ?> DT
                                </label>
                            </div>
                        <?php endwhile; ?>

                        <input type="hidden" name="total" id="total-input" value="0">

                        <button type="button" id="calculate-btn" class="calculate-btn">Calculer le Total</button>
                        <button type="submit" class="calculate-btn" style="margin-top:10px;background-color:#4a3828;">
                            Commander ✅
                        </button>
                    </div>

                    <!-- Facture dynamique -->
                    <div class="invoice">
                        <h3>Votre Facture</h3>
                        <div id="invoice-items" class="invoice-items">
                            <p class="empty-message">Sélectionnez des articles pour voir la facture</p>
                        </div>
                        <div class="invoice-total">
                            <p><strong>Sous-total:</strong> <span id="subtotal">0.00 DT</span></p>
                            <p><strong>Taxe (10%):</strong> <span id="tax">0.00 DT</span></p>
                            <p class="total-line"><strong>Total:</strong> <span id="total">0.00 DT</span></p>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>

    <!-- Pied de page -->
    <footer id="contact">
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

    <script src="home.js"></script>

    <!-- UN SEUL script qui gère tout -->
    <script>
        // 1. Calculer le total et mettre à jour le champ hidden
        document.getElementById('calculate-btn').addEventListener('click', function() {
            calculateTotal();
            setTimeout(() => {
                const totalText = document.getElementById('total').textContent;
                const totalValue = parseFloat(totalText);
                document.getElementById('total-input').value = isNaN(totalValue) ? 0 : totalValue.toFixed(2);
            }, 600);
        });

        // 2. Avant de soumettre, envoyer les data-id au PHP
        document.getElementById('orderForm').addEventListener('submit', function() {
            document.querySelectorAll('.item-checkbox:checked').forEach(function(cb) {
                cb.value = cb.dataset.id; // remplace le prix par l'ID pour le PHP
            });
        });
    </script>

</body>
</html>