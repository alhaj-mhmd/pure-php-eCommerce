<?php
// ============================
// template page 
// ============================
ob_start();
session_start();
$pageTitle='';
if (isset($_SESSION['username'])) {
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    //manage page
    if ($do == 'manage') { }
    //add page
    elseif ($do == 'add') { }
    //insert page
    elseif ($do == 'insert') { }
    //Edit page
    elseif ($do == 'edit') { }
    //update page
    elseif ($do == 'update') { } 
    //delete page
    elseif ($do == 'delete') { } 
    //activate page
    elseif ($do == 'activate') { }
    //footer           
    include $tpl . 'footer.php';
    //session usernmae not found 
} else {
    header('Location: index.php');
    exit();
}
ob_end_flush();
