<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: ../login.php");
    exit();
}

include("../config.php");


if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $row = $conn->query("SELECT image FROM produits WHERE id=$id")->fetch_assoc();
    if(!empty($row['image']) && file_exists("../pictures/products/" . $row['image'])){
        unlink("../pictures/products/" . $row['image']);
    }
    $conn->query("DELETE FROM produits WHERE id=$id");
    header("Location: produits.php");
    exit();
}

// AJOUT / UPDATE
if(isset($_POST['save'])){
    $nom         = $_POST['nom'];
    $prix        = $_POST['prix'];
    $categorie   = $_POST['categorie'];   // texte ex: "boissons-chaudes"
    $description = $_POST['description'];
    $image_name  = $_POST['image_actuelle'] ?? '';

    // Upload image
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if(in_array(strtolower($ext), $allowed)){
            $image_name = uniqid('prod_') . '.' . $ext;
            $upload_dir = "../pictures/products/";
            if(!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_name);
        }
    }

    if($_POST['id'] == ""){
        $stmt = $conn->prepare("INSERT INTO produits (nom, prix, categorie, image, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdsss", $nom, $prix, $categorie, $image_name, $description);
    } else {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE produits SET nom=?, prix=?, categorie=?, image=?, description=? WHERE id=?");
        $stmt->bind_param("sdsssi", $nom, $prix, $categorie, $image_name, $description, $id);
    }

    $stmt->execute();
    header("Location: produits.php");
    exit();
}

// EDIT
$edit = null;
if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);
    $edit = $conn->query("SELECT * FROM produits WHERE id=$id")->fetch_assoc();
}

// LISTE PRODUITS
$produits = $conn->query("SELECT * FROM produits ORDER BY id DESC");

// Catégories fixes (comme dans ta DB)
$categories = [
    'boissons-chaudes' => ' Boissons Chaudes',
    'boissons-froides' => ' Boissons Froides',
    'patisseries'      => ' Pâtisseries',
    'sandwiches'       => ' Sandwiches',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #f5f0eb; color: #2c1810; display: flex; min-height: 100vh; }

        .sidebar { width: 260px; background: #694e39; min-height: 100vh; padding: 32px 0; position: fixed; top: 0; left: 0; display: flex; flex-direction: column; }
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

        .form-card { background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); margin-bottom: 32px; }
        .form-card h2 { font-size: 18px; margin-bottom: 24px; color: #6b4e3d; font-family: 'Playfair Display', serif; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: 1 / -1; }

        .form-group label { font-size: 12px; font-weight: 600; color: #8b6f47; text-transform: uppercase; letter-spacing: 0.5px; }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 14px;
            border: 2px solid #e8d5c4;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: #2c1810;
            background: #faf8f5;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus { outline: none; border-color: #c9a87c; background: #fff; }

        .form-group textarea { resize: vertical; min-height: 90px; }

        .image-upload-area {
            border: 2px dashed #e8d5c4;
            border-radius: 10px;
            padding: 24px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: #faf8f5;
            position: relative;
        }

        .image-upload-area:hover { border-color: #c9a87c; background: #fff; }

        .image-upload-area input[type="file"] {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%;
            opacity: 0; cursor: pointer; border: none; padding: 0;
        }

        .upload-icon { font-size: 28px; margin-bottom: 6px; }
        .upload-text { color: #8b6f47; font-size: 13px; }

        .image-preview { margin-top: 10px; display: none; }
        .image-preview img { width: 100px; height: 90px; object-fit: cover; border-radius: 10px; border: 2px solid #e8d5c4; }

        .current-image { display: flex; align-items: center; gap: 10px; padding: 10px; background: #f5f0eb; border-radius: 10px; margin-top: 8px; }
        .current-image img { width: 56px; height: 56px; object-fit: cover; border-radius: 8px; }
        .current-image span { font-size: 12px; color: #8b6f47; }

        .form-actions { margin-top: 24px; display: flex; gap: 12px; }

        .btn-save { padding: 13px 28px; background: linear-gradient(135deg, #c9a87c, #a07850); border: none; border-radius: 10px; color: #fff; font-size: 15px; font-weight: 600; font-family: 'DM Sans', sans-serif; cursor: pointer; transition: all 0.2s; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(201,168,124,0.3); }

        .btn-cancel { padding: 13px 28px; background: #f0e8df; border: none; border-radius: 10px; color: #6b4e3d; font-size: 15px; font-family: 'DM Sans', sans-serif; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }

        .table-card { background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; color: #8b6f47; padding: 0 12px 16px; border-bottom: 2px solid #f0e8df; }
        td { padding: 14px 12px; font-size: 14px; border-bottom: 1px solid #f5f0eb; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }

        .product-img { width: 56px; height: 56px; object-fit: cover; border-radius: 10px; border: 2px solid #e8d5c4; }
        .no-img { width: 56px; height: 56px; background: #f0e8df; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; }

        .desc-text { color: #8b6f47; font-size: 13px; max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block; }

        .btn-edit { padding: 6px 14px; background: #f5f0eb; border-radius: 8px; color: #6b4e3d; text-decoration: none; font-size: 13px; transition: all 0.2s; margin-right: 6px; }
        .btn-edit:hover { background: #c9a87c; color: #fff; }
        .btn-delete { padding: 6px 14px; background: rgba(231,76,60,0.08); border-radius: 8px; color: #e74c3c; text-decoration: none; font-size: 13px; transition: all 0.2s; }
        .btn-delete:hover { background: #e74c3c; color: #fff; }

        .badge-cat { display: inline-block; padding: 4px 10px; background: #f0e8df; border-radius: 20px; font-size: 12px; color: #6b4e3d; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="../pictures/logo.png" alt="Logo" style="width:160px;height:160px;object-fit:contain;">
        <h2>Drink & Fly</h2>
        <p>Administration</p>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <a href="../dashboard.php" class="nav-item">📊 Dashboard</a>
        <a href="commandes.php" class="nav-item">🛒 Commandes</a>
        <a href="messages.php" class="nav-item">✉️ Messages</a>
        <a href="produits.php" class="nav-item active">☕ Produits</a>
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
        <h1>☕ Gestion des Produits</h1>
        <p><?= $edit ? "Modifier un produit existant" : "Ajouter un nouveau produit" ?></p>
    </div>

    <!-- FORMULAIRE -->
    <div class="form-card">
        <h2><?= $edit ? "✏️ Modifier le produit" : "➕ Nouveau produit" ?></h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
            <input type="hidden" name="image_actuelle" value="<?= $edit['image'] ?? '' ?>">

            <div class="form-grid">

                <!-- Nom -->
                <div class="form-group">
                    <label>Nom du produit</label>
                    <input type="text" name="nom" placeholder="Ex: Latte Vanille" required
                           value="<?= htmlspecialchars($edit['nom'] ?? '') ?>">
                </div>

                <!-- Prix -->
                <div class="form-group">
                    <label>Prix (DT)</label>
                    <input type="number" step="0.01" name="prix" placeholder="Ex: 5.50" required
                           value="<?= $edit['prix'] ?? '' ?>">
                </div>

                <!-- Catégorie -->
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="categorie" required>
                        <option value="">-- Choisir une catégorie --</option>
                        <?php foreach($categories as $val => $label): ?>
                            <option value="<?= $val ?>"
                                <?= (isset($edit['categorie']) && $edit['categorie'] == $val) ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Image -->
                <div class="form-group">
                    <label>Image du produit</label>
                    <div class="image-upload-area">
                        <input type="file" name="image" accept="image/*" onchange="previewImage(this)">
                        <div class="upload-icon">📷</div>
                        <div class="upload-text">Cliquez pour choisir une image<br><small>JPG, PNG, WEBP</small></div>
                    </div>
                    <div class="image-preview" id="imagePreview">
                        <img id="previewImg" src="" alt="Aperçu">
                    </div>
                    <?php if(!empty($edit['image'])): ?>
                        <div class="current-image">
                            <img src="../pictures/products/<?= htmlspecialchars($edit['image']) ?>" alt="Image actuelle">
                            <span>Image actuelle conservée si aucune nouvelle image choisie</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div class="form-group full">
                    <label>Description</label>
                    <textarea name="description" placeholder="Ex: Café latte avec sirop de vanille naturel..."><?= htmlspecialchars($edit['description'] ?? '') ?></textarea>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" name="save" class="btn-save">
                    <?= $edit ? "💾 Modifier le produit" : "➕ Ajouter le produit" ?>
                </button>
                <?php if($edit): ?>
                    <a href="produits.php" class="btn-cancel">✕ Annuler</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- TABLEAU -->
    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Catégorie</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($p = $produits->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if(!empty($p['image'])): ?>
                            <img src="../pictures/products/<?= htmlspecialchars($p['image']) ?>" class="product-img" alt="">
                        <?php else: ?>
                            <div class="no-img">☕</div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= htmlspecialchars($p['nom']) ?></strong></td>
                    <td><span class="desc-text"><?= htmlspecialchars($p['description'] ?? '—') ?></span></td>
                    <td><strong><?= number_format($p['prix'], 2) ?> DT</strong></td>
                    <td><span class="badge-cat"><?= htmlspecialchars($p['categorie'] ?? '—') ?></span></td>
                    <td>
                        <a href="?edit=<?= $p['id'] ?>" class="btn-edit">✏️ Modifier</a>
                        <a href="?delete=<?= $p['id'] ?>" class="btn-delete"
                           onclick="return confirm('Supprimer ce produit ?')">🗑️ Supprimer</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
function previewImage(input) {
    if(input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>