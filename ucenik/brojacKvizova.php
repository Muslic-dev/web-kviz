<?php
require "../includes/connection.php"; 

if (isset($_GET['kviz_id'])) {
    $kviz_id = $_GET['kviz_id'];
    $_SESSION['trenutni_kviz_id'] = $kviz_id;
    $korisnicko_ime = $_SESSION['username'] ?? "Gost";

    try {
        // Kreiramo početni red. Kolona 'prezime' služi kao status/rezultat.
        $sql = "INSERT INTO rezultati (kviz_id, ucenik_id, vrijeme_izrade, bodovi) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$kviz_id, $_SESSION['id'], 0, 0]);

        // Preusmjeravanje na HTML fajlove kvizova
        if ($kviz_id == 1) {
            header("Location: opceZnanje.php");
        } elseif ($kviz_id == 2) {
            header("Location: tehnologija.php");
        } elseif ($kviz_id == 3) {
            header("Location: nogomet.php");
        }
        exit();
        
    } catch (PDOException $e) {
        die("Greška sa bazom: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
}
?>