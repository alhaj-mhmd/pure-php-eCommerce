<?php
/*
    == Memebers Manage page
    == add - insert | edit - update | 
*/
session_start();
$pageTitle = "Members";

if (isset($_SESSION['username'])) {
    include 'init.php';
    $do = isset($_GET['do']) ? $_GET['do'] : 'manage';
    //manage page
    if ($do == 'manage') {
        $query = '';
        if (isset($_GET['page']) && $_GET['page'] == 'pending') {
            $query = 'AND register = 0';
        }
        // select data from the DB
        $stmt = $con->prepare("SELECT * FROM users WHERE groupID != 1 $query");
        $stmt->execute();
        //asign data to the variabls
        $rows = $stmt->fetchAll();
        ?>
        <div class="container text-center">
            <h1>Manage Members </h1>
            <table class="table table-bordered">
                <th>#ID</th>
                <th>UserName</th>
                <th>Email</th>
                <th>FullName</th>
                <th>Date</th>
                <th>Control</th>
                <?php
                        foreach ($rows as $row) {
                            echo '<tr>';
                            echo '<td>' . $row['userID'] . '</td>';
                            echo '<td>' . $row['username'] . '</td>';
                            echo '<td>' . $row['email'] . '</td>';
                            echo '<td>' . $row['fullname'] . '</td>';
                            echo '<td>' . $row['date'] . '</td>';
                            echo '<td>
                                    <a href="members.php?do=edit&userid=' . $row['userID'] . '" class="btn btn-success"><i class="fa fa-edit mr-1"></i>Edit</a>
                                    <a href="members.php?do=delete&userid=' . $row['userID'] . '"class="btn btn-danger  confirm"><i class="fa fa-trash mr-1"></i>Delete</a>';
                            if ($row['regstatus'] == 0) {
                                echo '<a href="members.php?do=activate&userid=' . $row['userID'] . '" class="btn btn-secondary ml-1"><i class="fa fa-check"></i>Active</a>';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
            </table>
            <a href="members.php?do=add" class=" mt-3 btn btn-primary btn-lg"><i class="fa fa-plus"></i> New Member</a>
        </div>
    <?php }
    //add page
    elseif ($do == 'add') { ?>
        <!-- Add page  -->
        <div class="container">
            <h1 class="text-center">Add Member</h1>
            <form class="form-horizontal" action="?do=insert" method="POST">
                <div class="form-group">
                    <!-- user username  -->
                    <label class=" mt-3 control-lable">User Name</label>
                    <div class="col-6">
                        <input type="text" name="username" class="form-control" autocomplete="off" required="required">
                    </div>
                    <!-- user password  -->
                    <label class="mt-3 control-lable">Password</label>
                    <div class="col-6">
                        <input type="password" name="password" class="password form-control" autocomplete="new-password" required="required">
                        <i class="show-pass fa fa-eye"></i>
                    </div>
                    <!-- user email  -->
                    <label class="mt-3 control-lable">email</label>
                    <div class="col-6">
                        <input type="email" name="email" class="form-control" required="required">
                    </div>
                    <!-- user name  -->
                    <label class="mt-3 control-lable">Name</label>
                    <div class="col-6">
                        <input type="text" name="fullname" class="form-control" required="required">
                    </div>
                    <!-- buttun   -->
                    <div class="col-6 mt-3">
                        <input type="submit" class="btn btn-primary" value="Add Member">
                    </div>
                </div>
            </form>
        </div>
    <?php }
    //insert page
    elseif ($do == 'insert') {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo '<div class="container">';
            $username = $_POST['username'];
            $password = $_POST['password'];
            $hashpassword = sha1($_POST['password']);
            $email = $_POST['email'];
            $fullname = $_POST['fullname'];
            //form validate
            $formerrors = array();
            if (strlen($username) < 0) {
                $formerrors[] = "username can npt be less than 3";
            }
            if (empty($username)) {
                $formerrors[] = "username cannot be empty ";
            }
            if (empty($password)) {
                $formerrors[] = "password cannot be empty ";
            }
            if (empty($email)) {
                $formerrors[] = " email cannot be empty ";
            }
            if (empty($fullname)) {
                $formerrors[] = "   name cannot be empty  ";
            }
            foreach ($formerrors as $error) {
                echo "<div class='alert alert-danger'>" . $error . "</div>";
            }

            // check if there is no error
            if (empty($formerrors)) {
                if (checkItem('username', 'users', $username) == 1) {
                    $theMsg = '<div class="alert alert-danger">The user is exsit</div>';
                    redirectHome($theMsg, 'previous');
                    // insert into database    
                } else {
                    $stmt = $con->prepare("INSERT INTO  users ( username, password, email, fullname,regstatus,date)
                    VALUES (:username, :password, :email,:fullname,0,now())");
                    $stmt->execute(array(
                        'username' => $username,
                        'password' => $hashpassword,
                        'email' => $email,
                        'fullname' => $fullname
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
        } else {
            $theMsg = "<div class='alert alert-danger'>You cant brouwse this directly </div>";
            redirectHome($theMsg);
        }
    } 
            //Edit page
    elseif ($do == 'edit') {
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
        $stmt = $con->prepare("SELECT *  FROM users WHERE userID=? ");
        $stmt->execute(array($userid));
        $row = $stmt->fetch();
        if ($stmt->rowCount() > 0) { ?>
        <div class="container">
            <h1 class="text-center">Edit Member </h1>
            <form class="form-horizontal" action="?do=update" method="POST">
                <div class="form-group">
                    <input type="hidden" name="userid" value="<?php echo $userid; ?>">
                    <!-- user username  -->
                    <label class=" mt-3 control-lable">User Name</label>
                    <div class="col-6">
                        <input type="text" value="<?php echo $row['username'] ?>" name="username" class="form-control" autocomplete="off" required="required">
                    </div>
                    <!-- user password  -->
                    <label class="mt-3 control-lable">Password</label>
                    <div class="col-6">
                        <input type="hidden" name="oldpassword" value="<?php echo $row['password']; ?>">
                        <input type="password" name="newpassword" class="form-control" autocomplete="new-password">
                    </div>
                    <!-- user email  -->
                    <label class="mt-3 control-lable">email</label>
                    <div class="col-6">
                        <input type="email" value="<?php echo $row['email'] ?>" name="email" class="form-control" required="required">
                    </div>
                    <!-- user name  -->
                    <label class="mt-3 control-lable">Name</label>
                    <div class="col-6">
                        <input type="text" value="<?php echo $row['fullname'] ?>" name="fullname" class="form-control" required="required">
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
            $userid = $_POST['userid'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $fullname = $_POST['fullname'];
            //password trick 
            $pass = empty($_POST['newpassword']) ? $_POST['oldpassword'] : sha1($_POST['newpassword']);
            //form validate
            $formerrors = array();
            if (strlen($username) < 3) {
                $formerrors[] = "username can npt be less than 3";
            }
            if (empty($username)) {
                $formerrors[] = "username cannot be empty ";
            }
            if (empty($email)) {
                $formerrors[] = " email cannot be empty ";
            }
            if (empty($fullname)) {
                $formerrors[] = "   name cannot be empty  ";
            }
            foreach ($formerrors as $error) {
                echo "<div class='alert alert-danger'>" . $error . "</div>";
            }
            // check if there is no error
            if (empty($formerrors)) {
                $stmt2 = $con->prepare("SELECT * FROM users
                                        WHERE username = ? 
                                        AND userID != ?");
                $stmt2->execute(array($username, $userid));
                $count = $stmt2->rowCount();
                if ($count == 1) {
                  echo "Sorry the username is existed";
                }else {
                      // update database     
                $stmt = $con->prepare("UPDATE users SET username=?, email=?, fullname=?, password=? WHERE userID=?");
                $stmt->execute(array($username, $email, $fullname, $pass, $userid));
                $theMsg = '<div class="alert alert-danger">' . $stmt->rowCount() . ' row updated </div>' . ' <h1 class="text-center">Update successed </h1>';
                redirectHome($theMsg, 'previous');
                }
            }
        } else {
            $theMsg = '<div class="alert alert-danger">Not updated</div>';
            redirectHome($theMsg, 'previous');
        }
        echo '</div>';
    }
    //delete page
    elseif ($do == 'delete') {
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
        $check = checkItem('userID', 'users', $userid);
        if ($check > 0) {
            $stmt = $con->prepare('DELETE FROM users WHERE userID=:userid');
            $stmt->bindparam(":userid", $userid);
            $stmt->execute();
            $theMsg = '<div class="alert alert-danger">' . $stmt->rowCount() . ' row Deleted </div>' . ' <h1 class="text-center">Delete successed </h1>';
            redirectHome($theMsg);
        } else {
            $theMsg = '<div class="alert alert-danger">Not exist</div>';
            redirectHome($theMsg);
        }
    }
    //activate page
    elseif ($do == 'activate') {
        $userid = isset($_GET['userid']) && is_numeric($_GET['userid']) ? intval($_GET['userid']) : 0;
        $check = checkItem('userID', 'users', $userid);
        if ($check > 0) {
            $stmt = $con->prepare('UPDATE users SET regstatus = 1 WHERE userID = :userid');
            $stmt->bindparam(":userid", $userid);
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
