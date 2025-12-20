<?php
require "includes/connection.php";

if (isset($_POST['score']) && isset($_POST['max'])) {
    $rezultat_tekst = $_POST['score'] . " / " . $_POST['max'];
    $korisnik = $_SESSION['username'] ?? "Gost";

    try {
        // Tražimo zadnji zapisani red za ovog korisnika koji još nema upisane bodove
        // i mijenjamo 'Započeto' (kolona prezime) u stvarni rezultat
        $sql = "UPDATE rezultati 
                SET prezime = ? 
                WHERE ime = ? AND prezime = 'Započeto' 
                ORDER BY rezultat_id DESC LIMIT 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([$rezultat_tekst, $korisnik]);
        
        echo "Uspješno spašeno";
    } catch (PDOException $e) {
        echo "Greška: " . $e->getMessage();
    }
}
?>