<?php
include "db.php";

$week = isset($_GET['week']) ? intval($_GET['week']) : 1;

/* ===== BULAN AUTO ===== */
$bulanMap=[
 1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",
 5=>"Mei",6=>"Juni",7=>"Juli",8=>"Agustus",
 9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
];

/* ===== JADWAL LATIHAN ===== */
$latihanTxt=[];

$qLat=$conn->query("
 SELECT hari,jam 
 FROM latihan
 WHERE aktif = 1
 ORDER BY FIELD(hari,'Kamis','Jumat','Sabtu'), jam
");

while($r=$qLat->fetch_assoc()){
 $latihanTxt[]="Latihan hari {$r['hari']}, pukul {$r['jam']} WIB";
}

$bulan=$bulanMap[date("n")];
$tahun=date("Y");

/* ===== WEEK BUTTON ===== */
$weekAvailable=[1,2,3,4];

$chk=$conn->query("
 SELECT 1 FROM assign WHERE week=5
 UNION SELECT 1 FROM songs WHERE week=5
 UNION SELECT 1 FROM outfits WHERE week=5
 LIMIT 1
");

if($chk && $chk->num_rows>0){
 $weekAvailable[]=5;
}

/* ===== ASSIGN ===== */
$assignQ=$conn->query("
 SELECT assign.role, people.name
 FROM assign
 JOIN people ON people.id=assign.person_id
 WHERE week=$week
");

$assign=[];
while($r=$assignQ->fetch_assoc()){
 $assign[$r['role']][]=$r['name'];
}

/* ===== SONG ===== */
$songQ=$conn->query("SELECT * FROM songs WHERE week=$week ORDER BY ordering");

/* ===== OUTFIT ===== */
$outfitQ=$conn->query("SELECT * FROM outfits WHERE week=$week");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Jadwal Pelayanan GKPB MDC</title>

<style>
:root{
 --bg1:#03091d;
 --bg2:#0b1f3f;
 --glass:rgba(255,255,255,.18);
 --border:rgba(255,255,255,.35);
}

*{box-sizing:border-box;font-family:system-ui}

body{
 margin:0;padding:26px;min-height:100vh;color:white;
 background:
 radial-gradient(circle at 20% 15%,#3a7bff55,transparent 42%),
 radial-gradient(circle at 80% 20%,#a06cff55,transparent 42%),
 linear-gradient(160deg,var(--bg1),var(--bg2));
}

h1{
 text-align:center;font-size:clamp(2.2rem,4vw,3rem);
 font-weight:900;
 background:linear-gradient(90deg,#6cf7ff,#b99cff);
 -webkit-background-clip:text;
 -webkit-text-fill-color:transparent;
}

.subtitle{text-align:center;opacity:.7;margin-top:6px}

.tabs{
 display:flex;justify-content:center;gap:14px;flex-wrap:wrap;
 margin:26px 0 36px
}
.tabs a{
 background:var(--glass);border:1px solid var(--border);
 padding:10px 24px;border-radius:999px;
 color:white;font-weight:700;text-decoration:none;
}
.tabs a.active{
 background:linear-gradient(135deg,#6cf7ff55,#a38cff66);
 box-shadow:0 14px 36px #6cf7ff66;
}

.grid{
 display:grid;
 grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
 gap:24px
}

.card{
 background:linear-gradient(180deg,rgba(255,255,255,.22),rgba(255,255,255,.06));
 border:1px solid var(--border);
 border-radius:30px;
 padding:22px;
 backdrop-filter:blur(28px);
 box-shadow:0 30px 70px rgba(0,0,0,.65);
}

.roles{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px}
.role{
 background:rgba(255,255,255,.1);
 border:1px solid rgba(255,255,255,.28);
 padding:11px;border-radius:16px
}

.song{
 display:flex;justify-content:space-between;gap:12px;
 border-bottom:1px solid rgba(255,255,255,.25);
 padding:10px 0
}

.song-actions{display:flex;gap:10px}

/* ICON BUTTON */
.iconBtn{
 width:36px;height:36px;
 display:flex;align-items:center;justify-content:center;
 border-radius:50%;
 background:linear-gradient(135deg,#6cf7ff66,#a38cff66);
 border:1px solid rgba(255,255,255,.4);
 color:white;font-size:18px;
 text-decoration:none;
 box-shadow:0 6px 18px rgba(0,0,0,.4);
 transition:.2s ease;
}
.iconBtn:hover{
 transform:scale(1.12);
 box-shadow:0 0 16px #6cf7ff;
}
.iconBtn.yt{background:linear-gradient(135deg,#ff4b4b88,#ff808088)}
.iconBtn.seq{background:linear-gradient(135deg,#6cf7ff88,#7a9cff88)}

.viewBtn{
 background:linear-gradient(135deg,#6cf7ff66,#a38cff66);
 border:1px solid rgba(255,255,255,.4);
 border-radius:999px;
 padding:6px 16px;
 color:white;font-weight:700;
 cursor:pointer;
}

/* OUTFIT */
.outfit{display:flex;gap:16px;overflow-x:auto}
.outfit img{
 height:170px;border-radius:22px;
 border:1px solid rgba(255,255,255,.45)
}

/* VIDEO POPUP */
.video-overlay{
 position:fixed;
 inset:0;
 display:none;
 justify-content:center;
 align-items:flex-start;
 padding-top:90px;
 background:transparent;
 z-index:9999;
 pointer-events:none;
}

.video-box{
 width:92%;
 max-width:860px;
 aspect-ratio:16/9;
 background:black;
 border-radius:22px;
 overflow:hidden;
 box-shadow:0 40px 80px rgba(0,0,0,.7);
 pointer-events:auto;
 position:relative;
}

.video-box iframe{
 width:100%;
 height:100%;
 border:0;
}

.closeBtn{
 position:absolute;
 right:12px;
 bottom:12px;
 padding:6px 18px;
 border-radius:14px;
 background:rgba(0,0,0,.65);
 color:white;
 border:1px solid rgba(255,255,255,.3);
 cursor:pointer;
 font-weight:700;
}

/* MODAL */
.modal-bg{
 position:fixed;inset:0;
 background:rgba(0,0,0,.55);
 backdrop-filter:blur(6px);
 display:none;align-items:center;justify-content:center;
 z-index:999
}
.modal{
 background:linear-gradient(180deg,rgba(255,255,255,.22),rgba(255,255,255,.05));
 border-radius:26px;
 padding:22px;
 max-width:600px;width:92%;
 box-shadow:0 40px 90px rgba(0,0,0,.7)
}
.modal-actions{display:flex;justify-content:space-between;margin-top:12px}

.footer{text-align:center;opacity:.7;margin-top:40px;font-weight:600}
.footerLink{color:#6cf7ff;text-decoration:none}
.footerLink:hover{color:#b99cff}
</style>
</head>

<body>

<h1>Jadwal Pelayanan GKPB MDC</h1>
<div class="subtitle">
 Bulan <?=$bulan?> <?=$tahun?> — Minggu ke <?=$week?>
</div>

<!-- WEEK -->
<div class="tabs">
<?php foreach($weekAvailable as $i): ?>
 <a class="<?=$i==$week?'active':''?>" href="?week=<?=$i?>">Minggu <?=$i?></a>
<?php endforeach ?>
</div>

<div class="grid">
  
<!-- JADWAL LATIHAN -->
<?php if(count($latihanTxt)): ?>
<div class="card">
 <h2>📅 Jadwal Latihan</h2>

 <?php foreach($latihanTxt as $t): ?>
  <div class="role"><?=$t?></div>
 <?php endforeach ?>

</div>
<?php endif ?>


<!-- WORSHIP -->
<div class="card">
<h2>🎤 Worship Team</h2>
<div class="roles">
<?php
$wRoles=["WL","Singer","Keyboard 1","Keyboard 2","Gitar Listrik","Gitar Akustik","Bass","Drum"];
foreach($wRoles as $r){
 echo "<div class='role'><b>$r</b><br>".implode(", ",$assign[$r]??["-"])."</div>";
}
?>
</div>
</div>

<!-- MULTIMEDIA -->
<div class="card">
<h2>🎛 Multimedia</h2>
<div class="roles">
<?php
$mRoles=["Stage Leader","Lighting","Laptop","PC","Sound","Kamera"];
foreach($mRoles as $r){
 echo "<div class='role'><b>$r</b><br>".implode(", ",$assign[$r]??["-"])."</div>";
}
?>
</div>
</div>

<!-- SONG -->
<div class="card">
<h2>🎵 Lagu</h2>
<?php while($s=$songQ->fetch_assoc()): ?>
<div class="song">
 <span><?=htmlspecialchars($s['title'])?></span>
 <div class="song-actions">

<?php if($s['youtube']): ?>
 <button class="iconBtn yt"
  title="Play"
  onclick="playYT('<?=htmlspecialchars($s['youtube'])?>')">🎥</button>
<?php endif ?>

  <?php if($s['sequencer']): ?>
   <a class="iconBtn seq" target="_blank"
    title="Sequencer"
    href="<?=htmlspecialchars($s['sequencer'])?>">🎶</a>
  <?php endif ?>

  <?php if($s['lyrics']): ?>
   <button class="viewBtn"
    onclick="openModal('<?=htmlspecialchars($s['title'])?>',`<?=addslashes($s['lyrics'])?>`)">
    View
   </button>
  <?php endif ?>

 </div>
</div>
<?php endwhile ?>
</div>

<!-- OUTFIT -->
<div class="card">
<h2>👕 Outfit</h2>
<div class="outfit">
<?php while($o=$outfitQ->fetch_assoc()): ?>
 <img src="uploads/minggu<?=$week?>/<?=$o['filename']?>">
<?php endwhile ?>
</div>
</div>

</div>

<!-- MODAL -->
<div class="modal-bg" id="lyricsModal">
 <div class="modal">
  <h3 id="modalTitle"></h3>
  <pre id="modalText"></pre>
  <div class="modal-actions">
   <button class="viewBtn" onclick="copyLyrics()">Copy</button>
   <button class="viewBtn" onclick="closeModal()">Close</button>
  </div>
 </div>
</div>

<!-- VIDEO POPUP -->
<div class="video-overlay" id="videoPop">
 <div class="video-box">
  <iframe id="ytFrame"
   allow="autoplay; encrypted-media"
   allowfullscreen></iframe>

  <button class="closeBtn" onclick="closeYT()">Close</button>
 </div>
</div>

<script>
function openModal(t,x){
 modalTitle.innerText=t;
 modalText.innerText=x;
 lyricsModal.style.display="flex";
}
function closeModal(){
 lyricsModal.style.display="none";
}
function copyLyrics(){
 navigator.clipboard.writeText(modalText.innerText);
 alert("Lirik dicopy 👍");
}

function ytID(url){
 const m=url.match(/(youtu.be\/|v=)([^&]+)/);
 return m?m[2]:null;
}

function playYT(url){
 const id=ytID(url);

 ytFrame.src=
  "https://www.youtube.com/embed/"+id+
  "?autoplay=1&rel=0&modestbranding=1";

 videoPop.style.display="flex";
}

function closeYT(){
 ytFrame.src="";
 videoPop.style.display="none";
}
</script>

<div class="footer">
 © <a href="https://instagram.com/scardproject" target="_blank"
 class="footerLink">By SCARD-PROJECT</a>
</div>

</body>
</html>
