<?php
session_start();

require_once 'code/config.php';

if(!$db->is_loggedin())
{
 $db->redirect('login.php');
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>PDO</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
<?php
// Status message
if(isset($_SESSION['statusMsg'])){ ?>
	<div class="alert alert-success">
      <i class="glyphicon glyphicon-warning-sign"></i> &nbsp; <?php echo $_SESSION['statusMsg'] ?> !
    </div>
<?php 
    unset($_SESSION['statusMsg']); 
}

$userData = $db->getRows('users',array('where'=>array('id'=>$_GET['id']),'return_type'=>'single'));
if(!empty($userData)){
?>
<br>
<div class="row">
    <div class="panel panel-default user-add-edit">
        <div class="panel-heading">Edit User <a href="index.php" class="glyphicon glyphicon-arrow-left"></a></div>
        <div class="panel-body">
            <form method="post" action="code/userHandler.php">
				<div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" value="<?php echo $userData['username']; ?>">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" class="form-control" name="email" value="<?php echo $userData['email']; ?>">
                </div>
                <input type="hidden" name="id" value="<?php echo $userData['id']; ?>">
                <input type="hidden" name="action" value="edit">
                <input type="submit" class="form-control btn-default" name="submit" value="Update User">
            </form>
        </div>
    </div>
</div>

<?php } ?>
    	
</div>
</body>
</html>