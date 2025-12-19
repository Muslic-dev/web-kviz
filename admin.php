<?php
// 1. Putanja do konekcije (session_start() mora biti unutra ili ovdje)
require "includes/connection.php"; 

// Provjera sesije - samo admin može ući
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== "yes") {
    header('Location: index.php');
    exit();
}

try {
    // 2. Statistika za gornje kartice
    $br_kvizova = $conn->query("SELECT COUNT(*) FROM kvizovi")->fetchColumn();
    $br_pitanja = $conn->query("SELECT COUNT(*) FROM pitanja")->fetchColumn();
    $br_rezultata = $conn->query("SELECT COUNT(*) FROM rezultati")->fetchColumn();
    $br_admina = $conn->query("SELECT COUNT(*) FROM admini")->fetchColumn();

    // 3. Dohvaćanje registrovanih korisnika
    $sql_korisnici = "SELECT * FROM korisnici ORDER BY korisnik_id DESC";
    $korisnici = $conn->query($sql_korisnici)->fetchAll(PDO::FETCH_ASSOC);

    // 4. Dohvaćanje zadnjih 10 aktivnosti (Vrijeme sada vučemo iz baze!)
    $sql_aktivnost = "SELECT * FROM rezultati ORDER BY rezultat_id DESC LIMIT 10";
    $aktivnosti = $conn->query($sql_aktivnost)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Greška sa bazom: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <title>Kvizomanija | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-width: 250px; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        #sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: #2c3e50; color: white; z-index: 1000; }
        #content { margin-left: var(--sidebar-width); padding: 30px; }
        .nav-link { color: #bdc3c7; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { color: white; background: #34495e; }
        .card-stat { border: none; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: 0.3s; }
        .card-stat:hover { transform: translateY(-5px); }
        .table-card { border-radius: 12px; overflow: hidden; margin-bottom: 30px; }
        .badge-started { background-color: #f39c12; color: white; } /* Narandžasta za Započeto */
        .badge-finished { background-color: #27ae60; color: white; } /* Zelena za bodove */
    </style>
</head>
<body>

<div id="sidebar" class="d-flex flex-column p-3">
    <h3 class="text-center py-3">Kvizomanija</h3>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="admin.php" class="nav-link active"><i class="fa fa-home me-2"></i> Dashboard</a></li>
        <li><a href="#" class="nav-link"><i class="fa fa-list me-2"></i> Kvizovi</a></li>
        <li><a href="#" class="nav-link"><i class="fa fa-plus me-2"></i> Dodaj Pitanje</a></li>
        <li><a href="#" class="nav-link"><i class="fa fa-users me-2"></i> Korisnici</a></li>
    </ul>
    <hr>
    <a href="index.php" class="btn btn-danger w-100"><i class="fa fa-sign-out-alt"></i> Odjavi se</a>
</div>

<div id="content">
    <h2 class="mb-4 text-dark">Dobrodošli u Admin Panel</h2>
    
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-white border-start border-primary border-5">
                <small class="text-uppercase text-muted fw-bold">Kvizova u bazi</small>
                <h2 class="mb-0 text-primary"><?= $br_kvizova ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-white border-start border-success border-5">
                <small class="text-uppercase text-muted fw-bold">Ukupno pitanja</small>
                <h2 class="mb-0 text-success"><?= $br_pitanja ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-white border-start border-warning border-5">
                <small class="text-uppercase text-muted fw-bold">Rezultata</small>
                <h2 class="mb-0 text-warning"><?= $br_rezultata ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-stat p-3 bg-white border-start border-info border-5">
                <small class="text-uppercase text-muted fw-bold">Admina</small>
                <h2 class="mb-0 text-info"><?= $br_admina ?></h2>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 table-card mb-4">
        <div class="card-header bg-dark text-white py-3 fw-bold">
            <i class="fa fa-history me-2 text-warning"></i> Aktivnost uživo (Učenici na kvizovima)
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Korisnik</th>
                            <th>Kviz ID</th>
                            <th>Status / Rezultat</th>
                            <th>Vrijeme započeto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($aktivnosti) > 0): ?>
                            <?php foreach ($aktivnosti as $a): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($a['ime']) ?></td>
                                <td><span class="badge bg-secondary">Kviz #<?= $a['kviz_id'] ?></span></td>
                                <td>
                                    <?php if($a['prezime'] == 'Započeto'): ?>
                                        <span class="badge badge-started"><i class="fa fa-spinner fa-spin me-1"></i> Započeto</span>
                                    <?php else: ?>
                                        <span class="badge badge-finished"><i class="fa fa-check me-1"></i> <?= htmlspecialchars($a['prezime']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted">
                                    <?= isset($a['vrijeme_zapoceto']) ? date('H:i:s d.m.Y', strtotime($a['vrijeme_zapoceto'])) : 'Nema podatka' ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center p-3 text-muted">Nema zapisa o aktivnostima.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 table-card">
        <div class="card-header bg-white py-3 fw-bold border-bottom">
            <i class="fa fa-users me-2 text-primary"></i> Registrovani korisnici (učenici)
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Korisničko Ime</th>
                            <th>Email</th>
                            <th>Razred</th>
                            <th>Datum Reg.</th>
                            <th class="text-center">Akcija</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($korisnici) > 0): ?>
                            <?php foreach ($korisnici as $k): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($k['korisnicko_ime']) ?></td>
                                <td><?= htmlspecialchars($k['email']) ?></td>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($k['razred']) ?></span></td>
                                <td class="small text-muted"><?= $k['datum_registracije'] ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center p-4 text-muted">Još uvijek nema registrovanih korisnika.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>