<?php
session_start();
include("config.php");

$error = "";
$success = "";

if(isset($_SESSION['client'])){
    header("Location: home.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if($password !== $confirm){
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si l'email existe déjà
        $check = $conn->prepare("SELECT id FROM clients WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if($check->num_rows > 0){
            $error = "Cet email est déjà utilisé.";
        } else {
            $stmt = $conn->prepare("INSERT INTO clients (nom, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nom, $email, $password);
            if($stmt->execute()){
                $success = "Compte créé avec succès ! Vous pouvez vous connecter.";
            } else {
                $error = "Erreur lors de la création du compte.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Drink & Fly</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #faf8f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(201,168,124,0.2) 0%, transparent 70%);
            top: -100px; right: -100px;
            border-radius: 50%;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(107,78,61,0.1) 0%, transparent 70%);
            bottom: -50px; left: -50px;
            border-radius: 50%;
        }

        .register-card {
            background: #fff;
            border: 1px solid rgba(201,168,124,0.3);
            border-radius: 24px;
            padding: 48px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
            box-shadow: 0 20px 60px rgba(107,78,61,0.12);
            animation: fadeUp 0.6s ease-out;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-area {
            text-align: center;
            margin-bottom: 36px;
        }

        .logo-icon { font-size: 44px; display: block; margin-bottom: 10px; }

        .logo-area h1 {
            font-family: 'Playfair Display', serif;
            color: #6b4e3d;
            font-size: 26px;
        }

        .logo-area p {
            color: #8b6f47;
            font-size: 13px;
            margin-top: 6px;
            letter-spacing: 1px;
        }

        .error-box {
            background: rgba(231,76,60,0.08);
            border: 1px solid rgba(231,76,60,0.3);
            color: #e74c3c;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success-box {
            background: rgba(39,174,96,0.08);
            border: 1px solid rgba(39,174,96,0.3);
            color: #27ae60;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block;
            color: #6b4e3d;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 16px;
            background: #faf8f5;
            border: 2px solid #e8d5c4;
            border-radius: 12px;
            color: #2c1810;
            font-size: 15px;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #c9a87c;
            background: #fff;
        }

        .form-group input::placeholder { color: #c4b0a0; }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #6b4e3d, #4a3828);
            border: none;
            border-radius: 12px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(107,78,61,0.3);
        }

        .links {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #8b6f47;
        }

        .links a {
            color: #c9a87c;
            text-decoration: none;
            font-weight: 500;
        }

        .links a:hover { text-decoration: underline; }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: #c4b0a0;
            font-size: 13px;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e8d5c4;
        }
    </style>
</head>
<body>

<div class="register-card">
    <div class="logo-area">
        <span class="logo-icon">✈️</span>
        <h1>Drink & Fly</h1>
        <p>Créer un compte client</p>
    </div>

    <?php if($error): ?>
        <div class="error-box">⚠️ <?= $error ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="success-box">✓ <?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Nom complet</label>
            <input type="text" name="nom" placeholder="Votre nom" required
                   value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="votre@email.com" required
                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <div class="form-group">
            <label>Confirmer le mot de passe</label>
            <input type="password" name="confirm" placeholder="••••••••" required>
        </div>
        <button type="submit" class="submit-btn">Créer mon compte →</button>
    </form>

    <div class="divider">ou</div>

    <div class="links">
        Déjà un compte ? <a href="login_client.php">Se connecter</a>
    </div>
    <div class="links" style="margin-top:12px;">
        <a href="home.php">← Retour au site</a>
    </div>
</div>

</body>
</html>