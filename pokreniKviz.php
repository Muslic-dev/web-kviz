<?php
require "includes/connection.php";

// Provjera da li su stigli bodovi, max pitanja i sekunde
if (isset($_POST['score']) && isset($_POST['max']) && isset($_POST['sekunde'])) {
    
    $rezultat_tekst = $_POST['score'] . " / " . $_POST['max'];
    $sekunde = (int)$_POST['sekunde'];
    $korisnik = $_SESSION['username'] ?? "Gost";

    try {
        // Tražimo zadnji zapisani red za ovog korisnika koji je u statusu 'Započeto'
        // i upisujemo stvarni rezultat i sekunde
        $sql = "UPDATE rezultati 
                SET prezime = ?, sekunde = ? 
                WHERE ime = ? AND prezime = 'Započeto' 
                ORDER BY rezultat_id DESC LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$rezultat_tekst, $sekunde, $korisnik]);
        
        echo "Uspješno spremljeno: $rezultat_tekst za $sekunde sekundi.";
    } catch (PDOException $e) {
        echo "Greška: " . $e->getMessage();
    }
} else {
    echo "Greška: Podaci nisu primljeni.";
}
?>