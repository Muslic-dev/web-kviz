<?php
require "includes/connection.php";
// session_start(); // Mora biti u connection.php, ako nije, otkomentariši

$korisnik = $_SESSION['username'] ?? "Gost";

// Funkcija za provjeru statusa kviza
function dohvatiStatus($conn, $kviz_id, $korisnik) {
    if($korisnik == "Gost") return "Prijavite se za igru";
    
    $stmt = $conn->prepare("SELECT prezime FROM rezultati WHERE ime = ? AND kviz_id = ? ORDER BY rezultat_id DESC LIMIT 1");
    $stmt->execute([$korisnik, $kviz_id]);
    $rez = $stmt->fetch();
    
    if(!$rez) return "Nije započeto";
    if($rez['prezime'] == 'Započeto') return "<span style='color:#f39c12;'><i class='bi bi-hourglass-split'></i> Započeto</span>";
    return "<span style='color:#27ae60;'><i class='bi bi-check-all'></i> Rezultat: " . htmlspecialchars($rez['prezime']) . "</span>";
}
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <title>Kvizomanija | Izbor kviza</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style2.css">
    <style>
        .status-info { font-size: 0.85rem; margin-top: 10px; font-weight: 600; display: block; border-top: 1px solid #eee; padding-top: 8px; }
        .user-info { font-weight: bold; color: #2563eb; }
    </style>
</head>
<body>

<header>
    <div class="container">
        <h1 class="logo">Kvizomanija</h1>
        <div class="user-info">Ulogovan: <?= htmlspecialchars($korisnik) ?> 👋</div>
    </div>
</header>

<main>
    <div class="container">
        <section class="quiz-grid">
            <div class="quiz-card">
                <div class="quiz-icon general-icon"><img src="slike/einstein.png" alt="Opće"></div>
                <h3>Opće znanje</h3>
                <p>Pitanja iz geografije, historije i kulture.</p>
                <a href="brojacKvizova.php?kviz_id=1">Započni kviz</a>
                <span class="status-info"><?= dohvatiStatus($conn, 1, $korisnik) ?></span>
            </div>

            <div class="quiz-card">
                <div class="quiz-icon tech-icon"><img src="slike/technology.png" alt="IT"></div>
                <h3>IT & Tehnologija</h3>
                <p>Računari, internet i osnove programiranja.</p>
                <a href="brojacKvizova.php?kviz_id=2">Započni kviz</a>
                <span class="status-info"><?= dohvatiStatus($conn, 2, $korisnik) ?></span>
            </div>

            <div class="quiz-card">
                <div class="quiz-icon"><img src="slike/footbal.jpg" alt="Sport"></div>
                <h3>Sport</h3>
                <p>Sportisti, klubovi i velika takmičenja.</p>
                <a href="brojacKvizova.php?kviz_id=3">Započni kviz</a>
                <span class="status-info"><?= dohvatiStatus($conn, 3, $korisnik) ?></span>
            </div>
        </section>

        <section class="extra-section">
            <h3>Tvoja statistika</h3>
            <div class="stats">
                <div class="stat-box">
                    <span>-</span>
                    <p>Uskoro detaljnije</p>
                </div>
            </div>
        </section>
    </div>
</main>

<footer>
    <p>&copy; 2025 Kvizomanija | Projekat za školu</p>
</footer>

</body>
</html>