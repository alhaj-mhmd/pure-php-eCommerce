<?php
/* 
    ============================
    items  page 
    ============================ 
*/
ob_start();
session_start();
$pageTitle = 'Items';
if (isset($_SESSION['username'])) {
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    //manage page
    if ($do == 'manage') {
        // select data from the DB
        $stmt=$con->prepare("SELECT items.* , categories.name AS category, users.username FROM items
        INNER JOIN categories on categories.id = items.cat_id
        INNER JOIN users on users.userID = items.member_id");
        $stmt->execute();
        //asign data to the variabls
        $items = $stmt->fetchAll();
        ?>
        <div class="container text-center">
            <h1>Manage Items </h1>
            <table class="table table-bordered">
                <th>#ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Add Date</th>
                <th>Category Name</th>
                <th>User Name</th>
                <th>Control</th>
                <?php
                        foreach ($items as $item) {
                            echo '<tr>';
                                echo '<td>' . $item['item_id'] . '</td>';
                                echo '<td>' . $item['name'] . '</td>';
                                echo '<td>' . $item['description'] . '</td>';
                                echo '<td>' . $item['price'] . '</td>';
                                echo '<td>' . $item['add_date'] . '</td>';
                                echo '<td>' . $item['category'] . '</td>';
                                echo '<td>' . $item['username'] . '</td>';
                                echo '<td>
                                    <a href="items.php?do=edit&itemid=' . $item['item_id'] . '" class="btn btn-info"><i class="fa fa-edit mr-1"></i>Edit</a>
                                    <a href="items.php?do=delete&itemid=' . $item['item_id'] . '"class="btn btn-danger  confirm"><i class="fa fa-trash mr-1"></i>Delete</a>';
                                    if ($item['approve'] == 0) {
                                            echo '<a href="items.php?do=approve&itemid=' . $item['item_id'] . '" class="btn btn-success ml-1"><i class="fa fa-check mr-1"></i>Approve</a>';

                                        }
                                echo '</td>';
                            echo '</tr>';
                        }
                        ?>
            </table>
            <a href="items.php?do=add" class=" mt-3 btn btn-primary btn-lg"><i class="fa fa-plus"></i> New Item</a>
        </div>
        <?php 
    }
    //add page
    elseif ($do == 'add') {
        ?>
        <div class="container">
            <h1 class="text-center">Add Item </h1>
            <form class="form-horizontal" action="?do=insert" method="POST">
                <div class="form-group">
                    <!-- item name  -->
                    <label class=" mt-3 control-lable">Name</label>
                    <div class="col-6">
                        <input type="text" name="name" class="form-control" required="required">
                    </div>
                    <!-- item description  -->
                    <label class="mt-3 control-lable">Description</label>
                    <div class="col-6">
                        <input type="text" name="description" class="form-control">
                    </div>
                    <!-- item price  -->
                    <label class="mt-3 control-lable">Price</label>
                    <div class="col-6">
                        <input type="text" name="price" class="form-control">
                    </div>
                    <!-- item country made  -->
                    <label class="mt-3 control-lable">Country Made</label>
                    <div class="col-6">
                        <input type="text" name="countrymade" class="form-control">
                    </div>
                    <!-- item status  -->
                    <label class="mt-3 control-lable">Status</label>
                    <div class="col-6">
                        <select name="status" id="" class="form-control">
                            <option value="0">...</option>
                            <option value="1">New</option>
                            <option value="2">Like New</option>
                            <option value="3">Used</option>
                            <option value="4">Very Old</option>
                        </select>
                    </div>
                    <!-- item rating  -->
                    <label class="mt-3 control-lable">Rating</label>
                    <div class="col-6">
                        <select name="rating" id="" class="form-control">
                            <option value="1">*</option>
                            <option value="2">**</option>
                            <option value="3">***</option>
                            <option value="4">****</option>
                        </select>
                    </div>
                    <!-- item member -->
                    <label class="mt-3 control-lable">Member</label>
                    <div class="col-6">
                        <select name="userid" id="" class="form-control">
                            <option value="0">...</option>
                            <?php
                                    $stmt = $con->prepare("SELECT * FROM users");
                                    $stmt->execute();
                                    $users = $stmt->fetchAll();
                                    foreach ($users as  $user) {
                                        echo '<option value="' . $user['userID'] . '">' . $user['username'] . '</option>';
                                    }
                                    ?>
                        </select>
                    </div>
                    <!-- item Category -->
                    <label class="mt-3 control-lable">Category</label>
                    <div class="col-6">
                        <select name="catid" id="" class="form-control">
                            <option value="0">...</option>
                            <?php
                                    $stmtcat = $con->prepare("SELECT * FROM categories");
                                    $stmtcat->execute();
                                    $cats = $stmtcat->fetchAll();
                                    foreach ($cats as  $cat) {
                                        echo '<option value="' . $cat['id'] . '">' . $cat['name'] . '</option>';
                                    }
                                    ?>
                        </select>
                    </div>
                    <!-- buttun -->
                    <div class="col-6 mt-3">
                        <input type="submit" class="btn btn-primary" value="Add Item">
                    </div>
                </div>
            </form>
        </div>
        <?php
            }
    //insert page
    elseif ($do == 'insert') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo '<div class="container">';
            $name           = $_POST['name'];
            $description    = $_POST['description'];
            $price          = $_POST['price'];
            $countrymade    = $_POST['countrymade'];
            $status         = $_POST['status'];
            $rating         = $_POST['rating'];
            $catid          = $_POST['catid'];
            $userid         = $_POST['userid'];

            $formerrors = array();
            if (empty($name)) {
                $formerrors[] = "username cannot be empty";
            }
            
            foreach ($formerrors as $error) {
                echo "<div class='alert alert-danger'>" . $error . "</div>";
            }

            // check if there is no error
            if (empty($formerrors)) {
                // insert into database    
                $stmt = $con->prepare("INSERT INTO  items ( name, description, price, add_date, country_made, status, rating, cat_id, member_id )
                    VALUES (:name, :description, :price , now(), :countrymade, :status, :rating, :catid, :userid )");
                $stmt->execute(array(
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'countrymade' => $countrymade,
                    'status' => $status,
                    'rating' => $rating,
                    'catid'=> $catid,
                    'userid'=>$userid
                ));
                if ($stmt->rowCount() == 1) {
                    $theMsg = '<div class="alert alert-danger">' . $stmt->rowCount() . ' Record is  inserted </div>' . ' <h1 class="text-center">Update successed </h1>';
                    redirectHome($theMsg, 'previous');
                } else {
                    $theMsg = '<div class="alert alert-danger">Not Inserted</div>';
                    redirectHome($theMsg, 'previous',80000);
                }
            }
        } else {
            $theMsg = "<div class='alert alert-danger'>You cant brouwse this directly </div>";
            redirectHome($theMsg);
        }
    }
    //Edit page
    elseif ($do == 'edit') {    
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        $stmt = $con->prepare("SELECT *  FROM items WHERE item_id = ? ");
        $stmt->execute(array($itemid));
        $row = $stmt->fetch();
        if ($stmt->rowCount() > 0) { ?>
        <div class="container">
        <h1 class="text-center">Edit Item </h1>
        <form class="form-horizontal" action="?do=update" method="POST">
            <div class="form-group">
                <input type="hidden" name="itemid" value="<?php echo $itemid; ?>">
                <!-- item name  -->
                <label class=" mt-3 control-lable">Item Name</label>
                <div class="col-6">
                    <input type="text"  name="name" value="<?php echo $row['name'] ?>" class="form-control" autocomplete="off" required="required">
                </div>
                <!-- item description  -->
                <label class="mt-3 control-lable">description</label>
                <div class="col-6">
                    <input type="text" name="description" value="<?php echo $row['description']; ?>" class="form-control">
                </div>
                <!-- item price  -->
                <label class="mt-3 control-lable">price</label>
                <div class="col-6">
                    <input type="text" name="price" value="<?php echo $row['price'] ?>"  class="form-control" required="required">
                </div>
                <!-- country made -->
                <label class="mt-3 control-lable">Country Made</label>
                <div class="col-6">
                    <input type="text" name="country" value="<?php echo $row['country_made'] ?>"  class="form-control">
                </div>
                 <!-- status  -->
                 <label class="mt-3 control-lable">Status</label>
                    <div class="col-6">
                        <select name="status" id="" class="form-control">
                            <option value="0" <?php if ($row["status"] == 0) { echo "selected"; } ?>>...</option>
                            <option value="1" <?php if ($row["status"] == 1) { echo "selected"; } ?>>New</option>
                            <option value="2" <?php if ($row["status"] == 2) { echo "selected"; } ?>>Like New</option>
                            <option value="3" <?php if ($row["status"] == 3) { echo "selected"; } ?>>Used</option>
                            <option value="4" <?php if ($row["status"] == 4) { echo "selected"; } ?>>Very Old</option>
                        </select>
                    </div>
                     <!-- member -->
                     <label class="mt-3 control-lable">Member</label>
                    <div class="col-6">
                        <select name="userid" id="" class="form-control">
                            <option value="0">...</option>
                            <?php
                                    $stmt = $con->prepare("SELECT * FROM users");
                                    $stmt->execute();
                                    $users = $stmt->fetchAll();
                                    foreach ($users as  $user) {
                                        echo '<option value="' . $user['userID'] . '"'; if ($row["member_id"] == $user["userID"]) { echo "selected"; } echo '>' . $user['username'] . '</option>';
                                    }
                                    ?>
                        </select>
                    </div>
                    <!-- Category -->
                    <label class="mt-3 control-lable">Category</label>
                    <div class="col-6">
                        <select name="catid" id="" class="form-control">
                            <option value="0">...</option>
                            <?php
                                    $stmtcat = $con->prepare("SELECT * FROM categories");
                                    $stmtcat->execute();
                                    $cats = $stmtcat->fetchAll();
                                    foreach ($cats as  $cat) {
                                        echo '<option value="' . $cat['id'] . '"'; if ($row["cat_id"] == $cat["id"]) { echo "selected"; } echo '>' . $cat['name'] . '</option>';
                                    }
                                    ?>
                        </select>
                    </div>
                <!-- buttun   -->
                <div class="col-6 mt-3">
                    <input type="submit" class="btn btn-primary" value="edit">
                </div>
            </div>
        </form>
     </div>
        <?php
        }
        //manage page
    
        // select data from the DB
        $stmt = $con->prepare("SELECT comments.* , items.name AS item , users.username AS user
                                FROM  comments
                                INNER JOIN items ON items.item_id = comments.item_id
                                INNER JOIN users ON users.userID = comments.user_id
                                WHERE items.item_id = ?
                            ");
        $stmt->execute(array($itemid));
        //asign data to the variabls
        $rows = $stmt->fetchAll();
        if (!empty($rows)) {
          
      
        ?>
        <div class="container text-center">
            <h1>Manage [ <?php echo $row['name'] ?> ] Comments </h1>
            <table class="table table-bordered">
                <th>#ID</th>
                <th>Comment</th>
                <th>Comment Date</th>
                <th>Member</th>
                <th>Control</th>
                <?php
                        foreach ($rows as $row) {
                            echo '<tr>';
                            echo '<td>' . $row['c_id'] . '</td>';
                            echo '<td>' . $row['comment'] . '</td>';
                            echo '<td>' . $row['comment_date'] . '</td>';
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
    <?php   }

    }
    //update page
    elseif ($do == 'update') { 
        echo '<div class="container">';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $itemid         = $_POST['itemid'];
            $name           = $_POST['name'];
            $description    = $_POST['description'];
            $price          = $_POST['price'];
            $status         = $_POST['status'];
            $country        = $_POST["country"];
            $member         = $_POST['userid'];
            $category       = $_POST['catid'];

            $formerrors = array();

            if (empty($name)) {
                $formerrors[] = "username cannot be empty";
            }
            foreach ($formerrors as $error) {
                echo "<div class='alert alert-danger'>" . $error . "</div>";
            }
            // check if there is no error
            if (empty($formerrors)) {
                // update database     
                $stmt = $con->prepare(" UPDATE 
                                            items
                                        SET 
                                            name = ?, description = ?, price = ? , status = ?, country_made = ?, cat_id = ? , member_id=?
                                        WHERE 
                                            item_id = ?");
                $stmt->execute(array( $name , $description, $price, $status, $country, $category, $member, $itemid ));
                $theMsg = '<div class="alert alert-danger">' . $stmt->rowCount() . ' row updated </div>' . ' <h1 class="text-center">Update successed </h1>';
                redirectHome($theMsg, 'previous');
            }
        } else {
            $theMsg = '<div class="alert alert-danger">Not updated</div>';
            redirectHome($theMsg, 'previous');
        }
        echo '</div>';
    }
    //delete page
    elseif ($do == 'delete') {
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        $check = checkItem('item_id', 'items', $itemid);
        if ($check > 0) {
            $stmt = $con->prepare('DELETE FROM items WHERE item_id = :itemid');
            $stmt->bindparam(":itemid", $itemid);
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
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        $check = checkItem('item_id', 'items', $itemid);
        if ($check > 0) {
            $stmt = $con->prepare('UPDATE items SET approve = 1 WHERE item_id = :itemid');
            $stmt->bindparam(":itemid", $itemid);
            $stmt->execute();
            $theMsg = '<div class="alert alert-danger">' . $stmt->rowCount() . ' row updated </div>' . ' <h1 class="text-center">Activate successed </h1>';
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
ob_end_flush();
