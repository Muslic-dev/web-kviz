<?php
// 1. Putanja do konekcije
require "includes/connection.php"; 

// Provjera sesije
if (!isset($_SESSION['username'])) { 
    header("Location: index.php"); 
    exit(); 
}
$korisnik = $_SESSION['username'];

// Funkcija za status (ostaje ista)
function dohvatiStatus($conn, $kviz_id, $korisnik) {
    $stmt = $conn->prepare("SELECT prezime FROM rezultati WHERE ime = ? AND kviz_id = ? ORDER BY rezultat_id DESC LIMIT 1");
    $stmt->execute([$korisnik, $kviz_id]);
    $rez = $stmt->fetch();
    if(!$rez) return "Nije započeto";
    if($rez['prezime'] == 'Započeto') return "<span style='color:#f39c12;'>Započeto</span>";
    return "<span style='color:#27ae60;'>Rezultat: " . htmlspecialchars($rez['prezime']) . "</span>";
}

// Funkcija za Top 3
function dohvatiTopRezultate($conn, $kviz_id) {
    $stmt = $conn->prepare("
        SELECT ime, prezime, sekunde 
        FROM rezultati 
        WHERE kviz_id = ? AND prezime != 'Započeto' 
        ORDER BY 
            CAST(SUBSTRING_INDEX(prezime, ' ', 1) AS UNSIGNED) DESC, 
            sekunde ASC 
        LIMIT 3
    ");
    $stmt->execute([$kviz_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Dohvaćamo sve kvizove iz baze za prikaz
$stmt_kvizovi = $conn->query("SELECT * FROM kvizovi");
$svi_kvizovi_iz_baze = $stmt_kvizovi->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <title>Kvizomanija | Izbor kviza</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style2.css">
    <style>
        /* Tvoji stilovi ostaju identični */
        .header-actions { display: flex; align-items: center; }
        .user-info { font-weight: bold; color: #2563eb; margin-right: 15px; }
        .logout-btn { 
            background: #ef4444; color: white; text-decoration: none; 
            padding: 8px 15px; border-radius: 8px; font-size: 0.9rem; 
            transition: 0.3s; display: flex; align-items: center; gap: 5px;
        }
        .logout-btn:hover { background: #dc2626; color: white; }
        .status-info { font-size: 0.85rem; margin-top: 10px; font-weight: 600; display: block; border-top: 1px solid #eee; padding-top: 8px; }
        .stats-container { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; margin-top: 20px; }
        .stat-card { 
            background: #f8fafc; border-radius: 12px; padding: 15px; 
            min-width: 250px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
            border: 1px solid #e2e8f0; flex: 1;
        }
        .stat-card h4 { margin-top: 0; color: #1e293b; border-bottom: 2px solid #667eea; padding-bottom: 5px; font-size: 1.1rem; }
        .leaderboard-list { list-style: none; padding: 0; margin: 10px 0 0 0; text-align: left; }
        .leaderboard-list li { 
            padding: 8px 0; border-bottom: 1px solid #edf2f7; 
            display: flex; justify-content: space-between; align-items: center; font-size: 0.95rem;
        }
        .rank { font-weight: bold; color: #667eea; margin-right: 8px; }
        .score { font-weight: 700; color: #27ae60; display: block; }
        .time-label { 
            font-size: 0.8rem; 
            color: #64748b; 
            margin-left: 5px;
            font-weight: normal;
        }
        .no-data { color: #94a3b8; font-style: italic; font-size: 0.9rem; }
    </style>
</head>
<body>

<header>
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="logo">Kvizomanija</h1>
        <div class="header-actions">
            <div class="user-info">Ulogovan: <?= htmlspecialchars($korisnik) ?> 👋</div>
            <a href="index.php" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> Odjavi se
            </a>
        </div>
    </div>
</header>

<main>
    <div class="container">
        <section class="quiz-grid">
            <?php foreach ($svi_kvizovi_iz_baze as $k): 
                // Određivanje ikonice i naslova
                $id = $k['kviz_id'];
                $slika = "slike/default.png"; // Rezervna slika
                $klasa = "";
                $opis = "Testiraj svoje znanje u ovoj kategoriji.";

                if ($id == 1) {
                    $slika = "slike/einstein.png";
                    $klasa = "general-icon";
                    $opis = "Pitanja iz geografije, historije i kulture.";
                } elseif ($id == 2) {
                    $slika = "slike/technology.png";
                    $klasa = "tech-icon";
                    $opis = "Računari, internet i osnove programiranja.";
                } elseif ($id == 3) {
                    $slika = "slike/footbal.jpg";
                    $klasa = "";
                    $opis = "Sportisti, klubovi i velika takmičenja.";
                }
            ?>
                <div class="quiz-card">
                    <div class="quiz-icon <?= $klasa ?>"><img src="<?= $slika ?>" alt="Kviz"></div>
                    <h3><?= htmlspecialchars($k['naziv_kviza']) ?></h3>
                    <p><?= $opis ?></p>
                    <a href="brojacKvizova.php?kviz_id=<?= $id ?>">Započni kviz</a>
                    <span class="status-info"><?= dohvatiStatus($conn, $id, $korisnik) ?></span>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="extra-section">
            <h3>🏆 Top 3 Rezultata po Kvizovima</h3>
            <div class="stats-container">
                <?php foreach ($svi_kvizovi_iz_baze as $k): 
                    $id = $k['kviz_id'];
                    $top = dohvatiTopRezultate($conn, $id);
                ?>
                <div class="stat-card">
                    <h4><?= htmlspecialchars($k['naziv_kviza']) ?></h4>
                    <ul class="leaderboard-list">
                        <?php if (count($top) > 0): ?>
                            <?php foreach ($top as $i => $r): ?>
                            <li>
                                <span>
                                    <span class="rank"><?= $i + 1 ?>.</span> 
                                    <?= htmlspecialchars($r['ime']) ?>
                                </span>
                                <div style="text-align: right;">
                                    <span class="score"><?= htmlspecialchars($r['prezime']) ?></span>
                                    <br>
                                    <span class="time-label">
                                        <i class="bi bi-clock"></i> <?= (int)$r['sekunde'] ?>s
                                    </span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="no-data">Još nema rezultata</li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>

<footer>
    <p>&copy; 2025 Kvizomanija | Projekat za školu</p>
</footer>

</body>
</html>