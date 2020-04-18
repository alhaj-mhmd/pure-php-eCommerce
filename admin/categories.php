<?php
/* 
    ============================
        Category  page 
    ============================
 */
ob_start();
session_start();
$pageTitle = 'Categories';
if (isset($_SESSION['username'])) {
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    //manage page
    if ($do == 'manage') {
        $sort = 'ASC';
        $sort_array = array('ASC', 'DESC');
        if (isset($_GET['sort']) && in_array($_GET['sort'], $sort_array)) {
            $sort = $_GET['sort'];
        }
        // select data from the DB
        $stmt = $con->prepare("SELECT * FROM categories ORDER BY ordering $sort");
        $stmt->execute();
        //asign data to the variabls
        $rows = $stmt->fetchAll();
        ?>
        <div class="container text-center">
            <h1 class="my-4">Manage Categories </h1>
            <div>
                Ordering
                [
                <a href="?sort=ASC">ASC</a> |
                <a href="?sort=DESC">DESC</a>
                ]
            </div>
            <table class="table table-bordered">
                <th>#catID</th>
                <th>Category Name</th>
                <th>Description</th>
                <th>ordering</th>
                <th>Visible</th>
                <th>Allow Comment</th>
                <th>Allow Ads</th>
                <th>Controls</th>
                <?php
                        foreach ($rows as $row) {
                            echo '<tr>';
                            echo '<td>' . $row['id'] . '</td>';
                            echo '<td>' . $row['name'] . '</td>';
                            echo '<td>' . $row['description'] . '</td>';
                            echo '<td>' . $row['ordering'] . '</td>';
                            echo '<td>';
                            if ($row['visibility'] == 0) {
                                echo 'visibile';
                            } else {
                                echo 'hidden';
                            }
                            echo '</td>';
                            echo '<td>';
                            if ($row['allow_comment'] == 0) {
                                echo 'Allowed Comments';
                            } else {
                                echo 'No Comments';
                            }
                            echo '</td>';
                            echo '<td>';
                            if ($row['allow_ads'] == 0) {
                                echo 'Allowed Ads';
                            } else {
                                echo 'No Ads';
                            }
                            echo '</td>';
                            echo '<td>
                                    <a href="categories.php?do=edit&catid=' . $row['id'] . '" class="btn btn-success"><i class="fa fa-edit mr-1"></i>Edit</a>
                                    <a href="categories.php?do=delete&catid=' . $row['id'] . '"class="btn btn-danger  confirm"><i class="fa fa-trash mr-1"></i>Delete</a>
                                    </td>';
                            echo '</tr>';
                        }
                        ?>
            </table>
            <a href="categories.php?do=add" class="mt-3 btn btn-primary btn-lg"><i class="fa fa-plus"></i> New Category</a>
        </div>
        <?php
            }
    //add page
    elseif ($do == 'add') {
            ?>
        <div class="container">
            <h1 class="text-center">Add Category </h1>
            <form class="form-horizontal" action="?do=insert" method="POST">
                <div class="form-group">
                    <!-- cat name  -->
                    <label class=" mt-3 control-lable">Name</label>
                    <div class="col-6">
                        <input type="text" name="name" class="form-control" autocomplete="off" required="required">
                    </div>
                    <!-- cat description  -->
                    <label class="mt-3 control-lable">Description</label>
                    <div class="col-6">
                        <input type="text" name="description" class="form-control">
                    </div>
                    <!-- cat Ordering  -->
                    <label class="mt-3 control-lable">Ordering</label>
                    <div class="col-6">
                        <input type="text" name="ordering" class="form-control" value="">
                    </div>
                    <!--  cat visible -->
                    <label class="mt-3 control-lable">Visible</label>
                    <div class="col-6">
                        <!-- vis-yes is from: visible yes 
                              vis-no is form : visible no -->
                        <div class="form-check">
                            <input id="vis-yes" type="radio" name="visible" class="form-check-input" value="0" checked>
                            <label for="vis-yes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input id="vis-no" type="radio" name="visible" class="form-check-input" value="1">
                            <label for="vis-no">No</label>
                        </div>
                    </div>
                    <!--  cat allow comment -->
                    <label class="mt-3 control-lable">Allow Comment</label>
                    <div class="col-6">
                        <!-- com-yes is from: comment yes 
                              com-no is form : comment no -->
                        <div class="form-check">
                            <input id="com-yes" type="radio" name="comment" class="form-check-input" value="0" checked>
                            <label for="com-yes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input id="com-no" type="radio" name="comment" class="form-check-input" value="1">
                            <label for="com-no">No</label>
                        </div>
                    </div>
                    <!--  cat allow ads -->
                    <label class="mt-3 control-lable">Allow Ads</label>
                    <div class="col-6">
                        <!-- ads-yes is from: ads yes - ads-no is form : ads no -->
                        <div class="form-check">
                            <input id="ads-yes" type="radio" name="ads" class="form-check-input" value="0" checked>
                            <label for="ads-yes" class="form-check-label">Yes</label>
                        </div>
                        <div class="form-check">
                            <input id="ads-no" type="radio" name="ads" class="form-check-input" value="1">
                            <label for="ads-no">No</label>
                        </div>
                    </div>
                    <!-- buttun -->
                    <div class="col-6 mt-3">
                        <input type="submit" class="btn btn-primary" value="Add Category">
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
            $ordering = $_POST['ordering'];
            $visible = $_POST['visible'];
            $comment = $_POST['comment'];
            $ads = $_POST['ads'];
            // form validate
            if ($name !== '') {
                if (checkItem('name', 'categories', $name) == 1) {
                    $theMsg = '<div class="alert alert-danger">The category is exsit</div>';
                    redirectHome($theMsg, 'previous');
                } else {
                    // insert into database  
                    $stmt = $con->prepare(" INSERT INTO 
                                                    categories (name, description, ordering, visibility, allow_comment, allow_ads)
                                            VALUES 
                                                    (:name, :description, :ordering, :visible, :comment, :ads) ");
                    $stmt->execute(array(
                        'name' => $name,
                        'description' => $description,
                        'ordering' => $ordering,
                        'visible' => $visible,
                        'comment' => $comment,
                        'ads' => $ads
                    ));
                    if ($stmt->rowCount() == 1) {
                        $theMsg = '<div class="alert alert-danger">' . $stmt->rowCount() . ' Record is  inserted </div>' . ' <h1 class="text-center">Update successed </h1>';
                        redirectHome($theMsg, 'previous');
                    } else {
                        $theMsg = '<div class="alert alert-danger">Not Inserted</div>';
                        redirectHome($theMsg, 'previous');
                    }
                }
            }
            echo '</div>';
        } else {
            $theMsg = "<div class='alert alert-danger'>You cant brouwse this directly </div>";
            redirectHome($theMsg);
        }
    }
    //Edit page
    elseif ($do == 'edit') {
                $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
                $stmt = $con->prepare("SELECT *  FROM categories WHERE id = ? ");
                $stmt->execute(array($catid));
                $row = $stmt->fetch();
                if ($stmt->rowCount() > 0) { ?>
            <div class="container">
                <h1 class="text-center">Edit Category </h1>
                <form class="form-horizontal" action="?do=update" method="POST">
                    <div class="form-group">
                    <input type="hidden" name="catid" value="<?php echo $catid; ?>">
                        <!-- cat name  -->
                        <label class=" mt-3 control-lable">Name</label>
                        <div class="col-6">
                            <input type="text" name="name" class="form-control" autocomplete="off" required="required" value="<?php echo $row['name']; ?>">
                        </div>
                        <!-- cat description  -->
                        <label class="mt-3 control-lable">Description</label>
                        <div class="col-6">
                            <input type="text" name="description" class="form-control" value="<?php echo $row['description']; ?>">
                        </div>
                        <!-- cat Ordering  -->
                        <label class="mt-3 control-lable">Ordering</label>
                        <div class="col-6">
                            <input type="text" name="ordering" class="form-control" value="<?php echo $row['ordering']; ?>">
                        </div>
                        <!--  cat visibility -->
                        <label class="mt-3 control-lable">Visible</label>
                        <div class="col-6">
                            <!-- vis-yes is from: visible yes 
                              vis-no is form : visible no -->
                            <div class="form-check">
                                <input id="vis-yes" type="radio" name="visible" class="form-check-input" value="0" <?php if ($row['visibility'] == 0) {
                                                                                                                                    echo 'checked';
                                                                                                                                } ?>>
                                <label for="vis-yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input id="vis-no" type="radio" name="visible" class="form-check-input" value="1" <?php if ($row['visibility'] == 1) {
                                                                                                                                    echo 'checked';
                                                                                                                                } ?>>
                                <label for="vis-no">No</label>
                            </div>
                        </div>
                        <!--  cat allow comment allowcomment -->
                        <label class="mt-3 control-lable">Allow Comment</label>
                        <div class="col-6">
                            <!-- com-yes is from: comment yes 
                              com-no is form : comment no -->
                            <div class="form-check">
                                <input id="com-yes" type="radio" name="comment" class="form-check-input" value="0" <?php if ($row['allow_comment'] == 0) {
                                                                                                                                    echo 'checked';
                                                                                                                                } ?>>
                                <label for="com-yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input id="com-no" type="radio" name="comment" class="form-check-input" value="1" <?php if ($row['allow_comment'] == 1) {
                                                                                                                                    echo 'checked';
                                                                                                                                } ?>>
                                <label for="com-no">No</label>
                            </div>
                        </div>
                        <!--  cat allow ads allowads-->
                        <label class="mt-3 control-lable">Allow Ads</label>
                        <div class="col-6">
                            <!-- ads-yes is from: ads yes - ads-no is form : ads no -->
                            <div class="form-check">
                                <input id="ads-yes" type="radio" name="ads" class="form-check-input" value="0" <?php if ($row['allow_ads'] == 0) {
                                                                                                                                echo 'checked';
                                                                                                                            } ?>>
                                <label for="ads-yes" class="form-check-label">Yes</label>
                            </div>
                            <div class="form-check">
                                <input id="ads-no" type="radio" name="ads" class="form-check-input" value="1" <?php if ($row['allow_ads'] == 1) {
                                                                                                                                echo 'checked';
                                                                                                                            } ?>>
                                <label for="ads-no">No</label>
                            </div>
                        </div>
                        <!-- buttun   -->
                        <div class="col-6 mt-3">
                            <input type="submit" class="btn btn-primary" value="edit">
                        </div>
                    </div>
                </form>
            </div>
        <?php } else {
                $theMsg = '<div class="alert alert-danger"> Not found user</div>';
                redirectHome($theMsg);
            }
        }
    //update page
    elseif ($do == 'update') {
        echo '<div class="container">';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $catid=$_POST['catid'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $ordering = $_POST['ordering'];
            $visible = $_POST['visible'];
            $comment = $_POST['comment'];
            $ads = $_POST['ads'];
            // check if there is no error
            if ($name !== '') {
                // update database  
                $stmt = $con->prepare("UPDATE categories SET name = ?, description = ?,ordering = ?, visibility=  ?, allow_comment = ?,allow_ads = ? WHERE id = ?");
                $stmt->execute(array($name, $description, $ordering, $visible, $comment, $ads,$catid));
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
        $catid = isset($_GET['catid']) && is_numeric($_GET['catid']) ? intval($_GET['catid']) : 0;
        $check = checkItem('catID', 'categories', $catid);
        if ($check > 0) {
            $stmt = $con->prepare('DELETE FROM categories WHERE id = :catid');
            $stmt->bindparam(":id", $catid);
            $stmt->execute();
            $theMsg = '<div class="alert alert-danger">' . $stmt->rowCount() . ' row Deleted </div>' . ' <h1 class="text-center">Delete successed </h1>';
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
