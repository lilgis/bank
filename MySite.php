<?php 

session_start(); 

if(!isset($_SESSION["valid"]) || $_SESSION["valid"]==false){
    header('Location: index.php');
}

if(isset($_POST['button1'])){
    session_destroy();
    header('Location: index.php');
}




//delete when done testing
class MyDB extends SQLite3 {
    function __construct() {
       $this->open('users.sql');
    }
 }
 $db = new MyDB();

 function returnDb($db){
    $results = $db->query('SELECT * FROM tb1');//stores query in assoc array
    return $results;
}

function retBal($db, $username){
    $results = returnDb($db);
    while ($row = $results->fetchArray()) {//for each row
        if($row['uname']==$username){
            return $row['balance'];
        }
    }
}

function increaseBalance($db, $balance, $username){

    $query = "UPDATE tb1 set balance=".$balance." where uname = \"".$username."\";";
    $db->exec($query);
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$username = $_SESSION["username"];
$balance = retBal($db, $username);

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    test_input($_POST["amount"]);
    $amount = $_POST["amount"];
    $amount = abs($amount);
    $balance += $amount;
    increaseBalance($db, $balance, $username);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>MY SITE</title>
</head>
<body>

    
    <h1><?php echo("welcome ".$username);?></h1>
    <p><?php echo "Balance: ".$balance."";?></p>
    <br>
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <label for="amount">Amount:</label>
    <input type="text" id="amount" name="amount" required><br><br>
    <input type="submit" name="add" value="Add money"><br><br>
    </form>
    <button>Send $</button>
    <form method="post">
        <input type="submit" name="button1" value="Logout"/>
    </form>
</body>
</html>
