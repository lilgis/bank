<!DOCTYPE html>
<?php 
//setcookie("cookie", "this is a cookie", time() + 4000, "/");
session_start();
if(!isset($_SESSION["valid"])){
    $_SERVER["valid"]= false;
}else{
    if($_SESSION["ip"]==getenv("REMOTE_ADDR")){
        header('Location: MySite.php');
    }
}
?>
<html>
<head>  
    <title>Login Page</title>
</head>
<body>
    <h2>Login</h2>
    <div id="red">
     <?php
            if (isset($_GET['error'])) {
                if($_GET['error']==1){
                    echo("<p>Invalid username or password</p>");
                }elseif($_GET['error']==2){
                    echo("<p>Username is already taken</p>");
                }
            }
        ?>
    </div>
    <form action="auth.php" method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="hidden" name="create" value="false">
        <input type="submit" value="Login"> </form>
        
        
<br>    
<button id="butt" onclick="myFunction()">Make an Account</button>
   

<div id="hidden">
    <h2>Make an account</h2>
    <form method="post" action="auth.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br><br>
    <input type="hidden" id="create" name="create" value="true"><!--test to see if works-->
    <input type="submit" value="Create">
    </form>
</div>


<style>
form{
    width: 300px;
  padding: 12px 20px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}
#hidden{
visibility: hidden;
}
#red{
    color: red;
}
</style>
<script>
function myFunction(){
document.getElementById("hidden").style.visibility = "visible";
}
</script>

</body>
</html>
