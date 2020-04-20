<?php 
/**
 * function to get categories 
 */
function getCats()
{
    global $con;
    $getcats = $con->prepare("SELECT * FROM categories ORDER BY id ASC");
    $getcats->execute();
    $cats = $getcats->fetchAll();
    return $cats;
}

/**
 * function to get items 
 */
function getItems($catid)
{
    global $con;
    $getitems = $con->prepare("SELECT * FROM items WHERE cat_id = ? ORDER BY item_id DESC");
    $getitems->execute(array($catid));
    $items = $getitems->fetchAll();
    return $items;
}
























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

/* redirect fucntion v2.0
   redirect function with params 
   $errorMsg : the error message 
   $seconds : seconds nombers
 */
function redirectHome($theMsg, $url = null, $seconds=3){
    if ($url === null){
        $url='index.php';
        $link='Home Page';
    }else {
        // HTTP_REFERER : tha perviouse page that directed me the current page
        if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!=='') {
            $url = $_SERVER['HTTP_REFERER'];
            $link = 'Previouse Page';
        } else {
            $url = 'index.php';
            $link = 'Home Page';
        }
    }

    echo'<div class="container mt-4">';
    echo $theMsg;
    echo"<div class='alert alert-info'> You will be redirecct after $seconds seconds to the $link";
    echo'</div>';
    header("refresh:$seconds;url=$url");
    exit();
}

/* check item function v1.0
 check item in the database 
 $select : the item to select 
 $from : the table to select from
 $value : the item condition (the value)
 */
function checkItem($select,$from,$value){
    global $con;
    $statement = $con->prepare("SELECT $select FROM $from WHERE $select = ? ");
    $statement->execute(array($value));
    $count = $statement->rowCount();
    return $count;
}

/* count items functions v1.0 
fetchColumn : fetch the one column 
*/
function countItems($item,$table)
{
    global $con;
    $stmtCount=$con->prepare("SELECT COUNT($item) FROM $table");
    $stmtCount->execute();
    return $stmtCount->fetchColumn();
}
/*
    note: this function is the best for count 
    $total_jobs = $this->db->query("SELECT count(*) AS total_jobs FROM job")->fetchColumn();
*/


/* 
    Get latest records function v1.0 
    function to get latest items from datebase[users,items,comments]
    $select
    $table
    $limits
*/
function getLatest($select, $table, $order, $limit = 5 , $where = ''){
    global $con;
    if (! empty($where)) {
        $where = "WHERE " . $where;
    }
    $getStmt = $con->prepare("SELECT $select FROM $table $where ORDER BY $order DESC LIMIT $limit");
    $getStmt->execute();
    $rows = $getStmt->fetchAll();
    return $rows;
}

