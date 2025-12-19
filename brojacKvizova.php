<?php
// U connection.php obavezno mora biti session_start();
require "includes/connection.php"; 

if (isset($_GET['kviz_id'])) {
    $kviz_id = $_GET['kviz_id'];
    $korisnicko_ime = $_SESSION['username'] ?? "Gost";

    try {
        // Upisujemo početni status
        $sql = "INSERT INTO rezultati (kviz_id, ime, prezime, razred_odjeljenje) VALUES (?, ?, 'Započeto', 'Učenik')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$kviz_id, $korisnicko_ime]);

        // Preusmjeravanje na fajlove - pazi na velika/mala slova!
        if ($kviz_id == 1) {
            header("Location: opceZnanje.html");
        } elseif ($kviz_id == 2) {
            header("Location: tehnologija.html");
        } elseif ($kviz_id == 3) {
            header("Location: nogomet.html");
        }
        exit();
        
    } catch (PDOException $e) {
        die("Greška sa bazom: " . $e->getMessage());
    }
} else {
    header("Location: index2.php");
}
?>