<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Keuzedeel Portal</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px;
        }
        .login-container {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }
        .login-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, .98) 0%, rgba(248, 250, 252, .96) 100%);
            border: 2px solid rgba(255, 255, 255, .4);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, .25),
                0 20px 60px rgba(0, 0, 0, .2),
                inset 0 1px 0 rgba(255, 255, 255, .6);
        }
        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }
        .login-logo {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--gold-500), var(--gold-400));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 800;
            color: var(--green-950);
            box-shadow: 0 8px 24px rgba(212, 175, 55, .3);
        }
        .login-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: var(--green-950);
            margin: 0 0 8px;
            letter-spacing: -.02em;
        }
        .login-header p {
            color: var(--slate-700);
            font-size: 15px;
            margin: 0;
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-group label {
            display: block;
            font-weight: 700;
            font-size: 14px;
            color: var(--green-950);
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--slate-200);
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all .2s;
            background: var(--white);
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--gold-500);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, .1);
        }
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 32px;
        }
        .btn-login {
            flex: 1;
            padding: 14px 24px;
            border: 2px solid transparent;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all .2s;
            font-family: inherit;
        }
        .btn-login.primary {
            background: linear-gradient(135deg, var(--gold-500), var(--gold-400));
            color: var(--green-950);
            box-shadow: 0 8px 24px rgba(212, 175, 55, .2);
        }
        .btn-login.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(212, 175, 55, .3);
        }
        .btn-login.secondary {
            background: rgba(26, 122, 80, .08);
            color: var(--green-700);
            border-color: var(--green-700);
        }
        .btn-login.secondary:hover {
            background: var(--green-700);
            color: var(--white);
        }
        .login-footer {
            text-align: center;
            margin-top: 24px;
        }
        .login-footer a {
            color: var(--white);
            text-decoration: none;
            font-size: 14px;
            opacity: .9;
            padding: 8px 12px;
            border-radius: 8px;
            display: inline-block;
            transition: opacity .2s;
        }
        .login-footer a:hover {
            opacity: 1;
            background: rgba(255, 255, 255, .1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">K</div>
                <h1>Inloggen</h1>
                <p>Welkom terug bij Keuzedeel Portal</p>
            </div>

            <form method="post" action="login_handler.php">
                <div class="form-group">
                    <label for="username">Studentnummer</label>
                    <input type="text" id="username" name="username" placeholder="Vul je studentnummer in" required>
                </div>

                <div class="form-group">
                    <label for="password">Wachtwoord</label>
                    <input type="password" id="password" name="password" placeholder="Vul je wachtwoord in" required>
                </div>

                <div class="button-group">
                    <button type="submit" name="role" value="student" class="btn-login primary">Student login</button>
                    <button type="submit" name="role" value="admin" class="btn-login secondary">Admin login</button>
                </div>
            </form>
        </div>

        <div class="login-footer">
            <a href="index.php">← Terug naar home</a>
        </div>
    </div>
</body>
</html>
