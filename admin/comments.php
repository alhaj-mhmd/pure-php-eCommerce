<?php
/*
    == Memebers Manage page
    == view comments | Approve comment | 
*/
session_start();
$pageTitle = "Comments";

if (isset($_SESSION['username'])) {
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    //manage page
    if ($do == 'manage') {
        // select data from the DB
        $stmt = $con->prepare("SELECT comments.* , items.name AS item , users.username AS user
                                FROM  comments
                                INNER JOIN items ON items.item_id = comments.item_id
                                INNER JOIN users ON users.userID = comments.user_id
                            ");
        $stmt->execute();
        //asign data to the variabls
        $rows = $stmt->fetchAll();
        ?>
        <div class="container text-center">
            <h1>Manage Comments </h1>
            <table class="table table-bordered">
                <th>#ID</th>
                <th>Comment</th>
                <th>Comment Date</th>
                <th>Item</th>
                <th>Member</th>
                <th>Control</th>
                <?php
                        foreach ($rows as $row) {
                            echo '<tr>';
                            echo '<td>' . $row['c_id'] . '</td>';
                            echo '<td>' . $row['comment'] . '</td>';
                            echo '<td>' . $row['comment_date'] . '</td>';
                            echo '<td>' . $row['item'] . '</td>';
                            echo '<td>' . $row['user'] . '</td>';
                            echo '<td>
                                    <a href="comments.php?do=edit&commentid=' . $row['c_id'] . '" class="btn btn-success"><i class="fa fa-edit mr-1"></i>Edit</a>
                                    <a href="comments.php?do=delete&commentid=' . $row['c_id'] . '"class="btn btn-danger  confirm"><i class="fa fa-trash mr-1"></i>Delete</a>';
                            if ($row['status'] == 0) {
                                echo '<a href="comments.php?do=approve&commentid=' . $row['c_id'] . '" class="btn btn-secondary ml-1"><i class="fa fa-check"></i>Approve</a>';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
            </table>
        </div>
    <?php } 
    //Edit page
    elseif ($do == 'edit') {
        $commentid = isset($_GET['commentid']) && is_numeric($_GET['commentid']) ? intval($_GET['commentid']) : 0;
        $stmt = $con->prepare("SELECT *  FROM comments WHERE c_id = ? ");
        $stmt->execute(array($commentid));
        $row = $stmt->fetch();
        if ($stmt->rowCount() > 0) { ?>
        <div class="container">
            <h1 class="text-center">Edit Comment </h1>
            <form class="form-horizontal" action="?do=update" method="POST">
                <div class="form-group">
                    <input type="hidden" name="commentid" value="<?php echo $commentid; ?>">
                    <!-- comment  -->
                    <label class=" mt-3 control-lable">Comment</label>
                    <div class="col-6">
                        <input type="text" value="<?php echo $row['comment'] ?>" name="comment" class="form-control" autocomplete="off">
                    </div>
                    <!-- buttun   -->
                    <div class="col-6 mt-3">
                        <input type="submit" class="btn btn-primary" value="edit">
                    </div>
                </div>
            </form>
        </div>
        <?php } else {
        $theMsg = '<div class="alert alert-danger"> Not found comment</div>';
        redirectHome($theMsg);
        }
    }
    //update page
    elseif ($do == 'update') {
        echo '<div class="container">';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $commentid = $_POST['commentid'];
            $comment = $_POST['comment'];
        }
            $stmt = $con->prepare("UPDATE comments SET comment = ? WHERE c_id = ?");
            $stmt->execute(array($comment, $commentid));
            $theMsg = '<div class="alert alert-danger">' . $stmt->rowCount() . ' row updated </div>' . ' <h1 class="text-center">Update successed </h1>';
            redirectHome($theMsg, 'previous');
      
        echo '</div>';
    }
    //delete page
    elseif ($do == 'delete') {
        $commentid = isset($_GET['commentid']) && is_numeric($_GET['commentid']) ? intval($_GET['commentid']) : 0;
        $check = checkItem('c_id', 'comments', $commentid);
        if ($check > 0) {
            $stmt = $con->prepare('DELETE FROM comments WHERE c_id = :commentid');
            $stmt->bindparam(":commentid", $commentid);
            $stmt->execute();
            $theMsg = '<div class="alert alert-danger">' . $stmt->rowCount() . ' row Deleted </div>' . ' <h1 class="text-center">Delete successed </h1>';
            redirectHome($theMsg);
        } else {
            $theMsg = '<div class="alert alert-danger">Not exist</div>';
            redirectHome($theMsg);
        }
    }
    //approve page
    elseif ($do == 'approve') {
        $commentid = isset($_GET['commentid']) && is_numeric($_GET['commentid']) ? intval($_GET['commentid']) : 0;
        $check = checkItem('c_id', 'comments', $commentid);
        if ($check > 0) {
            $stmt = $con->prepare('UPDATE comments SET status = 1 WHERE c_id = :commentid');
            $stmt->bindparam(":commentid", $commentid);
            $stmt->execute();
            $theMsg = '<div class="alert alert-danger">' . $stmt->rowCount() . ' row updated </div>' . ' <h1 class="text-center">Approve successed </h1>';
            redirectHome($theMsg);
        } else {
            $theMsg = '<div class="alert alert-danger">Not exist</div>';
            redirectHome($theMsg);
        }
    }
    //footer           
    include $tpl . 'footer.php';
    //session usernmae not found 
} else {
    header('Location: index.php');
    exit();
}
