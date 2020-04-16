<?php
// ============================
// items  page 
// ============================
ob_start();
session_start();
$pageTitle = 'Items';
if (isset($_SESSION['username'])) {
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    //manage page
    if ($do == 'manage') {
        // select data from the DB
        $stmt = $con->prepare("SELECT * FROM items");
        $stmt=$con->prepare("SELECT items.* , categories.name AS category, users.username FROM items
        INNER JOIN categories on categories.catID=items.catid
        INNER JOIN users on users.userID=items.userid");
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
                                echo '<td>' . $item['itemID'] . '</td>';
                                echo '<td>' . $item['name'] . '</td>';
                                echo '<td>' . $item['description'] . '</td>';
                                echo '<td>' . $item['price'] . '</td>';
                                echo '<td>' . $item['adddate'] . '</td>';
                                echo '<td>' . $item['category'] . '</td>';
                                echo '<td>' . $item['username'] . '</td>';
                                echo '<td>
                                    <a href="items.php?do=edit&itemid=' . $item['itemID'] . '" class="btn btn-success"><i class="fa fa-edit mr-1"></i>Edit</a>
                                    <a href="items.php?do=delete&itemid=' . $item['itemID'] . '"class="btn btn-danger  confirm"><i class="fa fa-trash mr-1"></i>Delete</a>';
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
        <!-- Add page  -->
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
                                        echo '<option value="' . $cat['catID'] . '">' . $cat['name'] . '</option>';
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
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $countrymade = $_POST['countrymade'];
            $status = $_POST['status'];
            $catid=$_POST['catid'];
            $userid=$_POST['userid'];
            //form validate
            $formerrors = array();
            if (empty($name)) {
                $formerrors[] = "username cannot be empty";
            }
            // if (empty($username)) {
            //     $formerrors[] = "username cannot be empty ";
            // }
            // if (empty($password)) {
            //     $formerrors[] = "password cannot be empty ";
            // }
            // if (empty($email)) {
            //     $formerrors[] = " email cannot be empty ";
            // }
            // if (empty($fullname)) {
            //     $formerrors[] = "   name cannot be empty  ";
            // }
            foreach ($formerrors as $error) {
                echo "<div class='alert alert-danger'>" . $error . "</div>";
            }

            // check if there is no error
            if (empty($formerrors)) {
                // insert into database    
                $stmt = $con->prepare("INSERT INTO  items ( name, description, price, adddate, countrymade, status, catid,userid )
                    VALUES (:name, :description, :price , now(), :countrymade, :status,:catid, :userid )");
                $stmt->execute(array(
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'countrymade' => $countrymade,
                    'status' => $status,
                    'catid'=> $catid,
                    'userid'=>$userid
                ));
                if ($stmt->rowCount() == 1) {
                    $theMsg = '<div class="alert alert-danger">' . $stmt->rowCount() . ' Record is  inserted </div>' . ' <h1 class="text-center">Update successed </h1>';
                    redirectHome($theMsg, 'previous');
                } else {
                    $theMsg = '<div class="alert alert-danger">Not Inserted</div>';
                    redirectHome($theMsg, 'previous');
                }
            }
        } else {
            $theMsg = "<div class='alert alert-danger'>You cant brouwse this directly </div>";
            redirectHome($theMsg);
        }
    }
    // end insert page
    //Edit page
    elseif ($do == 'edit') {
        
        $itemid = isset($_GET['itemid']) && is_numeric($_GET['itemid']) ? intval($_GET['itemid']) : 0;
        $stmt = $con->prepare("SELECT *  FROM items WHERE itemID=? ");
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
                    <input type="text" value="<?php echo $row['name'] ?>" name="name" class="form-control" autocomplete="off" required="required">
                </div>
                <!-- item description  -->
                <label class="mt-3 control-lable">description</label>
                <div class="col-6">
                    <input type="text" name="description" value="<?php echo $row['description']; ?>">
                </div>
                <!-- item price  -->
                <label class="mt-3 control-lable">price</label>
                <div class="col-6">
                    <input type="text" value="<?php echo $row['price'] ?>" name="price" class="form-control" required="required">
                </div>
                <!-- item adddate  -->
                <label class="mt-3 control-lable">Add date</label>
                <div class="col-6">
                    <input type="date" value="<?php echo $row['adddate'] ?>" name="adddate" class="form-control" required="required">
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
                                        echo '<option value="' . $cat['catID'] . '">' . $cat['name'] . '</option>';
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
    }
    //update page
    elseif ($do == 'update') { }
    //end update page
    //delete page
    elseif ($do == 'delete') { }
    //activate page
    elseif ($do == 'approve') { }
    //footer           
    include $tpl . 'footer.php';
    //session usernmae not found 
} else {
    header('Location: index.php');
    exit();
}
ob_end_flush();
