<?php
require "includes/connection.php"; 

if (isset($_GET['kviz_id'])) {
    $kviz_id = $_GET['kviz_id'];
    $korisnicko_ime = $_SESSION['username'] ?? "Gost";

    try {
        // Kreiramo početni red. Kolona 'prezime' služi kao status/rezultat.
        $sql = "INSERT INTO rezultati (kviz_id, ime, prezime, razred_odjeljenje, sekunde) 
                VALUES (?, ?, 'Započeto', 'Učen', 0)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$kviz_id, $korisnicko_ime]);

        // Preusmjeravanje na HTML fajlove kvizova
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