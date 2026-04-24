<?php
session_start();
include("config.php");

$error = "";

// Si déjà connecté, rediriger vers dashboard
if(isset($_SESSION['admin'])){
    header("Location: dashboard.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if($admin && $password == $admin['password']){
        $_SESSION['admin'] = $admin['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Drink & Fly</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #1a1008;
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
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(201,168,124,0.15) 0%, transparent 70%);
            top: -100px;
            right: -100px;
            border-radius: 50%;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(107,78,61,0.2) 0%, transparent 70%);
            bottom: -50px;
            left: -50px;
            border-radius: 50%;
        }

        .login-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(201,168,124,0.2);
            border-radius: 24px;
            padding: 56px 48px;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.6s ease-out;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-area {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            font-size: 48px;
            display: block;
            margin-bottom: 12px;
        }

        .logo-area h1 {
            font-family: 'Playfair Display', serif;
            color: #c9a87c;
            font-size: 28px;
            letter-spacing: 1px;
        }

        .logo-area p {
            color: rgba(255,255,255,0.4);
            font-size: 13px;
            margin-top: 6px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .error-box {
            background: rgba(231,76,60,0.15);
            border: 1px solid rgba(231,76,60,0.3);
            color: #e74c3c;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 24px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: rgba(255,255,255,0.6);
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(201,168,124,0.2);
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #c9a87c;
            background: rgba(201,168,124,0.08);
        }

        .form-group input::placeholder {
            color: rgba(255,255,255,0.25);
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #c9a87c, #a07850);
            border: none;
            border-radius: 12px;
            color: #1a1008;
            font-size: 16px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(201,168,124,0.3);
        }

        .back-link {
            text-align: center;
            margin-top: 24px;
        }

        .back-link a {
            color: rgba(255,255,255,0.3);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s;
        }

        .back-link a:hover {
            color: #c9a87c;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo-area">
        <span class="logo-icon">✈️</span>
        <h1>Drink & Fly</h1>
        <p>Espace Administration</p>
    </div>

    <?php if($error): ?>
        <div class="error-box">⚠️ <?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Nom d'utilisateur</label>
            <input type="text" name="username" placeholder="admin" required>
        </div>
        <div class="form-group">
            <label>Mot de passe</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit" class="submit-btn">Se connecter →</button>
    </form>

    <div class="back-link">
        <a href="home.php">← Retour au site</a>
    </div>
</div>

</body>
</html>