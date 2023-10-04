<?php
session_start();
//$IP = getenv ( "REMOTE_ADDR" );

class MyDB extends SQLite3 {
    function __construct() {
       $this->open('users.sql');
    }
 }
 $db = new MyDB();
//  if(!$db) {
//     echo $db->lastErrorMsg();
//  } else {
//     echo "Opened database successfully\n";
//  }

function returnDb($db){
    $results = $db->query('SELECT * FROM tb1');//stores query in assoc array
    return $results;
}

function userExists($db, $username){
    $results = returnDb($db);
    while ($row = $results->fetchArray()) {//for each row
        if($row['uname']==$username){
            return true;
        }
    }
}

function retPass($db, $username){
    $results = returnDb($db);
    while ($row = $results->fetchArray()) {//for each row
        if($row['uname']==$username){
            return $row['pass'];
        }
    }
}

function addUser($username, $password, $db){
    $db->exec("insert into tb1 (uname, pass, balance) values ('".$username."', '".$password."', '0')");
}

//check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $username = $_POST["username"];
    $password = $_POST["password"];
    $type = $_POST["create"];

    userExists($db, $username);
    //returnDb($db);

//check if they are making an account
    if($type=="true"){
        if(userExists($db, $username)==false){
            addUser($username, $password, $db);
            header("Location: MySite.php");
        }else{
            header('Location: index.php?error=2'); 
        }
    }else{
       if(retPass($db, $username)==$password){
            $_SESSION["valid"]=true;
            $_SESSION["username"]=$username;
            $_SESSION["ip"]=getenv("REMOTE_ADDR");
            //post balance
            header("Location: MySite.php");
            exit();
       }else{
            header('Location: index.php?error=1');
            exit();
       } 
    }
}
   
    
/*pseudo code for cookie:
    set cookie
    store cookie in db
    if cookie == cookie in db then login is automatic.
*/

/*
pseudo code for session possibility:
    make sessions with session info like username
    store sessionid as a cookie so it can be loaded up again?

    
*/
    
        //session_start();
        //$_SESSION["username"] = $username;
        //$cookie_lifetime = 60 * 60 * 24 * 7; // 7 days
        //setcookie(session_name(), session_id(), time() + $cookie_lifetime);

?>
