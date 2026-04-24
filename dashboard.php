<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}
include("config.php");

// Statistiques
$nb_commandes = $conn->query("SELECT COUNT(*) as total FROM commandes")->fetch_assoc()['total'];
$nb_messages  = $conn->query("SELECT COUNT(*) as total FROM messages")->fetch_assoc()['total'];
$nb_produits  = $conn->query("SELECT COUNT(*) as total FROM produits")->fetch_assoc()['total'];
$total_revenus = $conn->query("SELECT SUM(total) as total FROM commandes")->fetch_assoc()['total'] ?? 0;

// Dernières commandes
$dernieres_commandes = $conn->query("SELECT * FROM commandes ORDER BY id DESC LIMIT 5");

// Derniers messages
$derniers_messages = $conn->query("SELECT * FROM messages ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Drink & Fly</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f5f0eb;
            color: #2c1810;
            min-height: 100vh;
            display: flex;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: #1a1008;
            min-height: 100vh;
            padding: 32px 0;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-logo {
            text-align: center;
            padding: 0 24px 32px;
            border-bottom: 1px solid rgba(201,168,124,0.15);
        }

        .sidebar-logo span { font-size: 36px; display: block; margin-bottom: 8px; }

        .sidebar-logo h2 {
            font-family: 'Playfair Display', serif;
            color: #c9a87c;
            font-size: 20px;
        }

        .sidebar-logo p {
            color: rgba(255,255,255,0.3);
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .sidebar-nav {
            padding: 24px 16px;
            flex: 1;
        }

        .nav-label {
            color: rgba(255,255,255,0.25);
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 0 12px;
            margin-bottom: 8px;
            margin-top: 20px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s ease;
            margin-bottom: 4px;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(201,168,124,0.15);
            color: #c9a87c;
        }

        .nav-item .icon { font-size: 18px; }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(201,168,124,0.15);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            color: rgba(231,76,60,0.7);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.2s;
            width: 100%;
        }

        .logout-btn:hover {
            background: rgba(231,76,60,0.1);
            color: #e74c3c;
        }

        /* MAIN CONTENT */
        .main {
            margin-left: 260px;
            flex: 1;
            padding: 40px;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #2c1810;
        }

        .page-header p {
            color: #8b6f47;
            margin-top: 6px;
        }

        /* STATS CARDS */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.04);
            transition: transform 0.2s;
        }

        .stat-card:hover { transform: translateY(-4px); }

        .stat-icon {
            font-size: 32px;
            margin-bottom: 16px;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 600;
            color: #2c1810;
            line-height: 1;
        }

        .stat-label {
            color: #8b6f47;
            font-size: 13px;
            margin-top: 6px;
        }

        .stat-card.highlight {
            background: linear-gradient(135deg, #1a1008, #3d2010);
            color: #fff;
        }

        .stat-card.highlight .stat-value,
        .stat-card.highlight .stat-label { color: #c9a87c; }

        /* TABLES */
        .tables-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .table-card {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }

        .table-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-card-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #2c1810;
        }

        .see-all {
            color: #c9a87c;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #8b6f47;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0e8df;
        }

        td {
            padding: 12px 0;
            font-size: 14px;
            border-bottom: 1px solid #f5f0eb;
            color: #2c1810;
        }

        tr:last-child td { border-bottom: none; }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background: rgba(39,174,96,0.1);
            color: #27ae60;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <span>✈️</span>
        <h2>Drink & Fly</h2>
        <p>Administration</p>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <a href="dashboard.php" class="nav-item active">
            <span class="icon">📊</span> Dashboard
        </a>
        <a href="admin/commandes.php" class="nav-item">
            <span class="icon">🛒</span> Commandes
        </a>
        <a href="admin/messages.php" class="nav-item">
            <span class="icon">✉️</span> Messages
        </a>
        <a href="admin/produits.php" class="nav-item">
            <span class="icon">☕</span> Produits
        </a>

        <div class="nav-label">Site</div>
        <a href="home.php" class="nav-item" target="_blank">
            <span class="icon">🌐</span> Voir le site
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php" class="logout-btn">
            <span>🚪</span> Déconnexion
        </a>
    </div>
</aside>

<!-- MAIN -->
<main class="main">
    <div class="page-header">
        <h1>Bonjour, <?= $_SESSION['admin'] ?> 👋</h1>
        <p>Voici un aperçu de votre activité</p>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card highlight">
            <div class="stat-icon">💰</div>
            <div class="stat-value"><?= number_format($total_revenus, 2) ?> DT</div>
            <div class="stat-label">Revenus totaux</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🛒</div>
            <div class="stat-value"><?= $nb_commandes ?></div>
            <div class="stat-label">Commandes</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">✉️</div>
            <div class="stat-value"><?= $nb_messages ?></div>
            <div class="stat-label">Messages</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">☕</div>
            <div class="stat-value"><?= $nb_produits ?></div>
            <div class="stat-label">Produits</div>
        </div>
    </div>

    <!-- TABLES -->
    <div class="tables-grid">

        <!-- Dernières commandes -->
        <div class="table-card">
            <div class="table-card-header">
                <h3>🛒 Dernières commandes</h3>
                <a href="admin/commandes.php" class="see-all">Voir tout →</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Total</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($c = $dernieres_commandes->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $c['id'] ?></td>
                            <td><?= number_format($c['total'], 2) ?> DT</td>
                            <td><span class="badge">✓ Reçue</span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Derniers messages -->
        <div class="table-card">
            <div class="table-card-header">
                <h3>✉️ Derniers messages</h3>
                <a href="admin/messages.php" class="see-all">Voir tout →</a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($m = $derniers_messages->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['name']) ?></td>
                            <td style="color:#8b6f47;font-size:13px;"><?= htmlspecialchars($m['email']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

</body>
</html>