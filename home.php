<?php 

session_start(); 

if(!isset($_SESSION["valid"]) || $_SESSION["valid"]==false){
    header('Location: index.php');
}

if(isset($_POST['button1'])){
    session_destroy();
    header('Location: index.php');
}

function userExists($db, $username){
    $results = returnDb($db);
    while ($row = $results->fetchArray()) {//for each row
        if($row['uname']==$username){
            return true;
        }
    }
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

function transfer($recipient, $sender, $amount, $db){
    if(userExists($db, $recipient) && userExists($db, $sender)){
        $recBal = retBal($db, $recipient);//receiver balance
        $sendBal = retBal($db, $sender);//sender balance
        $recBal += $amount;//add amount send to receiver
        $sendBal -= $amount;//subtract amount from sender
        updateBalance($db, $recBal, $recipient);
        updateBalance($db, $sendBal, $sender);//update balances
        return true;
    }
    return false;
}

function updateBalance($db, $balance, $username){
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
$error = "";
$success="";

if ($_SERVER["REQUEST_METHOD"] == "POST"){//transactions
    if(isset($_POST["recipient"])){//send
        $recipient = test_input($_POST["recipient"]);
        $sender = $username;
        $amount = test_input($_POST["amount"]);
        $amount = abs($amount);
        if(!userExists($db, $recipient)){
            $error = "Please enter a valid recipient";
        }else if(retBal($db, $sender)<$amount){
            $error = "Error, please send less money";
        }else{
            if(transfer($recipient, $sender, $amount, $db)==true){
                $success="Success!";
                $balance -= $amount;
            }else{
                $error = "something went wrong";
            }
        }
        unset($_POST["recipient"]);
    }
    if(isset($_POST["sender"])){//request
        $sender = test_input($_POST["sender"]);
        $recipient = $username;
        $amount = test_input($_POST["amount"]);
        $amount = abs($amount);
        if(!userExists($db, $sender)){
            $error = "Please enter a valid username";
        }else if(retBal($db, $sender)<$amount){
            $error = "Error, please request less money";
        }else{
            if(transfer($recipient, $sender, $amount, $db)==true){
                $balance += $amount;
                $success="Success!";
            }else{
                $error = "something went wrong";
            }
        }
        unset($_POST["sender"]);
    }

    if(isset($_POST["addamount"])){//add
        $amount = test_input($_POST["addamount"]);
        $amount = abs($amount);
        $balance += $amount;
        updateBalance($db, $balance, $username);
        $success="Success!";
        unset($_POST["addamount"]);
    }
    
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>MY SITE</title>
</head>
<body>

    <h1><?php echo("Welcome ".$username);?></h1>
    <p><?php echo "Karma: ".$balance."₭";?></p>
    <br>
    
    <div id="buttons">
            <button onClick="showSend()">Send ₭</button>
            <button onClick="showReq()">Request ₭</button>
            <button onClick="showAdd()">Add ₭</button><br><br>
    </div>

    <div id="send" 
    <?php 
        if(isset($_POST["show"]) && $_POST["show"]=="send"){
            echo 'style="display:block"';
        }else{
            echo'style="display:none"';
        }
    ?>>
    <form method="post" id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="recipient">To:</label>
            <input type="text" id="recipient" name="recipient" required><br><br>
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" min="1" max=""required><br><br>
            <input type="submit" name="sendTo" value="Send ₭">
            <input type="reset" value="Cancel" onClick="resetForm()"><br><br>
            <input type="hidden" name="show" value="send">
    </form>
    <p id="error" style="color:red"><?php echo $error ?></p>
    <p id="success" style="color:lime"><?php echo $success ?></p>
    </div>

    <div id="request" 
    <?php
        if(isset($_POST["show"]) && $_POST["show"]=="request"){
            echo 'style="display:block"';
        }else{
            echo'style="display:none"';
        }
    ?>>
    <form method="post" id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="sender">From:</label>
            <input type="text" id="sender" name="sender" required ><br><br>
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" min="1" max=""required><br><br>
            <input type="submit" name="requestFrom" value="Request ₭">
            <input type="reset" value="Cancel" onClick="resetForm()"><br><br>
            <input type="hidden" name="show" value="request">
    </form>
    <p id="error" style="color:red"><?php echo $error ?></p>
    <p id="success" style="color:lime"><?php echo $success ?></p>
    </div>
    
    <div id="quantity" 
    <?php 
        if(isset($_POST["show"]) && $_POST["show"]=="add"){
            echo 'style="display:block"';
        }else{
            echo'style="display:none"';
        }
    ?>>
    <form method="post" id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="addamount" required><br><br>
            <input type="submit" name="add" value="Add ₭">
            <input type="reset" value="Cancel" onClick="resetForm()"><br><br>
            <input type="hidden" name="show" value="add">
    </form>
    <p id="error" style="color:red"><?php echo $error ?></p>
    <p id="success" style="color:lime"><?php echo $success ?></p>
    </div>

    

    <form method="post">
        <input type="submit" name="button1" value="Logout"/>
    </form>

    <br><br><button><a href="/myAccount.php" style="text-decoration:none; color:black">My account</a></button>
<script>
    function resetForm(){
        document.getElementById("request").style.display = "none";
        document.getElementById("quantity").style.display = "none";
        document.getElementById("send").style.display = "none";
    }
    function showSend(){
        resetForm();    
        document.getElementById("send").style.display = "block";
    }
    function showReq(){
        resetForm();
        document.getElementById("request").style.display = "block";
    }
    function showAdd(){
        resetForm();
        document.getElementById("quantity").style.display = "block";
    }
</script>

<style>
#form{
    width: 300px;
  padding: 12px 20px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  text-align: center;
}
</style>
</body>
</html>
