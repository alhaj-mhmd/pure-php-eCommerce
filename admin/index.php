 <?php
session_start();
$nonavbar='';
$pageTitle='Login';
if(isset($_SESSION['username'])){
	header('Location: dashboard.php');//redirect to dashboard.php page
}
     include 'init.php';
    
    //   ched if the user is coming from http post reques  
  if($_SERVER['REQUEST_METHOD'] == 'POST'){
      $username=$_POST['user'];
      $password = $_POST['pass'];
      $hashedpass = sha1($password);

      //check if the user exists in database
      $stmt = $con->prepare("SELECT userID, username, password FROM users WHERE username=? AND password = ? AND groupID = 1 LIMIT 1");
      $stmt->execute(array($username,$hashedpass));
      $row = $stmt->fetch();
      $count = $stmt->rowCount();
      if ($count>0) {
        $_SESSION['username'] = $username;//register session name
        $_SESSION['id'] = $row['userID'];
        header('Location: dashboard.php');
        exit();
        }
 }

 ?>

 <form class='login' action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
     <input class="form-control" type="text" name="user" id="" placeholder="UserName" autocomplete>
     <input class="form-control" type="password" name="pass" id="" placeholder="PassWord" autocomplete>
     <input class="btn " type="submit" value="Login" autocomplete>
 </form>

 <?php include $tpl.'footer.php'; ?>