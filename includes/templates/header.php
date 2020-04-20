<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php getTitle(); ?></title>
    <link rel="stylesheet" href="<?php echo $css; ?>bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $css; ?>all.min.css">
    <link rel="stylesheet" href="<?php echo $css ;?>frontend.css">
</head>

<body>
<div class="upper-bar">
  <div class="container">
   <a href="login.php">
     <span class="pull-right">Login/SignUp</span>
   </a>
  </div>
</div>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="index.php">Home</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav">
      <?php 
        foreach (getCats() as $cat) {?>
          <li class="nav-item"><a  class="nav-link" href="categories.php?pageid=<?= $cat['id']?>&pagename=<?= $cat['name']?>"><?= $cat['name']?></a></li>
        <?php }
      ?>
    </ul>

  </div>
</nav>