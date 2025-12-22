<?php
require "includes/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['naziv_kviza'])) {
    $naziv = $_POST['naziv_kviza'];
    $vrijeme = $_POST['vrijeme'];
    $broj = $_POST['broj_pitanja'];
    
    // LOGIKA ZA SLIKU
    $ime_slike = "default.jpg"; // Ako korisnik ne učita ništa

    if (isset($_FILES['kviz_slika']) && $_FILES['kviz_slika']['error'] == 0) {
        $upload_direktorij = "uploads/";
        
        // Uzimamo ekstenziju fajla (npr .jpg)
        $ekstenzija = pathinfo($_FILES["kviz_slika"]["name"], PATHINFO_EXTENSION);
        
        // Pravimo novo unikatno ime: npr. slika_658421.jpg
        $novo_ime = "slika_" . time() . "_" . uniqid() . "." . $ekstenzija;
        $putanja_do_fajla = $upload_direktorij . $novo_ime;

        // Premještamo sliku iz privremene memorije u naš folder
        if (move_uploaded_file($_FILES["kviz_slika"]["tmp_name"], $putanja_do_fajla)) {
            $ime_slike = $novo_ime;
        }
    }

    // 1. Ubaci kviz u bazu (UKLJUČUJUĆI I KOLONU SLIKA)
    // NAPOMENA: Provjeri u bazi da li ti se kolona zove 'slika'
    $stmt = $conn->prepare("INSERT INTO kvizovi (naziv_kviza, slika, vremensko_ogranicenje, broj_pitanja) VALUES (?, ?, ?, ?)");
    $stmt->execute([$naziv, $ime_slike, $vrijeme, $broj]);
    $novi_id = $conn->lastInsertId(); 
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <title>Dodaj Pitanja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { background: #f4f7fe; padding: 20px; font-family: 'Poppins', sans-serif; }
        .q-box { background: white; padding: 20px; margin-bottom: 20px; border-radius: 10px; border-left: 5px solid #667eea; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        input { margin-bottom: 10px; width: 100%; padding: 10px; border: 1px solid #eee; border-radius: 5px; box-sizing: border-box; }
        h2 { color: #2c3e50; margin-bottom: 30px; }
        button { background: #27ae60; color:white; border:none; padding:15px 30px; border-radius:10px; cursor:pointer; width:100%; font-size:18px; transition: 0.3s; }
        button:hover { background: #219150; }
    </style>
</head>
<body>
    <div style="max-width: 700px; margin: 0 auto;">
        <h2>Sada unesi pitanja za: <?= htmlspecialchars($naziv) ?></h2>
        
        <div class="mb-4 text-center">
            <img src="uploads/<?= $ime_slike ?>" style="max-width: 200px; border-radius: 10px; margin-bottom: 20px;">
        </div>

        <form action="finalizirajKviz.php" method="POST">
            <input type="hidden" name="kviz_id" value="<?= $novi_id ?>">
            
            <?php for($i = 1; $i <= $broj; $i++): ?>
                <div class="q-box">
                    <strong>Pitanje <?= $i ?>:</strong>
                    <input type="text" name="pitanje[]" placeholder="Tekst pitanja" required>
                    <input type="text" name="a[]" placeholder="Opcija A" required>
                    <input type="text" name="b[]" placeholder="Opcija B" required>
                    <input type="text" name="c[]" placeholder="Opcija C" required>
                    <input type="text" name="tacan[]" placeholder="KOPIRAJ tačan odgovor ovdje" required style="background: #e8f5e9; border: 1px solid #c8e6c9;">
                </div>
            <?php endfor; ?>
            
            <button type="submit">Sačuvaj sva pitanja</button>
        </form>
    </div>
</body>
</html>
<?php 
} else {
    header("Location: admin.php");
}
?>