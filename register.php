<?php
require_once "includes/connection.php";
if(isset($_POST['register']))
{
    $data = $_SESSION['mail_data'];
    $email = $data['email'];
    $kod = $data['kod'];
    $ime_prezime = $_POST['ime_prezime'];
    $lozinka = $_POST['lozinka'];
    $potvrdi_lozinku = $_POST['potvrdi_lozinku'];
    $razred_odjeljenje = $_POST['razred'].$_POST['odjeljenje'];

    if(empty($ime_prezime) || empty($lozinka) || empty($potvrdi_lozinku) || empty($_POST['razred']) || empty($_POST['odjeljenje']))
    {
        $_SESSION['error'] = "Sva polja su obavezna.";
        header("Location: register.php");
        exit();
    }
    else
    {
        if($lozinka !== $potvrdi_lozinku)
        {
            $_SESSION['error'] = "Lozinke se ne poklapaju.";
            header("Location: register.php");
            exit();
        }
        else
        {
            if(strlen($lozinka) < 6)
            {
                $_SESSION['error'] = "Lozinka mora imati najmanje 6 karaktera.";
                header("Location: register.php");
                exit();
            }
            else
            {
                $qSearch = $conn->prepare("SELECT * FROM nalozi WHERE email = :email");
                $qSearch->bindParam(':email', $email);
                $qSearch->execute();
                if($qSearch->rowCount() > 0)
                {
                    $_SESSION['error'] = "Nalog sa ovom email adresom već postoji.";
                    header("Location: register.php");
                    exit();
                }
                else
                {
                    $qSearch = $conn->prepare("SELECT * FROM verifikacije_naloga WHERE email = :email AND token = :token AND istek < CURRENT_TIMESTAMP");
                    $qSearch->bindParam(':email', $email);
                    $qSearch->bindParam(':token', $kod);
                    $qSearch->execute();
                    if($qSearch->rowCount() > 0)
                    {
                        $_SESSION['error'] = "Verifikacioni kod je istekao. Molimo registrujte se ponovo.";
                        header("Location: index.php");
                        exit();
                    }
                    else
                    {
                        $hashedPassword = password_hash($lozinka, PASSWORD_DEFAULT);
                        $qInsert = $conn->prepare("INSERT INTO nalozi (email, sifra, ime_prezime, razred_odjeljenje, pristup) VALUES (:email, :lozinka, :ime_prezime, :razred_odjeljenje, 'ucenik')");
                        $qInsert->bindParam(':email', $email);
                        $qInsert->bindParam(':lozinka', $hashedPassword);
                        $qInsert->bindParam(':ime_prezime', $ime_prezime);
                        $qInsert->bindParam(':razred_odjeljenje', $razred_odjeljenje);
                        $qInsert->execute();

                        $qDelete = $conn->prepare("DELETE FROM verifikacije_naloga WHERE email = :email");
                        $qDelete->bindParam(':email', $email);
                        $qDelete->execute();

                        $_SESSION['success'] = "Registracija uspješna. Sada se možete prijaviti.";
                        header("Location: index.php");
                        exit();
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

    <header>
        <div class="container">
            <h1 class="logo">Kvizomanija</h1>
        </div>
    </header>

    <div class="container hero-section" style="justify-content: center; min-height: 70vh; flex-direction: column;"> 
        <?php
        if(isset($_SESSION['error'])){
        echo '<div class="error-box" style="margin-bottom: 0px;"><i class="bi bi-exclamation-triangle"></i> ' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
        }?>
         <div class="form-flipper-container">
                <div class="flipper">
                    <div class="form-container form-front" style="height:610px;">
                        <form method="POST" action="">
                            <div class="input-group">
                                <label>Ime i Prezime</label>
                                <input type="text" name="ime_prezime" placeholder = "Ime Prezime">
                            </div>
                            <div class="input-group">
                                <label>Lozinka</label>
                                <input type="password" name="lozinka" placeholder = "Lozinka">
                            </div>
                            <div class="input-group">
                                <label>Potvrdi lozinku</label>
                                <input type="password" name="potvrdi_lozinku" placeholder = "Potvrdi lozinku">
                            </div>
                            <div class="input-group">
                                <div class="dropdown-container">
                                    <div class="input-container">
                                        <label for="Razred">Razred</label>
                                        <input type="text" class="selected" id="razred" name="razred" placeholder="Odaberi" readonly required>
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
                            <div class="input-group">
                                <label>Odjeljenje</label>
                                <input type="number" name="odjeljenje" min="1" max="5" placeholder="Unesi">
                            </div>
                            <button type="submit" name="register" class="btn" style="width:100%">Potvrdi</button>
                        </form>
                    </div>
                </div>
        </div>
    </div>
    <script src="dropdown.js"></script>
    <footer>
        <div class="container">
            <p>&copy; 2025 Kvizomanija. Sva prava zadržana.</p>
        </div>
    </footer>
</body>
</html>