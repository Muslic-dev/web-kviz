<?php
require "includes/connection.php";

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$kviz_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// 1. Dohvati podatke o kvizu
$stmt = $conn->prepare("SELECT * FROM kvizovi WHERE kviz_id = ?");
$stmt->execute([$kviz_id]);
$kviz = $stmt->fetch();

if (!$kviz) { die("Kviz nije pronađen."); }

// 2. Dohvati pitanja
$stmt = $conn->prepare("SELECT * FROM pitanja WHERE kviz_id = ?");
$stmt->execute([$kviz_id]);
$pitanja = $stmt->fetchAll(PDO::FETCH_ASSOC);
$pitanja_json = json_encode($pitanja);
?>

<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($kviz['naziv_kviza']) ?> | Kvizomanija</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea, #764ba2); margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .quiz-container { max-width: 600px; width: 90%; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); text-align: center; }
        .btn { background: #667eea; color: #fff; border: none; padding: 14px 35px; font-size: 16px; border-radius: 30px; cursor: pointer; transition: 0.3s; width: 100%; margin-top: 10px; }
        .btn:hover { background: #5a6fd5; }
        .timer { font-size: 20px; font-weight: 700; color: #667eea; margin-bottom: 20px; }
        .answers button { width: 100%; padding: 14px; margin: 10px 0; border: 2px solid #eee; border-radius: 10px; background: #fff; font-size: 16px; cursor: pointer; transition: 0.3s; text-align: left; }
        .answers button:hover:not([disabled]) { border-color: #667eea; background: #f8f9ff; }
        .correct { background: #4CAF50 !important; color: white !important; border-color: #4CAF50 !important; }
        .wrong { background: #e74c3c !important; color: white !important; border-color: #e74c3c !important; }
    </style>
</head>
<body>

<div class="quiz-container" id="quizBox">
    <div id="startScreen">
        <h2 style="color: #667eea;"><?= htmlspecialchars($kviz['naziv_kviza']) ?></h2>
        <p>Broj pitanja: <strong><?= count($pitanja) ?></strong></p>
        <p>Vrijeme po pitanju: <strong><?= $kviz['vremensko_ogranicenje'] ?>s</strong></p>
        <button class="btn" onclick="startQuiz()">Započni kviz</button>
    </div>

    <div id="quizScreen" style="display:none;">
        <div class="timer">⏱ Preostalo: <span id="time">15</span>s</div>
        <h3 id="questionText" style="margin-bottom: 25px;"></h3>
        <div class="answers" id="answerBtns"></div>
    </div>
</div>

<script>
const questions = <?= $pitanja_json ?>;
let index = 0, score = 0, time = <?= $kviz['vremensko_ogranicenje'] ?>, timer, totalSeconds = 0, stopwatch;

function startQuiz() {
    document.getElementById("startScreen").style.display = "none";
    document.getElementById("quizScreen").style.display = "block";
    
    // Započni ukupno mjerenje vremena
    stopwatch = setInterval(() => { totalSeconds++; }, 1000);
    
    loadQuestion();
}

function loadQuestion() {
    if (index >= questions.length) { finishQuiz(); return; }
    
    time = <?= $kviz['vremensko_ogranicenje'] ?>;
    document.getElementById("time").textContent = time;
    
    const q = questions[index];
    document.getElementById("questionText").textContent = q.tekst_pitanja;
    
    const btnsDiv = document.getElementById("answerBtns");
    btnsDiv.innerHTML = '';
    
    const options = [q.opcija_a, q.opcija_b, q.opcija_c];
    options.forEach(opt => {
        const b = document.createElement("button");
        b.textContent = opt;
        b.onclick = () => checkAnswer(opt, q.tacan_odgovor, b);
        btnsDiv.appendChild(b);
    });

    clearInterval(timer);
    timer = setInterval(() => {
        time--;
        document.getElementById("time").textContent = time;
        if (time <= 0) { checkAnswer(null, q.tacan_odgovor, null); }
    }, 1000);
}

function checkAnswer(selected, correct, btn) {
    clearInterval(timer);
    const btns = document.querySelectorAll(".answers button");
    btns.forEach(b => b.disabled = true);

    if (selected === correct) {
        if(btn) btn.classList.add("correct");
        score++;
    } else {
        if(btn) btn.classList.add("wrong");
        // Prikaži tačan odgovor
        btns.forEach(b => { if(b.textContent === correct) b.classList.add("correct"); });
    }

    setTimeout(() => {
        index++;
        loadQuestion();
    }, 1500);
}

function finishQuiz() {
    clearInterval(stopwatch);
    
    // Slanje rezultata u bazu
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "pokreniKviz.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("score=" + score + "&max=" + questions.length + "&sekunde=" + totalSeconds + "&kviz_id=" + <?= $kviz_id ?>);

    document.getElementById("quizBox").innerHTML = `
        <h2 style="color: #667eea;">Kviz završen!</h2>
        <p style="font-size: 1.2rem;">Tačnih odgovora: <strong>${score} / ${questions.length}</strong></p>
        <p>Ukupno vrijeme: <strong>${totalSeconds}s</strong></p>
        <a href="index2.php" class="btn" style="text-decoration:none; display:block;">Nazad na izbor</a>
    `;
}
</script>
</body>
</html>