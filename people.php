<?php
include "db.php";
header("Content-Type: application/json");

/* ===========================
   GET PEOPLE
=========================== */
if($_SERVER['REQUEST_METHOD']==='GET'){

 $division=$_GET['division'] ?? '';

 $sql="SELECT * FROM people";
 if($division){
  $sql.=" WHERE division=?";
  $st=$conn->prepare($sql);
  $st->bind_param("s",$division);
 }else{
  $st=$conn->prepare($sql);
 }

 $st->execute();
 $res=$st->get_result();

 echo json_encode($res->fetch_all(MYSQLI_ASSOC));
 exit;
}

/* ===========================
   ADD PERSON
=========================== */
if($_SERVER['REQUEST_METHOD']==='POST'){

 $data=json_decode(file_get_contents("php://input"),true);

 $name=$data['name'] ?? '';
 $division=$data['division'] ?? '';

 if(!$name || !$division){
  http_response_code(400);
  echo json_encode(["error"=>"Missing data"]);
  exit;
 }

 $st=$conn->prepare(
  "INSERT INTO people(name,division) VALUES(?,?)"
 );
 $st->bind_param("ss",$name,$division);
 $st->execute();

 echo json_encode(["success"=>true]);
 exit;
}

/* ===========================
   DELETE PERSON
=========================== */
if($_SERVER['REQUEST_METHOD']==='DELETE'){

 parse_str($_SERVER['QUERY_STRING'],$q);
 $id=intval($q['id'] ?? 0);

 if(!$id){
  http_response_code(400);
  echo json_encode(["error"=>"ID missing"]);
  exit;
 }

 $st=$conn->prepare("DELETE FROM people WHERE id=?");
 $st->bind_param("i",$id);
 $st->execute();

 echo json_encode(["success"=>true]);
 exit;
}