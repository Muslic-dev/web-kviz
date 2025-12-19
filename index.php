<?php 
require "includes/connection.php";

if(isset($_POST['adminLogin']))
{
    $email = $_POST['adm-email'];
    $password = $_POST['adm-password'];
    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        echo"Unesite pravilan email";
    }
    elseif(empty($email) || empty($password))
    {
        echo"Unesite sve podatke";
    }
    else 
    {
        $qLogin = $conn->prepare("SELECT * FROM admini WHERE email = :email LIMIT 1");
        $qLogin->bindparam(":email", $email);
        $qLogin->execute();
        $row = $qLogin->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($row))
        {
            foreach($row as $result)
            {
                if(password_verify($password, $result['sifra']) || $password == $result['sifra'])
                {
                    $_SESSION['logged'] = "yes";
                    $_SESSION['id'] = $result['admin_id'];
                    header('Location: admin.php');
                }
                else
                {
                    echo "Unijeli ste netacan password";
                }
            }
        }
        else
        {
            echo"Ne postoji admin s tim emailom";
        }
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
    <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"/>-->
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

    <header>
        <div class="container">
            <h1 class="logo">Kvizomanija</h1>
            <nav>
                <ul>
                    <li><a href="#">Početna</a></li>
                    <li><a href="#">Kvizovi</a></li>
                    <li><a href="#">Rang Lista</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container hero-section">
            <!-- Lijeva strana sa sadržajem -->
            <div class="welcome-content">
                <h2>Testirajte Svoje Znanje!</h2>
                <p>Pridružite se našoj zajednici i takmičite se u uzbudljivim kvizovima. Pokažite šta znate i osvojite prvo mjesto na rang listi!</p>
                <ul>
                    <li>✓ Tri različite kategorije kvizova</li>
                    <li>✓ 15 izazovnih pitanja po kvizu</li>
                    <li>✓ Vremenski ograničeni odgovori za dodatni adrenalin</li>
                    <li>✓ Pratite svoj napredak i uporedite se s drugima</li>
                </ul>
            </div>

            <!-- Desna strana sa formom koja se okreće -->
            <div class="form-flipper-container">
                <div class="flipper">
                    <!-- PREDNJA STRANA - LOGIN -->
                    <div class="form-container form-front">
                        <form id="loginForm">
                            <h3>Prijavite se</h3>
                            
                            <div class="input-group">
                                <label for="username">Korisničko ime</label>
                                <input type="text" id="username" name="username" placeholder="npr. korisnik123" required>
                            </div>
                            
                            <div class="input-group">
                                <label for="password">Lozinka</label>
                                <input type="password" id="password" name="password" placeholder="Unesite vašu lozinku" required>
                            </div>
                            
                            <div class="input-group">
                                <div class="dropdown-container mb-3 w-100">
                                    <div class="input-container">
                                        <label for="Razred">Razred</label>
                                        <input type="text" class="selected" id="razred" name="razred" placeholder="Razred" readonly required>
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


                            <button type="submit" class="btn">Uloguj se</button>

                            <p class="form-switch-link">
                                Nemate račun? <a href="#" id="show-register-form">Admin login</a>
                            </p>
                        </form>
                    </div>

                    <!-- ZADNJA STRANA - REGISTRACIJA -->
                    <div class="form-container form-back">
                        <form id="registerForm" method="POST">
                            <h3>Admin Login</h3>
                            
                            <div class="input-group">
                                <label for="reg-email">Email</label>
                                <input type="text" id="adm-email" name="adm-email" >
                            </div>

                            <div class="input-group">
                                <label for="reg-password">Lozinka</label>
                                <input type="password" id="adm-password" name="adm-password" >
                            </div>
                            
                            <button type="submit" class="btn" id="adminLogin" name="adminLogin">Login</button>

                            <p class="form-switch-link">
                                Već imate račun? <a href="#" id="show-login-form">Prijavite se</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Kvizomanija. Sva prava zadržana. Projekat za školu.</p>
        </div>
    </footer>

    <script>
document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();
    window.location.href = "index2.html";
});
</script>

<script src="dropdown.js"></script>

    <!-- JavaScript za flip efekat -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flipper = document.querySelector('.flipper');
            const showRegisterLink = document.querySelector('#show-register-form');
            const showLoginLink = document.querySelector('#show-login-form');

            showRegisterLink.addEventListener('click', function(e) {
                e.preventDefault();
                flipper.classList.add('is-flipped');
            });

            showLoginLink.addEventListener('click', function(e) {
                e.preventDefault();
                flipper.classList.remove('is-flipped');
            });
        });
    </script>

</body>
</html>