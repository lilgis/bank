<?php
//sanitize before saving
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


 function retUid($db, $username){
    $results = returntb1($db);
    while ($row = $results->fetchArray()) {//for each row
        if($row['uname']==$username){
            return $row['uid'];
        }
    }
}

function returntb1($db){
    $results = $db->query('SELECT * FROM tb1');//stores query in assoc array
    return $results;
}

function addFile($db, $uid, $location, $projName){//adds file to db if it doesn't already exist
    $query = "insert into udata (user, contentLocation, projName) values ('".$uid."', '".$location."', '".$projName."')";
    $db->exec($query);
}

function hasAccess($db, $uid, $location){//checks if location of doc is associated with user id
    $results = $db->query("SELECT * FROM udata WHERE user = \"".$uid."\";");
    while ($row = $results->fetchArray()) {//for each row
        if($row['contentLocation']==$location){
            return true;
        }
    }
    return false;
}

function retProjName($db, $location){
    $results = $db->query('SELECT * FROM udata');
    while ($row = $results->fetchArray()) {//for each row
        if($row['contentLocation']==$location){
            return $row['projName'];
        }
    }
}

function updateName($db, $location, $projName){
    $query = "UPDATE udata set projName=\"".$projName."\" where contentLocation = \"".$location."\";";
    $db->exec($query);
}

$username = $_SESSION["username"];
$uid = retUid($db, $username);
$disabled = "";

if(isset($_POST["saveButton"])){//sanitize
    $location = $_GET["file"];
    $projName = $_POST["docName"];
    $myfile = fopen($location, "w") or die("Unable to save file!");
    $data = $_POST["text"];
    fwrite($myfile, $data);
    fclose($myfile);
    updateName($db, $location, $projName);//if($projName!=retProjName($db, $location)){
}

if(isset($_POST["newSave"])){
    $location = $_GET["file"];
    $projName = $_POST["docName"];
    $myfile = fopen($location, "w") or die("Unable to save file!");
    $data = $_POST["text"];
    fwrite($myfile, $data);
    fclose($myfile);
    addFile($db, $uid, $location, $projName);
}

if(isset($_GET["file"])){
    $location = $_GET["file"];
    if(hasAccess($db, $uid, $location)==true){
        $hasAccess=true;
        $myfile = fopen($location, "r") or die("Unable to open file!");//file
        $content = fread($myfile,filesize($location));//content
        $projName = retProjName($db, $location);
        fclose($myfile);
    }else{
        $projName="Access Denied";
        $content = "";
        $disabled = "disabled";
    }
}


?>
<!DOCTYPE html>
<html>
    <head>
    <style>
    #doc {
        width:66%;
        outline: 0px solid transparent;
        padding: 12px 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    a:link, a:visited, #save {
        background-color: #f44336;
        color: white;
        padding: 14px 25px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
    }
    #save{
        background-color:forestgreen;
    }
    #save:hover{
        cursor: pointer;
    }
    a:hover, a:active {
        background-color: red;
    }
    [contenteditable] {
        outline: 0px solid transparent;
    }
    #docName{
        text-decoration: none;
        outline: none;
        border:none;
        font-size: 40px;
        font-weight: bold;
    }
    </style>
        <title><?php echo($projName) ?></title>
    </head>
    <body>
    <form method="post" action="Document.php?file=<?php echo($location)?>">
        <input type="text" id="docName" name="docName" contenteditable="true" value='<?php echo($projName);?>'>
        <textarea name="text" id="doc"><?php echo($content);?></textarea><br><br>
        <input id="save" type="submit" name="saveButton" value="Save" <?php echo($disabled); ?>/>
        <a href="/myAccount.php">Quit</a>
    </form> 
    
    </body>
</html>