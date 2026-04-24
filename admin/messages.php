<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: ../login.php");
    exit();
}

include("../config.php");

$messages = $conn->query("SELECT * FROM messages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages - Admin</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
* { margin:0; padding:0; box-sizing:border-box; }

body{
    font-family:'DM Sans',sans-serif;
    background:#f5f0eb;
    color:#2c1810;
    display:flex;
    min-height:100vh;
}

/* SIDEBAR (même style commandes) */
.sidebar{
    width:260px;
    background:#1a1008;
    min-height:100vh;
    padding:32px 0;
    position:fixed;
    left:0;
    top:0;
    display:flex;
    flex-direction:column;
}

.sidebar-logo{
    text-align:center;
    padding:0 24px 32px;
    border-bottom:1px solid rgba(201,168,124,0.15);
}

.sidebar-logo span{ font-size:36px; display:block; margin-bottom:8px; }
.sidebar-logo h2{
    font-family:'Playfair Display',serif;
    color:#c9a87c;
    font-size:20px;
}
.sidebar-logo p{
    color:rgba(255,255,255,0.3);
    font-size:11px;
    letter-spacing:2px;
}

.sidebar-nav{
    padding:24px 16px;
    flex:1;
}

.nav-label{
    color:rgba(255,255,255,0.25);
    font-size:10px;
    letter-spacing:2px;
    padding:0 12px;
    margin:20px 0 8px;
    text-transform:uppercase;
}

.nav-item{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 16px;
    border-radius:10px;
    color:rgba(255,255,255,0.5);
    text-decoration:none;
    font-size:14px;
    transition:0.2s;
    margin-bottom:4px;
}

.nav-item:hover, .nav-item.active{
    background:rgba(201,168,124,0.15);
    color:#c9a87c;
}

.sidebar-footer{
    padding:16px;
    border-top:1px solid rgba(201,168,124,0.15);
}

.logout-btn{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 16px;
    border-radius:10px;
    color:rgba(231,76,60,0.7);
    text-decoration:none;
}

.logout-btn:hover{
    background:rgba(231,76,60,0.1);
    color:#e74c3c;
}

/* MAIN */
.main{
    margin-left:260px;
    flex:1;
    padding:40px;
}

.page-header{
    margin-bottom:30px;
}

.page-header h1{
    font-family:'Playfair Display',serif;
    font-size:32px;
}

.page-header p{
    color:#8b6f47;
    margin-top:6px;
}

/* TABLE CARD */
.table-card{
    background:#fff;
    border-radius:16px;
    padding:32px;
    box-shadow:0 2px 12px rgba(0,0,0,0.06);
}

/* TABLE (STRUCTURE IDENTIQUE) */
table{
    width:100%;
    border-collapse:collapse;
}

th{
    text-align:left;
    font-size:11px;
    letter-spacing:1px;
    text-transform:uppercase;
    color:#8b6f47;
    padding-bottom:16px;
    border-bottom:2px solid #f0e8df;
}

td{
    padding:16px 0;
    font-size:14px;
    border-bottom:1px solid #f5f0eb;
}

tr:hover td{
    background:#faf7f4;
}

.total{
    font-weight:600;
    color:#2c1810;
}

.email{
    color:#6b4e3d;
    font-size:13px;
}

.message-box{
    max-width:400px;
    color:#444;
}

.date{
    color:#8b6f47;
    font-size:12px;
}

/* BUTTON HOME */
.back{
    display:inline-block;
    margin-top:20px;
    padding:10px 16px;
    background:#c9a87c;
    color:#1a1008;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
}

.back:hover{
    opacity:0.9;
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
        <a href="../dashboard.php" class="nav-item">📊 Dashboard</a>
        <a href="commandes.php" class="nav-item">🛒 Commandes</a>
        <a href="messages.php" class="nav-item active">✉️ Messages</a>
        <a href="produits.php" class="nav-item">☕ Produits</a>

        <div class="nav-label">Site</div>
        <a href="../home.php" class="nav-item" target="_blank">🌐 Voir le site</a>
    </nav>

    <div class="sidebar-footer">
        <a href="../logout.php" class="logout-btn">🚪 Déconnexion</a>
    </div>
</aside>

<!-- MAIN -->
<main class="main">

<div class="page-header">
    <h1>📩 Messages</h1>
    <p>Liste des messages envoyés par les clients</p>
</div>

<div class="table-card">

<table>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Message</th>
        <th>Date</th>
    </tr>

    <?php while($m = $messages->fetch_assoc()): ?>
        <tr>
            <td class="total">#<?= $m['id'] ?></td>
            <td><?= htmlspecialchars($m['name']) ?></td>
            <td class="email"><?= htmlspecialchars($m['email']) ?></td>
            <td class="message-box"><?= htmlspecialchars($m['message']) ?></td>
            <td class="date"><?= $m['date_envoi'] ?></td>
        </tr>
    <?php endwhile; ?>

</table>

</div>

</main>

</body>
</html>