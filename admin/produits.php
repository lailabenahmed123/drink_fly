<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: ../login.php");
    exit();
}

include("../config.php");

// 🔴 SUPPRIMER
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM produits WHERE id=$id");
    header("Location: produits.php");
    exit();
}

// 🟢 AJOUT / UPDATE
if(isset($_POST['save'])){
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $categorie_id = $_POST['categorie_id'];

    if($_POST['id'] == ""){
        $stmt = $conn->prepare("INSERT INTO produits (nom, prix, categorie_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sdi", $nom, $prix, $categorie_id);
    } else {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE produits SET nom=?, prix=?, categorie_id=? WHERE id=?");
        $stmt->bind_param("sdii", $nom, $prix, $categorie_id, $id);
    }

    $stmt->execute();
    header("Location: produits.php");
    exit();
}

// 🔵 EDIT
$edit = null;
if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);
    $edit = $conn->query("SELECT * FROM produits WHERE id=$id")->fetch_assoc();
}

// 📋 PRODUITS
$produits = $conn->query("
    SELECT produits.*, categories.nom AS categorie_nom
    FROM produits
    LEFT JOIN categories ON produits.categorie_id = categories.id
");

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Produits</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'DM Sans', sans-serif;
    background: #f5f0eb;
    padding: 40px;
    color: #2c1810;
}

h1 {
    font-family: 'Playfair Display', serif;
    margin-bottom: 20px;
}

form {
    background: #fff;
    padding: 20px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

form input, select {
    padding: 12px;
    margin: 5px;
    border-radius: 10px;
    border: 1px solid #ddd;
}

form button {
    background: #c9a87c;
    color: #fff;
    border: none;
    padding: 12px 18px;
    border-radius: 10px;
    cursor: pointer;
}

table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
}

th {
    padding: 14px;
    background: #f0e8df;
    font-size: 12px;
    text-transform: uppercase;
}

td {
    padding: 14px;
    border-top: 1px solid #eee;
}
</style>

</head>
<body>

<h1>☕ Gestion des produits</h1>

<!-- FORM -->
<form method="POST">
    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

    <input type="text" name="nom" placeholder="Nom" required
           value="<?= $edit['nom'] ?? '' ?>">

    <input type="number" step="0.01" name="prix" placeholder="Prix" required
           value="<?= $edit['prix'] ?? '' ?>">

    <!-- 🔥 CORRECTION ICI -->
    <select name="categorie_id" required>
        <option value="">-- Choisir catégorie --</option>

        <?php
        // requête fraîche → PAS DE BUG
        $cat_result = $conn->query("SELECT * FROM categories");

        while($cat = $cat_result->fetch_assoc()):
        ?>
            <option value="<?= $cat['id'] ?>"
                <?= (isset($edit['categorie_id']) && $edit['categorie_id'] == $cat['id']) ? 'selected' : '' ?>>
                <?= $cat['nom'] ?>
            </option>
        <?php endwhile; ?>
    </select>

    <button type="submit" name="save">
        <?= $edit ? "Modifier" : "Ajouter" ?>
    </button>
</form>

<!-- TABLE -->
<table>
<tr>
    <th>ID</th>
    <th>Nom</th>
    <th>Prix</th>
    <th>Catégorie</th>
    <th>Actions</th>
</tr>

<?php while($p = $produits->fetch_assoc()): ?>
<tr>
    <td><?= $p['id'] ?></td>
    <td><?= $p['nom'] ?></td>
    <td><?= number_format($p['prix'],2) ?> DT</td>
    <td><?= $p['categorie_nom'] ?? 'Non définie' ?></td>
    <td>
        <a href="?edit=<?= $p['id'] ?>">✏️ Edit</a> |
        <a href="?delete=<?= $p['id'] ?>" onclick="return confirm('Supprimer ?')">❌ Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>