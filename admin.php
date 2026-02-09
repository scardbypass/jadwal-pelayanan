<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Admin Jadwal Gereja</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
:root{
 --bg1:#03091d;
 --bg2:#0b1f3f;
 --glass:rgba(255,255,255,.18);
 --border:rgba(255,255,255,.35);
 --accent:#6cf7ff;
 --danger:#ff6b6b;
}

*{box-sizing:border-box}

body{
 margin:0;
 padding:20px;
 min-height:100vh;
 font-family:system-ui;
 color:white;
 background:
 radial-gradient(circle at 20% 15%,#3a7bff55,transparent 42%),
 radial-gradient(circle at 80% 20%,#a06cff55,transparent 42%),
 linear-gradient(160deg,var(--bg1),var(--bg2));
}

h1{
 text-align:center;
 font-size:clamp(2rem,4vw,3rem);
 font-weight:900;
 background:linear-gradient(90deg,#6cf7ff,#b99cff);
 -webkit-background-clip:text;
 -webkit-text-fill-color:transparent;
}

/* WEEK */
.weekGrid{
 display:grid;
 grid-template-columns:repeat(3,1fr);
 gap:14px;
 justify-items:center;
 margin:26px 0;
}
.weekGrid .center{
 grid-column:span 3;
 display:flex;
 justify-content:center;
 gap:14px;
}

.weekBtn{
 width:130px;
 padding:9px;
 border-radius:999px;
 border:1px solid var(--border);
 background:var(--glass);
 color:white;
 font-weight:800;
 cursor:pointer;
 transition:.25s;
}

.weekBtn:hover{transform:scale(1.05)}

.weekBtn.active{
 background:linear-gradient(135deg,#6cf7ff99,#a38cff99);
 box-shadow:0 0 22px #6cf7ffaa;
}

/* GRID */
.mainGrid{
 display:grid;
 grid-template-columns:repeat(auto-fit,minmax(320px,1fr));
 gap:22px;
}

/* CARD */
.card{
 background:linear-gradient(180deg,rgba(255,255,255,.22),rgba(255,255,255,.05));
 border:1px solid var(--border);
 border-radius:30px;
 padding:20px;
 backdrop-filter:blur(24px);
 box-shadow:0 30px 70px rgba(0,0,0,.6);
}

.row{
 display:grid;
 grid-template-columns:1fr 1fr;
 gap:14px;
}

/* BUTTON */
button{
 background:linear-gradient(135deg,#6cf7ff66,#a38cff66);
 border:1px solid var(--border);
 border-radius:999px;
 padding:8px 18px;
 color:white;
 font-weight:800;
 cursor:pointer;
 transition:.2s;
}

button:hover{transform:scale(1.05)}

.btnDanger{
 background:linear-gradient(135deg,#ff8b8b,#ff4d4d);
}

/* FORM */
input,textarea,select{
 width:100%;
 padding:12px;
 border-radius:16px;
 border:1px solid var(--border);
 background:rgba(0,0,0,.35);
 color:white;
}

/* SONG */
.songItem{
 background:rgba(255,255,255,.15);
 padding:10px 14px;
 border-radius:18px;
 border:1px solid var(--border);
 display:flex;
 justify-content:space-between;
 align-items:center;
 margin-top:8px;
}

/* OUTFIT */
.outfitGrid{
 display:grid;
 grid-template-columns:repeat(auto-fill,minmax(120px,1fr));
 gap:12px;
}
.outfitCard{
 position:relative;
 border-radius:20px;
 overflow:hidden;
}
.outfitCard img{
 width:100%;
 height:150px;
 object-fit:cover;
}
.outfitCard button{
 position:absolute;
 top:6px;
 right:6px;
}

/* PROGRESS */
.progressBar{
 height:12px;
 background:rgba(255,255,255,.25);
 border-radius:999px;
 overflow:hidden;
 margin-top:8px;
}
.progressFill{
 height:100%;
 width:0%;
 background:linear-gradient(90deg,#6cf7ff,#a38cff);
 transition:.2s;
}

/* MODAL */
.modal{
 position:fixed;
 inset:0;
 background:rgba(0,0,0,.6);
 display:none;
 align-items:center;
 justify-content:center;
 z-index:999;
}
.modalBox{
 background:linear-gradient(180deg,rgba(255,255,255,.22),rgba(255,255,255,.05));
 border-radius:26px;
 padding:18px;
 width:95%;
 max-width:440px;
}
</style>
</head>

<body>

<h1>ADMIN PANEL — JADWAL</h1>

<div class="weekGrid" id="weekGrid"></div>

<div class="mainGrid">

<!-- KELOLA NAMA -->
<div class="card">
<h3>📋 Kelola Nama</h3>

<select id="divisionSelect">
 <option value="Worship">Worship</option>
 <option value="Multimedia">Multimedia</option>
 <option value="Usher">Usher</option>
</select>

<div class="row">
 <input id="newName" placeholder="Nama baru">
 <button onclick="addPerson()">Tambah</button>
</div>

<div id="nameList"></div>
</div>

<!-- ASSIGN -->
<div class="card">
<h3>🎤 Assign Posisi</h3>
<div id="assignGroups"></div>
</div>

<!-- SONG -->
<div class="card">
<h3>🎵 Lagu</h3>

<input id="songTitle" placeholder="Judul lagu">
<input id="songYT" placeholder="Link YouTube">
<input id="songSeq" placeholder="Link Sequencer">
<textarea id="songLyrics" placeholder="Lirik"></textarea>

<button onclick="saveSong()">💾 Simpan Lagu</button>

<div id="songList"></div>
</div>

<!-- OUTFIT -->
<div class="card">
<h3>👕 Outfit</h3>

<input type="file" id="fileInput" multiple accept="image/*">

<button onclick="uploadOutfit()">⬆ Upload</button>

<div id="progressWrap"></div>

<div id="outfitGrid" class="outfitGrid"></div>
</div>

<!-- JADWAL LATIHAN -->
<div class="card">
<h3>📅 Jadwal Latihan</h3>

<div class="row">
<select id="latHari"></select>
<select id="latJam"></select>
</div>

<br>

<button onclick="saveLatihan()">💾 Simpan & Aktifkan</button>

<hr>

<div id="latihanList"></div>
</div>

<!-- PREVIEW -->
<div class="card">
<h3>👀 Preview Minggu</h3>
<div id="previewBox"></div>
</div>

</div>

<!-- MODAL ASSIGN -->
<div class="modal" id="assignModal">
 <div class="modalBox">
  <h3 id="assignTitle"></h3>
  <select id="peopleSelect" multiple></select>
  <br><br>
  <button onclick="saveAssign()">Save</button>
  <button onclick="closeAssign()">Close</button>
 </div>
</div>

<script>
const roles={
 Worship:["WL","Singer","Keyboard 1","Keyboard 2","Gitar Listrik","Gitar Akustik","Bass","Drum"],
 Multimedia:["Stage Leader","Lighting","Laptop","PC","Sound","Kamera"],
 Usher:["Door Pagi","Door Sore","Usher Pagi","Usher Sore","Pengumuman"]
};

const weeks=[1,2,3,4,5];
let currentWeek=1;
let currentRole="";

const api=(u,opt={})=>fetch(u,opt).then(r=>r.json());

/* ================= WEEK ================= */

weekGrid.innerHTML=
 weeks.slice(0,3).map(w=>`<button class="weekBtn" data-w="${w}">Minggu ${w}</button>`).join("")+
 `<div class="center">`+
 weeks.slice(3).map(w=>`<button class="weekBtn" data-w="${w}">Minggu ${w}</button>`).join("")+
 `</div>`;

document.querySelectorAll(".weekBtn").forEach(b=>{
 b.onclick=()=>{
  currentWeek=b.dataset.w;
  document.querySelectorAll(".weekBtn").forEach(x=>x.classList.remove("active"));
  b.classList.add("active");
  loadAll();
 };
});
document.querySelector(".weekBtn").click();

/* ================= NAMES ================= */

async function loadNames(){
 const d=divisionSelect.value;
 const data=await api(`people.php?division=${d}`);

 nameList.innerHTML=data.map(p=>`
  <div class="row">
   ${p.name}
   <button class="btnDanger" onclick="deletePerson(${p.id})">✖</button>
  </div>`).join("");
}

divisionSelect.onchange=loadNames;

async function deletePerson(id){

 if(!confirm("Hapus nama ini?")) return;

 const res = await fetch("people.php?id="+id,{
  method:"DELETE"
 });

 const out = await res.json();
 console.log("DELETE PERSON:", out);

 if(out.error){
  alert(out.error);
  return;
 }

 loadNames();
}

/* ================= ASSIGN ================= */

async function renderAssign(){

 const data=await api(`assign.php?week=${currentWeek}`);

 const map={};
 data.forEach(r=>{
  if(!map[r.role]) map[r.role]=[];
  map[r.role].push(r.name);
 });

 assignGroups.innerHTML="";

 for(const g in roles){
  assignGroups.innerHTML+=`<div class="sectionTitle">${g}</div>`;
  roles[g].forEach(r=>{
   assignGroups.innerHTML+=`
    <button onclick="openAssign('${r}')">
     ${r}<br>
     <small>${map[r]?.join(", ")||"Kosong"}</small>
    </button>`;
  });
 }
}

async function openAssign(role){

 currentRole=role;

 const group=Object.keys(roles).find(k=>roles[k].includes(role));

 const people=await api(`people.php?division=${group}`);
 const assigns=await api(`assign.php?week=${currentWeek}`);

 const selected=assigns
  .filter(a=>a.role===role)
  .map(a=>a.name);

 peopleSelect.innerHTML=people.map(p=>
  `<option value="${p.id}" ${selected.includes(p.name)?"selected":""}>${p.name}</option>`
 ).join("");

 assignTitle.innerText=`${role} — Minggu ${currentWeek}`;
 assignModal.style.display="flex";
}

function closeAssign(){
 assignModal.style.display="none";
}

async function saveAssign(){

 const ids=[...peopleSelect.selectedOptions].map(o=>o.value);

 const res=await fetch("assign.php",{
  method:"POST",
  headers:{ "Content-Type":"application/json" },
  body:JSON.stringify({
   week:currentWeek,
   role:currentRole,
   personIds:ids
  })
 });

 const out=await res.json();
 console.log("ASSIGN:",out);

 if(out.error){
  alert(out.error);
  return;
 }

 closeAssign();
 renderAssign();
 renderPreview();
}

/* ================= SONG ================= */

async function loadSongs(){

 const songs=await api(`songs.php?week=${currentWeek}`);

 songList.innerHTML=songs.map(s=>`
  <div class="songItem">
   ${s.title}
   <button class="btnDanger" onclick="deleteSong(${s.id})">✖</button>
  </div>`).join("");
}

async function saveSong(){

 const res=await fetch("songs.php",{
  method:"POST",
  headers:{ "Content-Type":"application/json" },
  body:JSON.stringify({
   week:currentWeek,
   title:songTitle.value,
   youtube:songYT.value,
   sequencer:songSeq.value,
   lyrics:songLyrics.value
  })
 });

 const out=await res.json();
 console.log("SONG:",out);

 if(out.error){
  alert(out.error);
  return;
 }

 songTitle.value="";
 songYT.value="";
 songSeq.value="";
 songLyrics.value="";

 loadSongs();
}

async function deleteSong(id){

 if(!confirm("Hapus lagu ini?")) return;

 const res=await fetch("songs.php?id="+id,{
  method:"DELETE"
 });

 const out=await res.json();
 console.log("DELETE SONG:",out);

 loadSongs();
}

/* ================= OUTFIT ================= */

async function loadOutfits(){

 const data=await api(`outfits.php?week=${currentWeek}`);

 outfitGrid.innerHTML=data.map(o=>`
  <div class="outfitCard">
   <img src="uploads/minggu${currentWeek}/${o.filename}">
   <button class="btnDanger" onclick="deleteOutfit(${o.id})">✖</button>
  </div>`).join("");
}

async function deleteOutfit(id){

 if(!confirm("Hapus outfit ini?")) return;

 await fetch("outfits.php?id="+id,{
  method:"DELETE"
 });

 loadOutfits();
}

/* ================= PREVIEW ================= */

async function renderPreview(){

 const a=await api(`assign.php?week=${currentWeek}`);
 const s=await api(`songs.php?week=${currentWeek}`);
 const o=await api(`outfits.php?week=${currentWeek}`);

 previewBox.innerHTML=
  a.map(x=>`${x.role}: ${x.name}`).join("<br>")+
  "<hr>Lagu:<br>"+s.map(x=>"• "+x.title).join("<br>")+
  "<hr>Outfit:<br>"+
  o.map(x=>`<img width=60 src="uploads/minggu${currentWeek}/${x.filename}">`).join("");
}

/* ================= LATIHAN ================= */

async function saveLatihan(){

 if(!latHari.value || !latJam.value){
  alert("Pilih hari & jam");
  return;
 }

 const res=await fetch("latihan.php",{
  method:"POST",
  headers:{ "Content-Type":"application/json" },
  body:JSON.stringify({
   hari:latHari.value,
   jam:latJam.value
  })
 });

 const out=await res.json();
 console.log("LATIHAN:",out);

 if(out.error){
  alert(out.error);
  return;
 }

 loadLatihan();
}

async function loadLatihan(){

 const hariArr=["Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu"];
 const jamArr=["17.00","18.00","19.00","20.00","21.00"];

 latHari.innerHTML=
  `<option value="">Hari</option>`+
  hariArr.map(h=>`<option>${h}</option>`).join("");

 latJam.innerHTML=
  `<option value="">Jam</option>`+
  jamArr.map(j=>`<option>${j}</option>`).join("");

 try{

  const data=await api("latihan.php");

  latihanList.innerHTML=data.map(r=>`
   <div class="row">
    ${r.hari} — ${r.jam}
    <b style="color:${r.aktif?'#6cff9c':'#aaa'}">
     ${r.aktif?'AKTIF':''}
    </b>
   </div>
  `).join("");

 }catch(err){

  console.error(err);

  latihanList.innerHTML=
   `<small style="opacity:.6">Belum ada data latihan</small>`;
 }
}

/* ================= LOAD ALL ================= */

async function loadAll(){

 await loadNames();
 await renderAssign();
 await loadSongs();
 await loadOutfits();
 await renderPreview();
 await loadLatihan();
}
</script>


</body>
</html>
