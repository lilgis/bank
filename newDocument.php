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

$username = $_SESSION["username"];
$uid = retUid($db, $username);
$location = "projects/".bin2hex(random_bytes(10))."";//unique identifier
$content = "start typing here";
$projName = "New Document";


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
    <!-- contenteditable="true" -->
    <form method="post" action="document.php?file=<?php echo($location)?>">
        <input type="text" id="docName" name="docName" contenteditable="true" value='<?php echo($projName);?>'>
        <textarea name="text" id="doc"><?php echo($content);?></textarea><br><br>
        <input id="save" type="submit" name="newSave" value="Save"/>
        <a href="/myAccount.php">Quit</a>
    </form> 
    
    </body>
</html>