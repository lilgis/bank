<?php

session_start(); 

if(!isset($_SESSION["valid"]) || $_SESSION["valid"]==false){
    header('Location: index.php');
}

class MyDB extends SQLite3 {
    function __construct() {
       $this->open('users.sql');
    }
 }
 $db = new MyDB();

function returntb1($db){
    $results = $db->query('SELECT * FROM tb1');//stores query in assoc array
    return $results;
}
function returnUdata($db){
    $results = $db->query('SELECT * FROM udata');//stores query in assoc array
    return $results;
}
function retProjName($db, $location){
    $results = $db->query('SELECT * FROM udata');
    while ($row = $results->fetchArray()) {//for each row
        if($row['contentLocation']==$location){
            return $row['projName'];
        }
    }
}

// function returnLocation($db, $uid){
//     $results = $db->query('SELECT * FROM udata WHERE user = '.$uid.';');//stores query in assoc array
//     return $results;
// }
function retUid($db, $username){
    $results = returntb1($db);
    while ($row = $results->fetchArray()) {//for each row
        if($row['uname']==$username){
            return $row['uid'];
        }
    }
}
function retProjects($db, $uid){
    $results = returnUdata($db);
    $projLoc = [];
    while ($row = $results->fetchArray()) {//for each row
        if($row['user']==$uid){
            $projLoc[] = $row['contentLocation']; 
        }
    }
    return $projLoc;
}

$username = $_SESSION["username"];
$uid = retUid($db, $username);
$projLoc = retProjects($db, $uid);//project file locations

?>
<!DOCTYPE html>
<html>
    <head>
        <title>My Account</title>
    </head>
    <body>
        <h1>Projects:</h1>
        <p>Here is where are all of your projects will appear.</p>
        <ul>
            <?php 
            for ($i = 0; $i < count($projLoc); $i++) {
                echo("<li><a href='document.php?file=".$projLoc[$i]."'>".retProjName($db, $projLoc[$i])."</a></li>");//change Project to project name  
              }
            ?>
            <li><a href="/newDocument.php">New Document</a></li>
        </ul>

        <br><br><a href="/myAccount.php" style="text-decoration:none; color:black">Back</a>
    <style>
    li {
      display: inline-block;
      margin: 10px;
    }
    a:visited, a:link{
        text-decoration: none;
        color:black;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 5px;
    }
    </style>
    </body>
</html>

