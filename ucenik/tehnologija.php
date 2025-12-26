<?php
require "../includes/connection.php";

// Provjera sesije
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <title>IT & Tehnologija | Kvizomanija</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #667eea, #764ba2); margin: 0; padding: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .quiz-container { max-width: 600px; width: 90%; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.25); text-align: center; position: relative; overflow: hidden; }
        
        /* Stilovi za Start Ekran */
        .icon-circle { width: 80px; height: 80px; background: #e0e7ff; color: #667eea; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; margin: 0 auto 20px; }
        .rules-list { list-style: none; padding: 0; margin: 25px 0; text-align: left; background: #f8fafc; padding: 20px; border-radius: 12px; }
        .rules-list li { margin-bottom: 12px; font-size: 15px; display: flex; align-items: center; color: #4a5568; }
        .rules-list li::before { content: '✔️'; margin-right: 10px; color: #48bb78; }
        
        .btn { background: #667eea; color: #fff; border: none; padding: 15px 40px; font-size: 18px; font-weight: 600; border-radius: 30px; cursor: pointer; margin-top: 10px; transition: 0.3s; width: 100%; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); }
        .btn:hover { background: #5a6fd6; transform: translateY(-3px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6); }
        
        .timer { font-size: 18px; font-weight: 700; color: #667eea; margin-bottom: 20px; background: #e0e7ff; display: inline-block; padding: 8px 20px; border-radius: 20px; }
        
        .answers button { width: 100%; padding: 16px; margin: 8px 0; border: 2px solid #edf2f7; border-radius: 12px; background: #fff; font-size: 16px; cursor: pointer; transition: 0.2s; font-weight: 500; color: #2d3748; text-align: left; padding-left: 20px; }
        .answers button:hover:not([disabled]) { border-color: #667eea; background: #f0f4ff; color: #667eea; }
        
        .correct { background: #48bb78 !important; color: #fff !important; border-color: #48bb78 !important; }
        .wrong { background: #f56565 !important; color: #fff !important; border-color: #f56565 !important; }
        
        .warning-box { background: #fffaf0; color: #c05621; padding: 12px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ed8936; font-size: 0.85rem; text-align: left; }
        h2 { color: #1a202c; margin-bottom: 10px; font-size: 1.8rem; }
        p.desc { color: #718096; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="quiz-container" id="quizBox">
    <div class="intro" id="startScreen">
        <div class="icon-circle">💻</div>
        <h2>IT & Tehnologija</h2>
        <p class="desc">Testiraj svoje znanje o računarima, internetu i softveru!</p>
        
        <ul class="rules-list">
            <li><strong>15 pitanja</strong> ukupno u kvizu.</li>
            <li><strong>15 sekundi</strong> vremena po pitanju.</li>
            <li>Budi brz! Ukupno vrijeme se računa za rang listu.</li>
        </ul>

        <div class="warning-box">
            ⚠️ <strong>Anti-Cheat Sistem:</strong><br>
            Zabranjeno je osvježavanje (refresh) stranice! Ako to uradiš, gubiš trenutno pitanje.
        </div>

        <button class="btn" onclick="startQuiz()">Započni Izazov 🚀</button>
    </div>

    <div id="quizScreen" style="display:none;">
        <div class="timer">⏱ <span id="time">15</span>s</div>
        <h3 id="question" style="min-height: 60px; display: flex; align-items: center; justify-content: center;"></h3>
        <div class="answers" id="answerBtns">
            <button onclick="checkAnswer(0)"></button>
            <button onclick="checkAnswer(1)"></button>
            <button onclick="checkAnswer(2)"></button>
        </div>
        <div style="margin-top: 20px; color: #a0aec0; font-size: 0.9rem;">
            Pitanje <span id="qIndex">1</span> / <span id="qTotal">15</span>
        </div>
    </div>
</div>

<script>
// --- 15 PITANJA IZ IT SVIJETA ---
const questions = [
    { q: "Šta znači skraćenica HTML?", a: ["Hyper Text Markup Language", "High Tech Main Language", "Hyperlink Text Mode"], c: 0 },
    { q: "Koji jezik se primarno koristi za baze podataka?", a: ["Python", "SQL", "C++"], c: 1 },
    { q: "Šta je RAM u računaru?", a: ["Trajna memorija", "Radna memorija", "Procesor"], c: 1 },
    { q: "Koja komponenta je 'mozak' računara?", a: ["HDD", "RAM", "CPU"], c: 2 },
    { q: "Koji je najpoznatiji operativni sistem za PC?", a: ["Windows", "Android", "iOS"], c: 0 },
    { q: "Šta je SSD?", a: ["Vrsta monitora", "Brzi disk za pohranu", "Grafička kartica"], c: 1 },
    { q: "Koji od navedenih je ulazni uređaj?", a: ["Zvučnik", "Monitor", "Tastatura"], c: 2 },
    { q: "Šta znači www u web adresi?", a: ["World Wide Web", "World Web Wide", "Wide World Web"], c: 0 },
    { q: "Koja je najmanja jedinica informacije?", a: ["Bajt", "Bit", "Megabajt"], c: 1 },
    { q: "Šta štiti mrežu od napada?", a: ["Antivirus", "Firewall", "Backup"], c: 1 },
    { q: "Koji brojčani sistem koriste računari?", a: ["Dekadni", "Binarni", "Oktalni"], c: 1 },
    { q: "Ekstenzija .jpg se koristi za:", a: ["Slike", "Tekst", "Video"], c: 0 },
    { q: "Šta znači PDF?", a: ["Portable Document Format", "Personal Data File", "Print Document File"], c: 0 },
    { q: "Kombinacija tastera za kopiranje je:", a: ["Ctrl + V", "Ctrl + X", "Ctrl + C"], c: 2 },
    { q: "Koji uređaj povezuje računar na internet?", a: ["Štampač", "Ruter", "Skener"], c: 1 }
];

let index = 0, score = 0, time = 15, timer;
let ukupnoSekundi = 0, stopwatch;

// Ažuriranje brojača pitanja
document.getElementById("qTotal").innerText = questions.length;

// --- ANTI-CHEAT PROVJERA ---
window.onload = function() {
    if (localStorage.getItem("kvizUToqu") === "da") {
        index = parseInt(localStorage.getItem("kvizIndex")) || 0;
        score = parseInt(localStorage.getItem("kvizScore")) || 0;
        ukupnoSekundi = parseInt(localStorage.getItem("kvizVrijeme")) || 0;

        alert("Pokušali ste osvježiti stranicu! Trenutno pitanje je označeno kao netačno.");
        index++; 
        startQuiz(true);
    }
};

function startQuiz(nastavak = false) {
    localStorage.setItem("kvizUToqu", "da");
    
    document.getElementById("startScreen").style.display = "none";
    document.getElementById("quizScreen").style.display = "block";
    
    // ŠTOPERICA (Mjeri ukupno vrijeme za rang listu)
    if (!stopwatch) {
        stopwatch = setInterval(() => { 
            ukupnoSekundi++; 
            localStorage.setItem("kvizVrijeme", ukupnoSekundi); 
        }, 1000);
    }

    loadQuestion();
}

function loadQuestion() {
    if (index >= questions.length) { finishQuiz(); return; }
    
    // Ažuriranje brojača trenutnog pitanja
    document.getElementById("qIndex").innerText = index + 1;

    localStorage.setItem("kvizIndex", index);
    localStorage.setItem("kvizScore", score);

    time = 15;
    document.getElementById("time").textContent = time;
    document.getElementById("question").textContent = questions[index].q;
    
    const btns = document.querySelectorAll(".answers button");
    btns.forEach((btn, i) => {
        btn.textContent = questions[index].a[i];
        btn.className = "";
        btn.disabled = false;
    });

    // Tajmer za pojedinačno pitanje (15s)
    clearInterval(timer);
    timer = setInterval(() => {
        time--;
        document.getElementById("time").textContent = time;
        if (time <= 0) { 
            clearInterval(timer);
            // Isteklo vrijeme - idemo dalje bez bodova
            checkAnswer(-1); 
        }
    }, 1000);
}

function checkAnswer(ans) {
    clearInterval(timer);
    const btns = document.querySelectorAll(".answers button");
    btns.forEach(b => b.disabled = true);

    if (ans === questions[index].c) {
        btns[ans].classList.add("correct");
        score++;
    } else if (ans !== -1) {
        btns[ans].classList.add("wrong");
        btns[questions[index].c].classList.add("correct");
    } else {
        // Ako je vrijeme isteklo (ans je -1), samo pokaži tačan odgovor
        btns[questions[index].c].classList.add("correct");
    }

    setTimeout(() => {
        index++;
        loadQuestion();
    }, 1200);
}

function finishQuiz() {
    clearInterval(stopwatch);
    clearInterval(timer);
    
    // Čišćenje memorije
    localStorage.removeItem("kvizUToqu");
    localStorage.removeItem("kvizIndex");
    localStorage.removeItem("kvizScore");
    localStorage.removeItem("kvizVrijeme");

    // SLANJE U BAZU
    const formData = new FormData();
    formData.append('score', score);
    formData.append('max', questions.length);
    formData.append('sekunde', ukupnoSekundi);

    fetch('pokreniKviz.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => console.log("Baza kaže:", data));

    // Završni ekran
    document.getElementById("quizBox").innerHTML = `
        <div class="result" style="animation: fadeIn 0.5s;">
            <div style="font-size: 60px; margin-bottom: 10px;">🏆</div>
            <h2 style="color: #667eea;">Čestitamo!</h2>
            <p style="font-size: 1.1rem; color: #4a5568;">Završili ste IT kviz.</p>
            
            <div style="background: #f7fafc; padding: 20px; border-radius: 15px; margin: 20px 0;">
                <p style="margin: 10px 0; font-size: 1.2rem;">Tačnih odgovora: <strong style="color: #48bb78;">${score} / ${questions.length}</strong></p>
                <p style="margin: 10px 0; font-size: 1.1rem;">Vrijeme: <strong style="color: #667eea;">${ukupnoSekundi} sekundi</strong></p>
            </div>
            
            <a href="index.php" class="btn">Nazad na Rang Listu</a>
        </div>`;
}
</script>
</body>
</html>