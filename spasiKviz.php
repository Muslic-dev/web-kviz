<?php
require "includes/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['naziv_kviza'])) {
    $naziv = $_POST['naziv_kviza'];
    $vrijeme = $_POST['vrijeme'];
    $broj = $_POST['broj_pitanja'];

    // 1. Ubaci kviz u bazu
    $stmt = $conn->prepare("INSERT INTO kvizovi (naziv_kviza, vremensko_ogranicenje, broj_pitanja) VALUES (?, ?, ?)");
    $stmt->execute([$naziv, $vrijeme, $broj]);
    $novi_id = $conn->lastInsertId(); // Uzimamo ID koji je baza upravo dodijelila
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <title>Dodaj Pitanja</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style2.css">
    <style>
        .q-box { background: white; padding: 20px; margin-bottom: 20px; border-radius: 10px; border-left: 5px solid #667eea; }
        input { margin-bottom: 10px; width: 100%; padding: 10px; border: 1px solid #eee; border-radius: 5px; }
    </style>
</head>
<body style="background: #f4f7fe; padding: 20px;">
    <div style="max-width: 700px; margin: 0 auto;">
        <h2>Sada unesi pitanja za: <?= htmlspecialchars($naziv) ?></h2>
        <form action="finalizirajKviz.php" method="POST">
            <input type="hidden" name="kviz_id" value="<?= $novi_id ?>">
            
            <?php for($i = 1; $i <= $broj; $i++): ?>
                <div class="q-box">
                    <strong>Pitanje <?= $i ?>:</strong>
                    <input type="text" name="pitanje[]" placeholder="Tekst pitanja" required>
                    <input type="text" name="a[]" placeholder="Opcija A" required>
                    <input type="text" name="b[]" placeholder="Opcija B" required>
                    <input type="text" name="c[]" placeholder="Opcija C" required>
                    <input type="text" name="tacan[]" placeholder="KOPIRAJ tačan odgovor ovdje" required style="background: #e8f5e9;">
                </div>
            <?php endfor; ?>
            
            <button type="submit" style="background: #27ae60; color:white; border:none; padding:15px 30px; border-radius:10px; cursor:pointer; width:100%; font-size:18px;">Sačuvaj sva pitanja</button>
        </form>
    </div>
</body>
</html>
<?php 
} else {
    header("Location: admin.php");
}
?>