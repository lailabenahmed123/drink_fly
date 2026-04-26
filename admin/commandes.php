<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: ../login.php");
    exit();
}
include("../config.php");

$commandes = $conn->query("
    SELECT commandes.id, commandes.total,
           GROUP_CONCAT(produits.nom SEPARATOR ', ') as produits_noms
    FROM commandes
    LEFT JOIN details_commande ON commandes.id = details_commande.commande_id
    LEFT JOIN produits ON details_commande.produit_id = produits.id
    GROUP BY commandes.id
    ORDER BY commandes.id DESC
");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commandes - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #f5f0eb; color: #2c1810; display: flex; min-height: 100vh; }

        .sidebar { width: 260px; background:#694e39 ; min-height: 100vh; padding: 32px 0; position: fixed; top: 0; left: 0; display: flex; flex-direction: column; }
        .sidebar-logo { text-align: center; padding: 0 24px 32px; border-bottom: 1px solid rgba(201,168,124,0.15); }
        .sidebar-logo span { font-size: 36px; display: block; margin-bottom: 8px; }
        .sidebar-logo h2 { font-family: 'Playfair Display', serif; color: #c9a87c; font-size: 20px; }
        .sidebar-logo p { color: rgba(255,255,255,0.3); font-size: 11px; letter-spacing: 2px; text-transform: uppercase; margin-top: 4px; }
        .sidebar-nav { padding: 24px 16px; flex: 1; }
        .nav-label { color: rgba(255,255,255,0.25); font-size: 10px; letter-spacing: 2px; text-transform: uppercase; padding: 0 12px; margin-bottom: 8px; margin-top: 20px; }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: rgba(255,255,255,0.5); text-decoration: none; font-size: 14px; transition: all 0.2s; margin-bottom: 4px; }
        .nav-item:hover, .nav-item.active { background: rgba(201,168,124,0.15); color: #c9a87c; }
        .sidebar-footer { padding: 16px; border-top: 1px solid rgba(201,168,124,0.15); }
        .logout-btn { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 10px; color: rgba(231,76,60,0.7); text-decoration: none; font-size: 14px; transition: all 0.2s; }
        .logout-btn:hover { background: rgba(231,76,60,0.1); color: #e74c3c; }

        .main { margin-left: 260px; flex: 1; padding: 40px; }
        .page-header { margin-bottom: 32px; }
        .page-header h1 { font-family: 'Playfair Display', serif; font-size: 32px; }
        .page-header p { color: #8b6f47; margin-top: 6px; }

        .table-card { background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #8b6f47; padding: 0 12px 16px; border-bottom: 2px solid #f0e8df; }
        td { padding: 16px 12px; font-size: 14px; border-bottom: 1px solid #f5f0eb; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: #faf7f4; }

        .badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; background: rgba(39,174,96,0.1); color: #27ae60; }
        .total-amount { font-weight: 600; font-size: 15px; }
        .produits-list { color: #8b6f47; font-size: 13px; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
      <img src="../pictures/logo.png" alt="Logo" style="width:160px;height:160px;object-fit:contain;">
        <h2>Drink & Fly</h2>
        <p>Administration</p>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <a href="../dashboard.php" class="nav-item">📊 Dashboard</a>
        <a href="commandes.php" class="nav-item active">🛒 Commandes</a>
        <a href="messages.php" class="nav-item">✉️ Messages</a>
        <a href="produits.php" class="nav-item">☕ Produits</a>
        <div class="nav-label">Site</div>
        <a href="../home.php" class="nav-item" target="_blank">🌐 Voir le site</a>
    </nav>
    <div class="sidebar-footer">
        <a href="../logout.php" class="logout-btn">🚪 Déconnexion</a>
    </div>
</aside>

<main class="main">
    <div class="page-header">
        <h1>🛒 Commandes</h1>
        <p>Liste de toutes les commandes des clients</p>
    </div>

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Produits commandés</th>
                    <th>Total</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php while($c = $commandes->fetch_assoc()): ?>
                <tr>
                    <td><strong>#<?= $c['id'] ?></strong></td>
                    <td><span class="produits-list"><?= htmlspecialchars($c['produits_noms'] ?? '—') ?></span></td>
                    <td><span class="total-amount"><?= number_format($c['total'], 2) ?> DT</span></td>
                    <td><span class="badge">✓ Reçue</span></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>