<?php
require "includes/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id']; // ID ulogovanog korisnika
    $kviz_id = $_SESSION['trenutni_kviz_id']; // Nadam se da ovo čuvaš u sesiji
    
    $bodovi_str = $_POST['bodovi'];// npr "15/20"
    $vrijeme = $_POST['vrijeme_izrade'];

    $stmt = $conn->prepare("UPDATE rezultati SET bodovi = ?, vrijeme_izrade = ? WHERE ucenik_id = ? AND kviz_id = ?");
    if($stmt->execute([$bodovi_str, $vrijeme, $user_id, $kviz_id])) {
        unset($_SESSION['trenutni_kviz_id']);
        echo "Rezultat je uspješno upisan!";
    } else {
        echo "Greška pri upisu u bazu.";
    }
}