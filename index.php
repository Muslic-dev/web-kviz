<?php 
require "includes/connection.php";
// session_start(); 

$error = "";

// --- LOGIKA ZA PRIJAVU (ZADNJA STRANA) ---
if(isset($_POST['adminLogin'])) {
    $identity = $_POST['adm-email']; 
    $password = $_POST['adm-password'];

    if(empty($identity) || empty($password)) {
        $error = "Unesite sve podatke";
    } else {
        $qAdmin = $conn->prepare("SELECT * FROM admini WHERE email = :email LIMIT 1");
        $qAdmin->execute([':email' => $identity]);
        $admin = $qAdmin->fetch(PDO::FETCH_ASSOC);

        if($admin) {
            if(password_verify($password, $admin['sifra']) || $password == $admin['sifra']) {
                $_SESSION['logged'] = "yes";
                $_SESSION['id'] = $admin['admin_id'];
                header('Location: admin.php');
                exit();
            } else {
                $error = "Pogrešna lozinka za admina!";
            }
        } else {
            $qUser = $conn->prepare("SELECT * FROM korisnici WHERE korisnicko_ime = :uname LIMIT 1");
            $qUser->execute([':uname' => $identity]);
            $user = $qUser->fetch(PDO::FETCH_ASSOC);

            if($user) {
                if(password_verify($password, $user['sifra'])) {
                    $_SESSION['user_logged'] = true;
                    $_SESSION['username'] = $user['korisnicko_ime'];
                    header("Location: index2.php");
                    exit();
                } else {
                    $error = "Pogrešna lozinka za korisnika!";
                }
            } else {
                $error = "Nalog ne postoji!";
            }
        }
    }
}

// --- LOGIKA ZA REGISTRACIJU (PREDNJA STRANA) ---
if(isset($_POST['userLogin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $razred = $_POST['razred'];

    if(!empty($username) && !empty($password) && !empty($razred)) {
        $check = $conn->prepare("SELECT * FROM korisnici WHERE korisnicko_ime = ?");
        $check->execute([$username]);
        $userExist = $check->fetch();

        if($userExist) {
            if(password_verify($password, $userExist['sifra'])) {
                $_SESSION['user_logged'] = true;
                $_SESSION['username'] = $userExist['korisnicko_ime'];
                header("Location: index2.php");
                exit();
            } else { $error = "Zauzeto ime ili pogrešna lozinka!"; }
        } else {
            $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO korisnici (korisnicko_ime, sifra, razred, email) VALUES (?, ?, ?, ?)");
            if($insert->execute([$username, $hashed_pw, $razred, $username."@kviz.ba"])) {
                $_SESSION['user_logged'] = true;
                $_SESSION['username'] = $username;
                header("Location: index2.php");
                exit();
            }
        }
    } else { $error = "Popunite sva polja!"; }
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
            <nav>
                <ul>
                    <li><a href="index.php">Početna</a></li>
                    <li><a href="#">Kvizovi</a></li>
                    <li><a href="#">Rang Lista</a></li>
                </ul>
            </nav>
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
                                <label>Korisničko ime</label>
                                <input type="text" name="username" placeholder="npr. korisnik123" required>
                            </div>
                            <div class="input-group">
                                <label>Lozinka</label>
                                <input type="password" name="password" placeholder="Lozinka" required>
                            </div>
                            <div class="input-group">
                                <div class="dropdown-container mb-3 w-100">
                                    <div class="input-container">
                                        <label>Vaš razred</label>
                                        <input type="text" class="selected" id="razred_display_front" name="razred" placeholder="Odaberite" readonly required>
                                        <i class="bi bi-arrow-down-short" id="arrow"></i>
                                    </div>
                                    <ul class="options-container">
                                        <li class="option"><p>I</p></li>
                                        <li class="option"><p>II</p></li>
                                        <li class="option"><p>III</p></li>
                                        <li class="option"><p>IV</p></li>
                                    </ul>
                                </div>
                            </div>
                            <button type="submit" name="userLogin" class="btn" style="width:100%">Započni kviz</button>
                        </form>
                    </div>

                    <div class="form-container form-back">
                        <div class="form-nav-header">
                            <a href="#" id="show-login-form">Nova prijava</a>
                            <a href="#" class="active">Prijavi se</a>
                        </div>

                        <form method="POST" action="">
                            <div class="input-group">
                                <label>Korisničko ime / Email</label>
                                <input type="text" name="adm-email" placeholder="Unesite podatke" required>
                            </div>
                            <div class="input-group">
                                <label>Lozinka</label>
                                <input type="password" name="adm-password" placeholder="Lozinka" required>
                            </div>
                            <button type="submit" name="adminLogin" class="btn" style="width:100%">Uloguj se</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Kvizomanija. Sva prava zadržana.</p>
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

            // Dropdown logika
            document.querySelectorAll('.option').forEach(opt => {
                opt.addEventListener('click', function() {
                    const display = document.getElementById('razred_display_front');
                    if(display) display.value = this.innerText.trim();
                });
            });
        });
    </script>
</body>
</html>