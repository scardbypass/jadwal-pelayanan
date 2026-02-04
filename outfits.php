<?php
include "db.php";
header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set("display_errors",1);

/* ---------- COMPRESS ---------- */
function compressImage($src,$dest,$quality=70){

 $info = getimagesize($src);

 if($info['mime']=="image/jpeg"){
  $img=imagecreatefromjpeg($src);
  imagejpeg($img,$dest,$quality);
 }
 elseif($info['mime']=="image/png"){
  $img=imagecreatefrompng($src);
  imagepng($img,$dest,7);
 }
 else{
  move_uploaded_file($src,$dest);
 }

}

/* ---------- LIST ---------- */
if($_SERVER["REQUEST_METHOD"]=="GET"){

 $week=intval($_GET["week"]);
 $r=$conn->query("SELECT * FROM outfits WHERE week=$week");

 echo json_encode($r->fetch_all(MYSQLI_ASSOC));
 exit;
}

/* ---------- UPLOAD MULTI ---------- */
if($_SERVER["REQUEST_METHOD"]=="POST"){

 $week=intval($_POST["week"]);
 $dir=__DIR__."/uploads/minggu$week/";

 if(!is_dir($dir)) mkdir($dir,0777,true);

 $files=$_FILES["files"];

 $total=count($files["name"]);

 for($i=0;$i<$total;$i++){

  if($files["error"][$i]!==0) continue;

  $tmp=$files["tmp_name"][$i];

  $safe=time()."_".rand(1000,9999)."_".
   preg_replace("/[^a-zA-Z0-9._-]/","",$files["name"][$i]);

  $dest=$dir.$safe;

  compressImage($tmp,$dest,65);

  $conn->query("INSERT INTO outfits(week,filename)
   VALUES($week,'$safe')");
 }

 echo json_encode(["ok"=>1]);
 exit;
}

/* ---------- DELETE ---------- */
if($_SERVER["REQUEST_METHOD"]=="DELETE"){

 parse_str($_SERVER["QUERY_STRING"],$q);

 $id=intval($q["id"]);

 $r=$conn->query("SELECT * FROM outfits WHERE id=$id");
 $d=$r->fetch_assoc();

 if($d){
  @unlink(__DIR__."/uploads/minggu{$d['week']}/".$d["filename"]);
 }

 $conn->query("DELETE FROM outfits WHERE id=$id");

 echo json_encode(["ok"=>1]);
 exit;
}