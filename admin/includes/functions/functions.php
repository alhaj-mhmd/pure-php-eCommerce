<?php 
// echo the title of the page if it hase a 
// variable pagetitle
function getTitle(){
    global $pageTitle;
if (isset($pageTitle)) {
    echo $pageTitle;
}else{
    return 'Default';
}
}

// redirect fucntion v2.0
// redirect function with params 
// $errorMsg : the error message 
// $seconds : seconds nombers
function redirectHome($theMsg,$url=null,$seconds=3){
    if($url===null){
        $url='index.php';
        $link='Home Page';
    }else{
        if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!==''){
            $url=$_SERVER['HTTP_REFERER'];
            $link='Previouse Page';
        } 
    }

    echo'<div class="container mt-4">';
    echo $theMsg;
    echo"<div class='alert alert-info'> You will be redirecct after $seconds seconds to the $link";
    echo'</div>';
    header("refresh:$seconds;url=$url");
    exit();
}

// check itrm function v1.0
// check item in the database 
// $select
// $from
// $where
function checkItem($select,$from,$value){
    global $con;
    $statement = $con->prepare("SELECT $select FROM $from WHERE $select=? ");
    $statement->execute(array($value));
    $count=$statement->rowCount();
    return $count;
}

// count items functions v1.0 

function countItems($item,$table)
{
    global $con;
    $stmtCount=$con->prepare("SELECT COUNT($item) FROM $table");
    $stmtCount->execute();
    return $stmtCount->fetchColumn();
}


// Get latest records function v1.0 
// function to get latest items from datebase[users,items,comments]
// $select
//$table
//$limits
function getLatest($select,$table,$order,$limit=5){
global $con;
$getStmt=$con->prepare("SELECT $select FROM $table ORDER BY $order DESC LIMIT $limit");
$getStmt->execute();
$rows=$getStmt->fetchAll();
return $rows;
}

