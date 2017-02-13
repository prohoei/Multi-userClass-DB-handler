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
<title>Sign up</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
<div class="container">
     <div class="form-container" style="width: 300px; margin: auto;">
        <form action="code/userHandler.php?action=add" method="post">
            <h2>Sign up</h2><hr>
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
				<input type="text" class="form-control" name="username" placeholder="Username" value="<?php echo isset($_POST["username"]) ? $_POST["username"] : ''; ?>">
            </div>
            <div class="form-group">
				<input type="text" class="form-control" name="email" placeholder="E-mail" value="<?php echo isset($_POST["email"]) ? $_POST["email"] : ''; ?>">
            </div>
            <div class="form-group">
				<input type="password" class="form-control" name="password" placeholder="Password">
            </div>
			<div class="form-group">
				<input type="password" class="form-control" name="password2" placeholder="Repeat Password">
            </div>
            <div class="clearfix"></div><hr>
			<button type="submit" class="btn btn-block btn-primary" name="btn-signup">
                <i class="glyphicon glyphicon-open-file"></i>&nbsp;SIGN UP
            </button><br>
            <label>Have an account? <a href="login.php">Sign In</a></label>
        </form>
       </div>
	
</div>
</body>
</html>