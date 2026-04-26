<?php
session_start();
include("config.php");

$error_admin  = "";
$error_client = "";
$active_tab   = "client";

// LOGIN ADMIN
if(isset($_POST['login_admin'])){
    $active_tab = "admin";
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password_admin'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if($admin && $password == $admin['password']){
        $_SESSION['admin'] = $admin['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error_admin = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}

// LOGIN CLIENT
if(isset($_POST['login_client'])){
    $active_tab = "client";
    $email    = htmlspecialchars($_POST['email']);
    $password = $_POST['password_client'];

    $stmt = $conn->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $client = $stmt->get_result()->fetch_assoc();

    if($client && $password == $client['password']){
        $_SESSION['client']    = $client['nom'];
        $_SESSION['client_id'] = $client['id'];
        header("Location: home.php");
        exit();
    } else {
        $error_client = "Email ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Drink & Fly</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #694e39;
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
            background: radial-gradient(circle, rgba(201,168,124,0.15) 0%, transparent 70%);
            top: -100px; right: -100px;
            border-radius: 50%;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(107,78,61,0.2) 0%, transparent 70%);
            bottom: -50px; left: -50px;
            border-radius: 50%;
        }

        .login-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(201,168,124,0.2);
            border-radius: 24px;
            padding: 48px;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.6s ease-out;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo-area { text-align: center; margin-bottom: 32px; }
        .logo-area span { font-size: 44px; display: block; margin-bottom: 10px; }
        .logo-area h1 { font-family: 'Playfair Display', serif; color: #c9a87c; font-size: 26px; }
        .logo-area p { color: rgba(255,255,255,0.35); font-size: 12px; letter-spacing: 2px; text-transform: uppercase; margin-top: 6px; }

        .tabs { display: flex; background: rgba(255,255,255,0.06); border-radius: 12px; padding: 4px; margin-bottom: 28px; }

        .tab-btn {
            flex: 1; padding: 11px; border: none; border-radius: 10px;
            font-size: 14px; font-family: 'DM Sans', sans-serif; font-weight: 500;
            cursor: pointer; transition: all 0.3s ease;
            background: transparent; color: rgba(255,255,255,0.4);
        }

        .tab-btn.active { background: #c9a87c; color: #1a1008; font-weight: 600; }

        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .error-box {
            background: rgba(231,76,60,0.15);
            border: 1px solid rgba(231,76,60,0.3);
            color: #e74c3c;
            padding: 12px 16px; border-radius: 10px;
            font-size: 14px; margin-bottom: 20px; text-align: center;
        }

        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block; color: rgba(255,255,255,0.5);
            font-size: 12px; letter-spacing: 1px;
            text-transform: uppercase; margin-bottom: 8px;
        }

        .form-group input {
            width: 100%; padding: 13px 16px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(201,168,124,0.2);
            border-radius: 12px; color: #fff;
            font-size: 15px; font-family: 'DM Sans', sans-serif;
            transition: all 0.3s ease;
        }

        .form-group input:focus { outline: none; border-color: #c9a87c; background: rgba(201,168,124,0.08); }
        .form-group input::placeholder { color: rgba(255,255,255,0.2); }

        .submit-btn {
            width: 100%; padding: 15px;
            background: linear-gradient(135deg, #c9a87c, #a07850);
            border: none; border-radius: 12px;
            color: #1a1008; font-size: 16px; font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer; margin-top: 8px; transition: all 0.3s ease;
        }

        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(201,168,124,0.3); }

        .links { text-align: center; margin-top: 20px; font-size: 13px; color: rgba(255,255,255,0.3); }
        .links a { color: #c9a87c; text-decoration: none; }
        .links a:hover { text-decoration: underline; }

        .back-link { text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid rgba(201,168,124,0.1); }
        .back-link a { color: rgba(255,255,255,0.25); text-decoration: none; font-size: 13px; transition: color 0.3s; }
        .back-link a:hover { color: #c9a87c; }
    </style>
</head>
<body>

<div class="login-card">

    <div class="logo-area">
        <img src="pictures/logo.png" alt="Logo" style="width:160px;height:160px;object-fit:contain;">
        <h1>Drink & Fly</h1>
        <p>Espace de connexion</p>
    </div>

    <!-- ONGLETS -->
    <div class="tabs">
        <button class="tab-btn <?= $active_tab == 'client' ? 'active' : '' ?>"
                onclick="switchTab('client', this)">
            👤 Client
        </button>
        <button class="tab-btn <?= $active_tab == 'admin' ? 'active' : '' ?>"
                onclick="switchTab('admin', this)">
            🔐 Admin
        </button>
    </div>

    <!-- FORMULAIRE CLIENT -->
    <div class="tab-content <?= $active_tab == 'client' ? 'active' : '' ?>" id="tab-client">
        <?php if($error_client): ?>
            <div class="error-box">⚠️ <?= $error_client ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="votre@email.com" required
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password_client" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login_client" class="submit-btn">Se connecter →</button>
        </form>
        <div class="links">
            Pas encore de compte ? <a href="register.php">S'inscrire</a>
        </div>
    </div>

    <!-- FORMULAIRE ADMIN -->
    <div class="tab-content <?= $active_tab == 'admin' ? 'active' : '' ?>" id="tab-admin">
        <?php if($error_admin): ?>
            <div class="error-box">⚠️ <?= $error_admin ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <input type="text" name="username" placeholder="admin" required>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password_admin" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login_admin" class="submit-btn">Se connecter →</button>
        </form>
    </div>

    
</div>

<script>
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
}
</script>

</body>
</html>