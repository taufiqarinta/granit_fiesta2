<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Undian Kehadiran</title>
<?php
$durasi = 10; // detik
?>
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,Helvetica,sans-serif;
}

body{
    color:white;
	min-height: 100vh;
	background-image: url('https://granit-fiesta2.kobin.co.id/images/bg-doorprize.webp');
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center center;
}

.header{
    background-image: url('pita.png');
    background-repeat: no-repeat;
    background-size: 40% 80%;
    background-position: center 70%;
    width: 100%;
    height: 140px;
}

.header h1{
    color:gold;
    font-size:20px;
	padding-top:35px;
	text-align:center;
}

.header h3{
	font-size:15px;
	text-align:center;
}

.container{
    display:flex;
    padding:20px;
    gap:20px;
}

.left{
    width:30%;
	text-align:center;
	justify-items: center;
	margin-top: 0%;
}

.center{
    width:40%;
    text-align:center;
}

.right{
	width:30%;
	justify-items: center;
	text-align:center;
}

.card{
	width: 150px;
    color: white;
    padding: 5px;
    text-align: center;
    font-size: 0.75em;
    font-weight: bold;
    border-radius: 10px;
    background-image: linear-gradient(#f01c28, #b71c1c), linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
    background-origin: border-box;
    background-clip: padding-box, border-box;
    border: 5px solid transparent;
}

.listPeserta{
    margin-top:15px;
    max-height: 400px;
    overflow-y: auto;
}

.listPeserta li{
    padding:8px;
    border-bottom:1px solid #333;
    list-style: none;
}

.total{
    font-size:50px;
    color:gold;
    text-align:center;
}

.wheel-container {
    position: relative;
    margin-top: -30px;
	padding: 80px; /* Ini akan berfungsi sebagai tebal border */
    background: linear-gradient(135deg, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
    display: inline-block;
	background-image: url('lencana.png');
    background-repeat: no-repeat;
    background-size: 110% auto;
    background-position: center -3%;
	
}

/* Panah penunjuk di posisi Atas / Jam 12 */
.pointer{
    width: 0;
    height: 0;
    border-left: 15px solid transparent;
    border-right: 15px solid transparent;
    border-top: 30px solid red;
    position: absolute;
    top: 72px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

#wheelCanvas {
    border-radius: 50%;
    display: block;
    transition: transform <?php echo $durasi; ?>s cubic-bezier(0.15, 0.9, 0.2, 1);
}

.btn-play {
    margin-top: 40%;
    width: 100px; /* Diubah menjadi 100px */
    height: 100px; /* Diubah menjadi 100px agar lingkaran sempurna */
    border-radius: 50%; 
    cursor: pointer;
    transition: transform 0.2s;
    
    /* Mengatur ikon dan teks di tengah secara vertikal */
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px; /* Jarak diperkecil agar pas dengan ukuran 100px */

    /* Trik Border Gradasi Emas */
    border: 4px solid transparent; 
    background-image: 
        linear-gradient(#cd1c21, #cd1c21), 
        linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
    background-origin: border-box;
    background-clip: padding-box, border-box;
	
	outline: none; /* Menghilangkan kotak biru/hitam saat fokus di desktop */
    -webkit-tap-highlight-color: transparent; /* Menghilangkan kotak highlight saat ditekan di HP */
}

/* Ukuran Segitiga Ikon Play (Disesuaikan agar proporsional) */
.icon-play {
    width: 0;
    height: 0;
    border-top: 10px solid transparent;
    border-bottom: 10px solid transparent;
    border-left: 16px solid white; 
    margin-left: 3px; /* Penyeimbang posisi tengah */
}

/* Ukuran Teks MULAI (Disesuaikan agar tidak terpotong) */
.text-play {
    color: white;
    font-size: 11px; /* Ukuran font diperkecil sedikit */
    font-weight: bold;
    font-family: sans-serif;
    letter-spacing: 0.5px;
}

/* Efek Hover */
.btn-play:hover {
    /* transform: scale(1.05); */
}

/* Efek Disabled */
.btn-play:disabled {
    background-image: linear-gradient(#555, #555), linear-gradient(#777, #777);
    cursor: not-allowed;
    transform: none;
}

.winner{
    font-size:18px;
	font-weight:bold;
	color:white;
	text-align:center;
	word-break: break-word;
	padding-top:28px;
	background-image: url('pita_winner.png');
    background-repeat: no-repeat;
    background-size: 130% 300%;
    background-position: center 59%;
    width: 100%;
    height: 140px;
	margin-top: 100px;
}

.info{
    margin-top:15px;
    text-align:center;
    font-size:18px;
}
</style>

</head>
<body>

<div class="header">
    <h1>UNDIAN KEHADIRAN</h1>
    <h3>Periode Kehadiran 17.00 - 18.00 WIB</h3>
</div>

<div class="container">

   <div class="left">
        <div class="card">
            <h2>Total Peserta</h2>
            <div class="total" id="total">0</div>
        </div>
		<div class="info" id="status"></div>
		<div class="winner" id="winner">-</div>
		<img src="kado_banyak.png" width="300px" />
    </div>

    <div class="center">
        <div class="wheel-container">
            <div class="pointer"></div>
            <canvas id="wheelCanvas" width="350" height="350"></canvas>
        </div>
    </div>
	
	<div class="right">
        <div class="card" style="display:none">
            <h2>Peserta</h2>
            <ul id="pesertaList" class="listPeserta"></ul>
        </div>
		<button class="btn-play" id="spinBtn" onclick="spin()">
		  <span class="icon-play"></span>
		  <span class="text-play">MULAI</span>
		</button>
    </div>

</div>

<script>
let peserta = [
    "MAJU JAYA", "FAJAR KERAMIK", "ANUGRAH KERAMIK", "SIDOMULYO", "ANEKA RAGAM KERAMIK", "INDAH GRANIT", "ASIA BANGUNAN", "BUDI JAYA KERAMIK", "ARGO GRANIT", 
    "BINTANG JAYA", "FAJAR NIAGA", "MAJU MAKMUR", "TANI MAKMUR", "DIAN KERAMIK", "JAYA RAYA KERAMIK", "AGRO JAYA", "DEPO MURAH", "ANGKASA KERAMIK", "MITRA BANGUNAN", "PELITA JAYA KERAMIK", "HARAPAN MAKMUR"
];

let sudahMenang = [];

const colors = [
    "#f39c12", "#2ecc71", "#3498db", "#9b59b6", "#e74c3c",
    "#16a085", "#d35400", "#2980b9", "#8e44ad", "#27ae60",
    "#1abc9c", "#e67e22", "#c0392b", "#2c3e50",
    "#f1c40f", "#34495e", "#7f8c8d", "#bdc3c7",
    "#ff0055", "#ff5722", "#ff9800", "#ffeb3b", "#8bc34a",
    "#4caf50", "#009688", "#00bcd4", "#03a9f4", "#2196f3",
    "#3f51b5", "#673ab7", "#9c27b0", "#e91e63", "#00e676",
    "#ff1744", "#f50057", "#d500f9", "#651fff", "#3d5aff",
    "#2979ff", "#00b0ff", "#00e5ff", "#1de9b6", "#76ff03",
    "#c6ff00", "#ffea00", "#ffc400", "#ff9100", "#ff3d00",
    "#7c4dff", "#536def", "#448aff", "#40c4ff", "#18ffff",
    "#64ffda", "#b2ff59", "#eeff41", "#ffd740", "#ffab40"
];

let currentRotation = 0;
let spinning = false;

const canvas = document.getElementById("wheelCanvas");
const ctx = canvas.getContext("2d");

function renderUI() {
    document.getElementById("total").innerHTML = peserta.length;

    const ul = document.getElementById("pesertaList");
    ul.innerHTML = "";
    peserta.forEach(n => {
        ul.innerHTML += "<li>" + n + "</li>";
    });

    drawWheel();
}

function drawWheel() {
    const numOptions = peserta.length;
    if (numOptions === 0) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        return;
    }

    const arcSize = (2 * Math.PI) / numOptions;
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = centerX;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    peserta.forEach((nama, i) => {
        const angle = i * arcSize;

        // Gambar Sektor
        ctx.beginPath();
        ctx.fillStyle = colors[i % colors.length];
        ctx.moveTo(centerX, centerY);
        ctx.arc(centerX, centerY, radius, angle, angle + arcSize);
        ctx.lineTo(centerX, centerY);
        ctx.fill();

        // Tulis Nama Peserta
        ctx.save();
        ctx.fillStyle = "#ffffff";
        ctx.font = "bold 11px Arial";
        ctx.translate(centerX, centerY);
        ctx.rotate(angle + arcSize / 2);
        ctx.textAlign = "right";
        ctx.fillText(nama, radius - 20, 5);
        ctx.restore();
    });
}

function spin() {
    if (spinning) return;

    if (peserta.length === 0) {
        alert("Semua peserta sudah menang!");
        return;
    }

    spinning = true;
    document.getElementById("spinBtn").disabled = true;
    // document.getElementById("status").innerHTML = "Mengundi...";

    // 1. Pilih pemenang secara acak berdasarkan index
    const winningIndex = Math.floor(Math.random() * peserta.length);
    const sliceAngle = 360 / peserta.length;

    // 2. Hitung sudut target agar winningIndex berada persis di jam 12 (270 derajat)
    const targetAngle = 270 - (winningIndex * sliceAngle + sliceAngle / 2);
    
    // Tambahkan putaran penuh ( minimal 5 putaran / 1800 deg)
    const extraRounds = 360 * 5;
    
    // Hitung sisa rotasi saat ini agar animasi berputar secara mulus terus ke depan
    const currentMod = currentRotation % 360;
    let distance = (targetAngle - currentMod) % 360;
    if (distance < 0) distance += 360;

    currentRotation += extraRounds + distance;

    // Putar canvas via CSS
    canvas.style.transform = `rotate(${currentRotation}deg)`;

    // 3. Setelah animasi CSS selesai (5 detik)
    setTimeout(() => {
        const namaPemenang = peserta[winningIndex];

        document.getElementById("winner").innerHTML = namaPemenang;
        // document.getElementById("status").innerHTML = "🎉 Selamat!";

        sudahMenang.push(namaPemenang);
        peserta.splice(winningIndex, 1);

        // Reset rotasi canvas secara instan tanpa animasi agar tidak melompat di undian berikutnya
        canvas.style.transition = "none";
        currentRotation = currentRotation % 360;
        canvas.style.transform = `rotate(${currentRotation}deg)`;
        
        // Render ulang daftar peserta & gambar roda yang baru
        renderUI();

        // Kembalikan efek animasi CSS
        setTimeout(() => {
            canvas.style.transition = "transform <?php echo $durasi; ?>s cubic-bezier(0.15, 0.9, 0.2, 1)";
            spinning = false;
            document.getElementById("spinBtn").disabled = false;
        }, 50);

    }, <?php echo ($durasi*1000); ?>);
}

document.addEventListener('keydown', function(event) {
    // Memastikan tombol yang ditekan adalah Spasi (kode: 'Space')
    if (event.code === 'Space' || event.keyCode === 32) {
        
        // Mengambil elemen tombol Anda
        const tombolPlay = document.querySelector('.btn-play');
        
        if (tombolPlay) {
            // Mencegah halaman scroll ke bawah saat tekan spasi
            event.preventDefault(); 
            
            // Memicu efek klik pada tombol
            tombolPlay.click();
        }
    }
});

// Contoh fungsi ketika tombol benar-benar diklik
document.querySelector('.btn-play').addEventListener('click', function() {
    console.log("Tombol MULAI berhasil ditekan lewat spasi atau klik!");
    // Tulis kode game atau video Anda di sini
});


// Inisialisasi awal
renderUI();
</script>

</body>
</html>