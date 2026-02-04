<?php
include "db.php";
header("Content-Type: application/json");

/* LIST */
if($_SERVER['REQUEST_METHOD']=="GET" && isset($_GET['week'])){
 $week=$_GET['week'];
 $r=$conn->query("SELECT * FROM songs WHERE week=$week ORDER BY ordering");
 echo json_encode($r->fetch_all(MYSQLI_ASSOC));
 exit;
}

/* ADD */
if($_SERVER['REQUEST_METHOD']=="POST" && !isset($_GET['reorder'])){
 $x=json_decode(file_get_contents("php://input"),true);

 $r=$conn->query("SELECT MAX(ordering) m FROM songs WHERE week=".$x['week']);
 $o=$r->fetch_assoc()['m']+1;

 $conn->query("INSERT INTO songs(week,title,youtube,sequencer,lyrics,ordering)
 VALUES({$x['week']},'{$x['title']}','{$x['youtube']}',
 '{$x['sequencer']}','{$x['lyrics']}',$o)");

 echo json_encode(["ok"=>1]);
 exit;
}

/* REORDER */
if(isset($_GET['reorder'])){
 $x=json_decode(file_get_contents("php://input"),true);

 foreach($x['orders'] as $o){
  $conn->query("UPDATE songs SET ordering={$o['order']} WHERE id={$o['id']}");
 }

 echo json_encode(["ok"=>1]);
 exit;
}

/* DELETE */
if($_SERVER['REQUEST_METHOD']=="DELETE"){
 parse_str($_SERVER['QUERY_STRING'],$q);
 $conn->query("DELETE FROM songs WHERE id=".$q['id']);
 echo json_encode(["ok"=>1]);
 exit;
}