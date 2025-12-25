<?php 
require "includes/connection.php";

$error = "";
if(isset($_SESSION['error']))
{
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
$success = "";

if(isset($_SESSION['success']))
{
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// --- LOGIKA ZA PRIJAVU (ZADNJA STRANA) ---
if(isset($_POST['login'])) 
{
    $email = $_POST['login-email']; 
    $password = $_POST['login-password'];

    if(empty($email) || empty($password)) {
        $error = "Unesite sve podatke";
    } else {
        $qLogin = $conn->prepare("SELECT * FROM nalozi WHERE email = :email LIMIT 1");
        $qLogin->execute([':email' => $email]);
        $account = $qLogin->fetch(PDO::FETCH_ASSOC);

        if($account) {
            if(password_verify($password, $account['sifra'])) {
                $_SESSION['logged'] = "yes";
                $_SESSION['id'] = $account['nalog_id'];
                $_SESSION['pristup'] = $account['pristup'];
                header('Location: ' . $_SESSION['pristup'] . '/index.php');
                exit();
            } else {
                $error = "Pogrešna lozinka!";
            }
        }
        else 
        {
        $error = "Nalog ne postoji!";
        }
    } 
}

// --- LOGIKA ZA REGISTRACIJU (PREDNJA STRANA) ---
if(isset($_POST['register'])) {
    $email = $_POST['email-register'];
    if(!empty($email))
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            $error = "Neispravan format email adrese!";
        }
        else
        {
            $check = $conn->prepare("SELECT * FROM nalozi WHERE email = ?");
            $check->execute([$email]);
            $userExist = $check->fetch();
            if($userExist)
            {
                $error = "Mail je već u upotrebi!";
            }
            else
            {
                $characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $token = "";
                for($i = 0; $i < 6; $i++) 
                {
                    $token .= $characters[rand(0, strlen($characters) - 1)];
                }
                $qInsert = $conn->prepare("INSERT INTO verifikacije_naloga (email, token, istek) VALUES (:email, :token, (CURRENT_TIMESTAMP + INTERVAL 15 MINUTE))");
                $qInsert->bindParam(':email', $email);
                $qInsert->bindParam(':token', $token);
                $qInsert->execute();
                $_SESSION['mail_data'] = [
                    'email' => $email,
                    'kod' => $token,
                    'purpose' => 'register',
                    'vrijeme' => time()
                ];
                header("Location: slanjeMaila.php");
            }
        }
    }
    else 
    {
    $error = "Email ne može biti prazan!";
    }
}
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kvizomanija - Dobrodošli!</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="dropdown.css">
    <style>
        .error-box { background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #fecaca; text-align: center; }
        .success-box { background: #fee2e2; color: #1cb953ff; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #fecaca; text-align: center; }
        
        /* NOVI DIZAJN: GORNJA NAVIGACIJA UNUTAR FORME */
        .form-nav-header {
            display: flex;
            background: #f1f5f9;
            border-radius: 12px;
            padding: 5px;
            margin-bottom: 25px;
        }
        .form-nav-header a {
            flex: 1;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 8px;
            transition: 0.3s;
        }
        .form-nav-header a.active {
            background: #ffffff;
            color: #2563eb;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .form-nav-header a:hover:not(.active) {
            color: #334155;
            background: #e2e8f0;
        }
    </style>
</head>
<body>

    <header>
        <div class="container">
            <h1 class="logo">Kvizomanija</h1>
        </div>
    </header>

    <main>
        <div class="container hero-section">
            <div class="welcome-content">
                <h2>Testirajte Svoje Znanje!</h2>
                <p>Pridružite se zajednici i pokažite šta znate u uzbudljivim kvizovima.</p>
                
                <?php if($error != ""): ?>
                    <div class="error-box"><i class="bi bi-exclamation-triangle"></i> <?= $error ?></div>
                <?php endif; ?>
                                
                <?php if($success != ""): ?>
                    <div class="success-box"><i class="bi bi-check-circle"></i> <?= $success ?></div>
                <?php endif; ?>

                <ul>
                    <li>✓ 15 izazovnih pitanja po kvizu</li>
                    <li>✓ Tri različite kategorije</li>
                    <li>✓ Pratite svoj napredak na tabeli</li>
                </ul>
            </div>

            <div class="form-flipper-container">
                <div class="flipper">
                    <div class="form-container form-front">
                        <div class="form-nav-header">
                            <a href="#" class="active">Nova prijava</a>
                            <a href="#" id="show-register-form">Već imam nalog</a>
                        </div>

                        <form method="POST" action="">
                            <div class="input-group">
                                <label>Email</label>
                                <input type="text" name="email-register" placeholder="email@email.com">
                            </div>
                            <button type="submit" name="register" class="btn" style="width:100%">Registruj se</button>
                        </form>
                    </div>

                    <div class="form-container form-back">
                        <div class="form-nav-header">
                            <a href="#" id="show-login-form">Nova prijava</a>
                            <a href="#" class="active">Prijavi se</a>
                        </div>

                        <form method="POST" action="">
                            <div class="input-group">
                                <label>Korisničko ime</label>
                                <input type="text" name="login-email" placeholder="Unesite podatke" >
                            </div>
                            <div class="input-group">
                                <label>Lozinka</label>
                                <input type="password" name="login-password" placeholder="Lozinka">
                            </div>
                            <button type="submit" name="login" class="btn" style="width:100%">Uloguj se</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 Kvizomanija. Sva prava zadržana.</p>
        </div>
    </footer>

    <script src="dropdown.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flipper = document.querySelector('.flipper');
            
            // Dugmad za prebacivanje (switch) unutar headera forme
            const toBack = document.querySelector('#show-register-form');
            const toFront = document.querySelector('#show-login-form');

            toBack.addEventListener('click', (e) => {
                e.preventDefault();
                flipper.classList.add('is-flipped');
            });

            toFront.addEventListener('click', (e) => {
                e.preventDefault();
                flipper.classList.remove('is-flipped');
            });
        });
    </script>
</body>
</html>