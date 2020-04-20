<?php
  include 'init.php';?>
    <div class="container login-page">
        <h2 class="text-center">
            <span class="login active">Login</span> |
            <span class="signup">Signup</span>
        </h2>
        <form class="login" action="">
            <input class="form-control" type="text" name="username">
            <input class="form-control" type="text" name="password">
            <input class="btn btn-primary" type="submit" value="login">
        </form>
        <form class="signup" action="">
            <input class="form-control" type="text" name="username">
            <input class="form-control" type="text" name="password">
            <input class="form-control" type="email" name="email">
            <input class="btn btn-primary" type="submit" value="signup">
        </form>
    </div>
  <?php include $tpl.'footer.php'; 
?>