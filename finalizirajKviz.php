<?php
require "../includes/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kviz_id = $_POST['kviz_id'];
    $pitanja = $_POST['pitanje'];
    $opcije_a = $_POST['a'];
    $opcije_b = $_POST['b'];
    $opcije_c = $_POST['c'];
    $tacni = $_POST['tacan'];

    $stmt = $conn->prepare("INSERT INTO pitanja (kviz_id, tekst_pitanja, opcija_a, opcija_b, opcija_c, tacan_odgovor) VALUES (?, ?, ?, ?, ?, ?)");

    for ($i = 0; $i < count($pitanja); $i++) {
        $stmt->execute([
            $kviz_id, 
            $pitanja[$i], 
            $opcije_a[$i], 
            $opcije_b[$i], 
            $opcije_c[$i], 
            $tacni[$i]
        ]);
    }

    echo "<script>alert('Kviz je uspješno kreiran!'); window.location.href='admin.php';</script>";
}