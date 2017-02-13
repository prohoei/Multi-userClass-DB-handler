<?php 
session_start();
if(isset($_SESSION['user_session'])){
	header ('location: index.php');
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Login</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">

     <div class="login-container" style="width: 300px; margin: auto;">
        <form action="code/userHandler.php?action=login" method="post">
            <h2>Sign in</h2><hr>
			<?php
			// Status message
			if(isset($_SESSION['statusMsg'])){ ?>
				<div class="alert alert-warning">
                      <i class="glyphicon glyphicon-log-in"></i> &nbsp; 
					  <?php
					  $array = $_SESSION['statusMsg'];
					  if (is_array($_SESSION['statusMsg'])){
					  foreach ($array as $key => $value){
						echo $value . "<br />";
					  }
					  }else{
						echo $_SESSION['statusMsg'];
					  }
					  ?>
                 </div>
			<?php unset($_SESSION['statusMsg']); } ?>
			
            <div class="form-group">
             <input type="text" class="form-control" name="username" placeholder="Username or E-mail" value="<?php echo isset($_POST["username"]) ? $_POST["username"] : ''; ?>" required>
            </div>
            <div class="form-group">
             <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="clearfix"></div><hr>
			<button type="submit" name="btn-login" class="btn btn-block btn-primary">
                 <i class="glyphicon glyphicon-log-in"></i>&nbsp;SIGN IN
            </button>
            <br>
            <label>Don't have account yet ! <a href="signup.php">Sign Up</a></label>
        </form>
       </div>

</div>
</body>
</html>