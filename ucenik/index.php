<?php
require "../includes/connection.php"; 

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== 'yes' || $_SESSION['pristup'] !== 'ucenik') 
{ 
    header("Location: index.php"); 
    exit(); 
}
$korisnik = $_SESSION['username'];

function dohvatiStatus($conn, $kviz_id, $korisnik) {
    $stmt = $conn->prepare("SELECT prezime FROM rezultati WHERE ime = ? AND kviz_id = ? ORDER BY rezultat_id DESC LIMIT 1");
    $stmt->execute([$korisnik, $kviz_id]);
    $rez = $stmt->fetch();
    if(!$rez) return "Nije započeto";
    //if($rez['prezime'] == 'Započeto') return "<span style='color:#f39c12;'>Započeto</span>";
    return "<span style='color:#27ae60;'>Rezultat: " . htmlspecialchars($rez['prezime']) . "</span>";
}

function dohvatiTopRezultate($conn, $kviz_id) {
    $stmt = $conn->prepare("
        SELECT ime_prezime, vrijeme_izrade, bodovi
        FROM rezultati, nalozi
        WHERE kviz_id = ? AND  rezultati.ucenik_id = nalozi.nalog_id 
        ORDER BY 
            CAST(SUBSTRING_INDEX(bodovi, ' ', 1) AS UNSIGNED) DESC, 
            vrijeme_izrade ASC 
        LIMIT 3
    ");
    $stmt->execute([$kviz_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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
    <link rel="stylesheet" href="../style2.css">
    <style>
        .header-actions { display: flex; align-items: center; }
        .user-info { font-weight: bold; color: #2563eb; margin-right: 15px; }
        .logout-btn { 
            background: #ef4444; color: white; text-decoration: none; 
            padding: 8px 15px; border-radius: 8px; font-size: 0.9rem; 
            transition: 0.3s; display: flex; align-items: center; gap: 5px;
        }
        .logout-btn:hover { background: #dc2626; color: white; }

        /* POPRAVLJENI KONTEJNER ZA SLIKE */
        .quiz-icon {
            width: 100%;
            height: 140px;      /* Optimalna visina */
            margin: 0 0 15px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: transparent; /* Uklonjena pozadina da se bolje uklopi */
        }

        .quiz-icon img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain; /* KLJUČNO: Cijela slika će se vidjeti bez odsijecanja */
            transition: transform 0.3s ease;
        }

        /* GRID SA TAČNO 3 STAVKE */
        .quiz-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 50px;
        }

        @media (max-width: 992px) { .quiz-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .quiz-grid { grid-template-columns: 1fr; } }

        .quiz-card {
            text-align: center;
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: 0.3s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .quiz-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .quiz-card:hover img { transform: scale(1.05); }

        .status-info { font-size: 0.85rem; margin-top: 10px; font-weight: 600; display: block; border-top: 1px solid #eee; padding-top: 10px; }
        
        .stats-container { display: flex; gap: 20px; flex-wrap: wrap; justify-content: center; margin-top: 20px; }
        .stat-card { 
            background: #f8fafc; border-radius: 12px; padding: 15px; 
            min-width: 280px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
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
        .time-label { font-size: 0.8rem; color: #64748b; margin-left: 5px; font-weight: normal; }
        .no-data { color: #94a3b8; font-style: italic; font-size: 0.9rem; }
    </style>
</head>
<body>

<header>
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="logo">Kvizomanija</h1>
        <div class="header-actions">
            <div class="user-info">Ulogovan: <?= htmlspecialchars($korisnik) ?> 👋</div>
            <a href="../logout.php" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> Odjavi se
            </a>
        </div>
    </div>
</header>

<main>
    <div class="container">
        <section class="quiz-grid">
            <?php foreach ($svi_kvizovi_iz_baze as $k): 
                $id = $k['kviz_id'];
                
                if (!empty($k['slika']) && file_exists("../uploads/" . $k['slika'])) {
                    $slika_putanja = "../uploads/" . $k['slika'];
                } else {
                    if ($id == 1) $slika_putanja = "../slike/einstein.png";
                    elseif ($id == 2) $slika_putanja = "../slike/technology.png";
                    elseif ($id == 3) $slika_putanja = "../slike/footbal.jpg";
                    else $slika_putanja = "../slike/default.png";
                }

                $opis = "Testiraj svoje znanje u ovoj kategoriji.";
                if ($id == 1) $opis = "Pitanja iz geografije, historije i kulture.";
                elseif ($id == 2) $opis = "Računari, internet i osnove programiranja.";
                elseif ($id == 3) $opis = "Sportisti, klubovi i velika takmičenja.";
            ?>
                <div class="quiz-card">
                    <div>
                        <div class="quiz-icon">
                            <img src="<?= $slika_putanja ?>" alt="Kviz">
                        </div>
                        <h3 style="margin-bottom: 10px; color: #1e293b;"><?= htmlspecialchars($k['naziv_kviza']) ?></h3>
                        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px; line-height: 1.4;"><?= $opis ?></p>
                    </div>
                    <div>
                        <a href="brojacKvizova.php?kviz_id=<?= $id ?>" class="play-btn" style="text-decoration: none; background: #667eea; color: white; padding: 12px 25px; border-radius: 10px; display: inline-block; width: 100%; box-sizing: border-box; font-weight: 600; transition: 0.3s;">Započni kviz</a>
                        <!-- <span class="status-info">// dohvatiStatus($conn, $id, $korisnik)</span> -->
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="extra-section">
            <h3 style="margin-top: 40px; text-align: center; color: #1e293b;">🏆 Top 3 Rezultata po Kvizovima</h3>
            <div class="stats-container">
                <?php

                foreach ($svi_kvizovi_iz_baze as $k): 
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
                                    <?= htmlspecialchars($r['ime_prezime']) ?>
                                </span>
                                <div style="text-align: right;">
                                    <span class="score"><?= htmlspecialchars($r['bodovi']) ?></span>
                                    <span class="time-label"><i class="bi bi-clock"></i> <?= (int)$r['vrijeme_izrade'] ?>s</span>
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