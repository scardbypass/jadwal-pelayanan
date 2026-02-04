<?php
include "db.php";
header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD']=="GET"){
 $week=intval($_GET['week']);
 $r=$conn->query("
 SELECT assign.role, people.name
 FROM assign JOIN people ON people.id=assign.person_id
 WHERE week=$week
 ");
 echo json_encode($r->fetch_all(MYSQLI_ASSOC));
 exit;
}

if($_SERVER['REQUEST_METHOD']=="POST"){
 $x=json_decode(file_get_contents("php://input"),true);

 $week=$x['week'];
 $role=$x['role'];
 $ids=$x['personIds'];

 $conn->query("DELETE FROM assign WHERE week=$week AND role='$role'");

 foreach($ids as $id){
  $conn->query("INSERT INTO assign(week,role,person_id)
   VALUES($week,'$role',$id)");
 }

 echo json_encode(["ok"=>1]);
 exit;
}