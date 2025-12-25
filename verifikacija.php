<?php
require_once "includes/connection.php";
$data = $_SESSION['mail_data'];
$email = $data['email'];
$kod = $data['kod'];
if(isset($_POST['verifikuj']))
{
    $code = strtoupper($_POST['full-code']);
    $qSearch = $conn->prepare("SELECT * FROM verifikacije_naloga WHERE email = :email AND istek > CURRENT_TIMESTAMP");
    $qSearch->bindParam(':email', $email);
    $qSearch->execute();
    $results = $qSearch->fetchAll(PDO::FETCH_ASSOC);
    foreach($results as $result)
    {
        if($result && $result['token'] == $code)
        {
            header("Location: register.php");
            $_SESSION['verified_email'] = $email;
            exit();
        }
        else
        {
            echo 'Nema';
            $_SESSION['error'] = "Došlo je do greške prilikom verifikacije e-maila.";
            header("Location: index.php");
            exit();
        }
    }
}
?><!DOCTYPE html>
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

    <div class="container hero-section" style="justify-content: center; min-height: 70vh;">
         <div class="form-flipper-container">
                <div class="flipper">
                    <div class="form-container form-front">
                        <h2>Unesite kod koji ste dobili u mailu</h2>
                        <form method="POST" action="">
                            <div class="verification-code-wrapper">
                                <div class="otp-container">
                                    <input type="text" class="otp-input" maxlength="1">
                                    <input type="text" class="otp-input" maxlength="1">
                                    <input type="text" class="otp-input" maxlength="1">
                                    <span class="otp-divider">-</span>
                                    <input type="text" class="otp-input" maxlength="1">
                                    <input type="text" class="otp-input" maxlength="1">
                                    <input type="text" class="otp-input" maxlength="1">
                                </div>
                                <input type="hidden" name="full-code" id="full-code">
                            </div>
                            <button type="submit" name="verifikuj" class="btn" style="width:100%">Potvrdi</button>
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

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const hiddenInput = document.getElementById('full-code');

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length > 1) {
                    e.target.value = e.target.value.slice(0, 1);
                }

                if (e.target.value !== "" && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }

                updateHiddenInput();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === "Backspace") 
                {
                    if(e.target.value == "")
                    {
                        inputs[index - 1].focus();
                    }
                    else
                    {
                        e.target.value = "";
                    }
                }
                if(e.key === "Delete")
                {
                    if(e.target.value == "")
                    {
                        if(index < inputs.length - 1)
                        {
                            inputs[index + 1].focus();
                        }

                    }
                    else
                    {
                        e.target.value = "";
                    }
                }
                if(e.key === "ArrowLeft" && index > 0)
                {
                    inputs[index - 1].focus();
                }
                if(e.key === "ArrowRight" && index < inputs.length - 1)
                {
                    inputs[index + 1].focus();
                }
            });
        });
        inputs[0].addEventListener('paste', (e) => {
        e.preventDefault();
        const pasteData = e.clipboardData.getData('text');
        const cleanChars = pasteData.split('-');
        const pasteChars = cleanChars.join('');
        const singleChars = pasteChars.split('').slice(0, 6);
        //pasteChars = pasteChars.split('');
        singleChars.forEach((char, charIndex) => {
        if (charIndex < inputs.length) {
            inputs[charIndex].value = char;
            }
        });
            updateHiddenInput();    
        })

        function updateHiddenInput() {
            let code = "";
            inputs.forEach(input => {
                code += input.value;
            });
            hiddenInput.value = code;
        }
    </script>
    <footer>
        <div class="container">
            <p>&copy; 2025 Kvizomanija. Sva prava zadržana.</p>
        </div>
    </footer>
</body>
</html>