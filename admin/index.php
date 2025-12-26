<?php
// 1. Putanja do konekcije
require "../includes/connection.php"; 

// Provjera sesije - samo admin može ući
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== "yes") {
    header('Location: index.php');
    exit();
}

// --- LOGIKA ZA BRISANJE KORISNIKA ---
if (isset($_POST['delete_id'])) {
    $id_za_brisanje = $_POST['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM nalozi WHERE nalog_id = ?");
        $stmt->execute([$id_za_brisanje]);
        header("Location: index.php?poruka=obrisano");
        exit();
    } catch (PDOException $e) {
        $greska_brisanje = "Greška pri brisanju: " . $e->getMessage();
    }
}

// --- LOGIKA ZA BRISANJE KVIZA ---
if (isset($_POST['delete_quiz_id'])) {
    $quiz_id = $_POST['delete_quiz_id'];
    try {
        // Brišemo kviz 
        // Napomena: Da bi ovo radilo bez greške, u bazi moraju biti podešeni ON DELETE CASCADE ključevi
        $stmt = $conn->prepare("DELETE FROM kvizovi WHERE kviz_id = ?");
        $stmt->execute([$quiz_id]);
        header("Location: admin.php?poruka=kviz_obrisan");
        exit();
    } catch (PDOException $e) {
        $greska_brisanje = "Greška pri brisanju kviza: " . $e->getMessage();
    }
}

try {
    // 2. Statistika za gornje kartice
    $br_kvizova = $conn->query("SELECT COUNT(*) FROM kvizovi")->fetchColumn();
    $br_korisnika = $conn->query("SELECT COUNT(*) FROM nalozi WHERE pristup = 'ucenik'")->fetchColumn(); 
    $br_rezultata = $conn->query("SELECT COUNT(*) FROM rezultati")->fetchColumn();
    $br_admina = $conn->query("SELECT COUNT(*) FROM nalozi WHERE pristup = 'admin'")->fetchColumn();

    // 3. Dohvaćanje registrovanih korisnika
    $sql_korisnici = "SELECT * FROM nalozi WHERE pristup = 'ucenik' ORDER BY nalog_id DESC";
    $korisnici = $conn->query($sql_korisnici)->fetchAll(PDO::FETCH_ASSOC);

    // 4. Dohvaćanje svih kvizova
    $sql_svi_kvizovi = "SELECT * FROM kvizovi ORDER BY kviz_id DESC";
    $lista_kvizova = $conn->query($sql_svi_kvizovi)->fetchAll(PDO::FETCH_ASSOC);

    // 5. Dohvaćanje zadnjih 10 aktivnosti
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
        .badge-started { background-color: #f39c12; color: white; }
        .badge-finished { background-color: #27ae60; color: white; }
        .extra-rows.collapse:not(.show) { display: none; }
        .extra-rows.show { display: table-row !important; }
    </style>
</head>
<body>

<div id="sidebar" class="d-flex flex-column p-3">
    <h3 class="text-center py-3">Kvizomanija</h3>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="admin.php" class="nav-link active"><i class="fa fa-home me-2"></i> Dashboard</a></li>
        <li><a href="dodajKviz.php" class="nav-link text-white bg-success mt-2"><i class="fa fa-plus-circle me-2"></i> KREIRAJ NOVI KVIZ</a></li>
        <hr>
        <li><a href="#tabela-kvizova" class="nav-link"><i class="fa fa-list me-2"></i> Lista Kvizova</a></li>
        <li><a href="#tabela-korisnika" class="nav-link"><i class="fa fa-users me-2"></i> Korisnici</a></li>
    </ul>
    <hr>
    <a href="../logout.php" class="btn btn-danger w-100"><i class="fa fa-sign-out-alt"></i> Odjavi se</a>
</div>

<div id="content">
    <h2 class="mb-4 text-dark">Admin Dashboard</h2>

    <?php if(isset($greska_brisanje)): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $greska_brisanje ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['poruka'])): ?>
        <?php if($_GET['poruka'] == 'obrisano'): ?>
            <div class="alert alert-success alert-dismissible fade show">Korisnik obrisan!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php elseif($_GET['poruka'] == 'kviz_obrisan'): ?>
            <div class="alert alert-warning alert-dismissible fade show">Kviz je uspješno uklonjen.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
    <?php endif; ?>
    
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card card-stat p-3 bg-white border-start border-primary border-5"><small class="text-uppercase text-muted fw-bold">Kvizova u bazi</small><h2 class="mb-0 text-primary"><?= $br_kvizova ?></h2></div></div>
        <div class="col-md-3"><div class="card card-stat p-3 bg-white border-start border-success border-5"><small class="text-uppercase text-muted fw-bold">Ukupno korisnika</small><h2 class="mb-0 text-success"><?= $br_korisnika ?></h2></div></div>
        <div class="col-md-3"><div class="card card-stat p-3 bg-white border-start border-warning border-5"><small class="text-uppercase text-muted fw-bold">Rezultata</small><h2 class="mb-0 text-warning"><?= $br_rezultata ?></h2></div></div>
        <div class="col-md-3"><div class="card card-stat p-3 bg-white border-start border-info border-5"><small class="text-uppercase text-muted fw-bold">Admina</small><h2 class="mb-0 text-info"><?= $br_admina ?></h2></div></div>
    </div>

    <div class="card shadow-sm border-0 table-card mb-4" id="tabela-kvizova">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <span><i class="fa fa-tasks me-2"></i> Upravljanje Kvizovima</span>
            <a href="dodajKviz.php" class="btn btn-sm btn-light fw-bold">+ Dodaj Novi</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>ID</th><th>Naziv Kviza</th><th>Pitanja</th><th>Vrijeme</th><th class="text-center">Akcija</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_kvizova as $kviz): ?>
                    <tr>
                        <td>#<?= $kviz['kviz_id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($kviz['naziv_kviza']) ?></td>
                        <td><?= $kviz['broj_pitanja'] ?></td>
                        <td><?= $kviz['vremensko_ogranicenje'] ?>s</td>
                        <td class="text-center">
                            <form method="POST" onsubmit="return confirm('Brisanjem kviza brišete i sve njegove rezultate i pitanja! Nastaviti?');" style="display:inline;">
                                <input type="hidden" name="delete_quiz_id" value="<?= $kviz['kviz_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Obriši</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0 table-card mb-4">
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <span><i class="fa fa-history me-2 text-warning"></i> Aktivnost uživo (Zadnjih 10)</span>
            <?php if (count($aktivnosti) > 3): ?>
            <button class="btn btn-sm btn-outline-warning" type="button" data-bs-toggle="collapse" data-bs-target=".extra-rows" onclick="this.innerText = this.innerText == 'Prikaži sve' ? 'Sakrij' : 'Prikaži sve'">Prikaži sve</button>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Korisnik</th><th>Kviz ID</th><th>Status / Rezultat</th><th>Vrijeme rada</th><th>Vrijeme započeto</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aktivnosti as $index => $a): ?>
                            <?php $rowClass = ($index > 2) ? 'extra-rows collapse' : ''; ?>
                            <tr class="<?= $rowClass ?>">
                                <td class="fw-bold text-primary"><?= htmlspecialchars($a['ime']) ?></td>
                                <td><span class="badge bg-secondary">Kviz #<?= $a['kviz_id'] ?></span></td>
                                <td>
                                    <?php if($a['prezime'] == 'Započeto'): ?>
                                        <span class="badge badge-started"><i class="fa fa-spinner fa-spin me-1"></i> Započeto</span>
                                    <?php else: ?>
                                        <span class="badge badge-finished"><i class="fa fa-check me-1"></i> <?= htmlspecialchars($a['prezime']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold"><?= ($a['prezime'] == 'Započeto') ? '---' : $a['sekunde'] . 's' ?></td>
                                <td class="small text-muted"><?= date('H:i:s d.m.Y', strtotime($a['vrijeme_zapoceto'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 table-card" id="tabela-korisnika">
        <div class="card-header bg-white py-3 fw-bold border-bottom"><i class="fa fa-users me-2 text-primary"></i> Upravljanje korisnicima</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>ID</th><th>Korisničko Ime</th><th>Email</th><th>Razred</th><th>Datum Reg.</th><th class="text-center">Akcija</th></tr>
                    </thead>
                    <tbody>
                        <?php if (count($korisnici) > 0): ?>
                            <?php foreach ($korisnici as $k): ?>
                            <tr>
                                <td>#<?= $k['nalog_id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($k['ime_prezime']) ?></td>
                                <td><?= htmlspecialchars($k['email']) ?></td>
                                <td><span class="badge bg-primary"><?= htmlspecialchars($k['razred_odjeljenje']) ?></span></td>
                                <td class="small text-muted"><?= $k['datum_registracije'] ?></td>
                                <td class="text-center">
                                    <form method="POST" onsubmit="return confirm('Da li ste sigurni da želite obrisati ovog korisnika?');" style="display:inline;">
                                        <input type="hidden" name="delete_id" value="<?= $k['nalog_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa fa-trash"></i> Obriši
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center p-4 text-muted">Nema registrovanih korisnika.</td></tr>
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