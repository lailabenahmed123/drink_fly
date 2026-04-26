<?php
session_start();

include("config.php");
 
$result = $conn->query("SELECT * FROM produits");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Drink & Fly</title>
    <link rel="stylesheet" href="menu.css">
</head>
<body>
 
    <!-- Header -->
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
 
    <!-- Contenu Principal -->
    <main class="container">
        <h1>Notre Menu</h1>
        <p class="subtitle">Découvrez nos délicieuses créations</p>
 
        <!-- Barre de filtrage par catégorie -->
        <div class="filter-bar">
            <h3>Filtrer par catégorie :</h3>
            <div class="filter-buttons">
                <button class="filter-btn active" data-category="tous">Tous les produits</button>
                <button class="filter-btn" data-category="boissons-chaudes">Boissons Chaudes</button>
                <button class="filter-btn" data-category="boissons-froides">Boissons Froides</button>
                <button class="filter-btn" data-category="patisseries">Pâtisseries</button>
                <button class="filter-btn" data-category="sandwiches">Sandwiches</button>
            </div>
        </div>
 
        <!-- Tri par prix -->
        <div class="sort-bar">
            <label for="sortSelect">Trier par :</label>
            <select id="sortSelect">
                <option value="default">Par défaut</option>
                <option value="price-asc">Prix croissant</option>
                <option value="price-desc">Prix décroissant</option>
                <option value="name">Nom (A-Z)</option>
            </select>
        </div>
 
        <!-- Grille des produits depuis la base de données -->
        <div class="products-grid" id="productsGrid">
 
            <?php while($row = $result->fetch_assoc()): 
                // Normaliser la catégorie pour correspondre aux filtres HTML
                $cat_raw = strtolower($row['categorie']);
                $cat_slug = str_replace(' ', '-', $cat_raw);
            ?>
                <div class="product-card" 
                     data-category="<?= htmlspecialchars($cat_slug) ?>" 
                     data-price="<?= $row['prix'] ?>" 
                     data-name="<?= htmlspecialchars($row['nom']) ?>">
 
                    <?php if(!empty($row['image'])): ?>
                        <img src="pictures/products/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['nom']) ?>">
                    <?php else: ?>
                        <div style="width:100%;height:220px;background:linear-gradient(135deg,#f5ebe0,#e8d5c4);display:flex;align-items:center;justify-content:center;font-size:64px;">☕</div>
                    <?php endif; ?>
 
                    <div class="product-content">
                        <span class="product-category"><?= htmlspecialchars($row['categorie']) ?></span>
                        <h3 class="product-name"><?= htmlspecialchars($row['nom']) ?></h3>
                        <?php if(!empty($row['description'])): ?>
                            <p class="product-description"><?= htmlspecialchars($row['description']) ?></p>
                        <?php endif; ?>
                        <p class="product-price"><?= $row['prix'] ?> DT</p>
                        <div class="product-details-hover">
                            <p>✨ <?= htmlspecialchars($row['nom']) ?></p>
                            <p class="product-review">"Une expérience unique à chaque gorgée"</p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
 
        </div>
    </main>
 
    <!-- Footer -->
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
 
    <script src="menu.js"></script>
 
</body>
</html>