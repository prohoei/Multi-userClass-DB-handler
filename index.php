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
<?php unset($_SESSION['statusMsg']); }

		if(!empty($_SESSION['user_session'])){ ?>
		<br><a href="code/userHandler.php?action=logout">Logout</a>
		<?php
		$userData = $db->getRows('users',array('where'=>array('id'=>$_SESSION['user_session']['user']),'return_type'=>'single'));
		if(!empty($userData)){ 
		if($userData['rank'] == 1){?>
		<h3>Welcome SUPERADMIN <?php echo $userData['username']; ?>!</h3>
		<?php }else{ ?>
		<p>Welcome : <?php echo $userData['username']; ?></p>
		<?php }}}else { ?><a href='login.php'>Log ind?</a> <?php } ?>
		<h3>Users</h3>
        <table class="table">
            <tr>
                <th>#</th>
				<th>Username</th>
                <th>Email</th>
				<th>Rank</th>
				<th>Active</th>
                <th>Created</th>
				<th>Edited</th>
                <th></th>
            </tr>
            <?php
            $users = $db->getRows('users',array('order_by'=>'id DESC'));
            if(!empty($users)){ $count = 0; foreach($users as $user){ $count++;?>
            <tr>
                <td><?php echo $count; ?></td>
				<td><?php echo $user['username']; ?></td>
                <td><?php echo $user['email']; ?></td>
				<?php
				  $admin = $user['rank'];
				  if ($admin == '0'){
						$admin = str_replace("0", "Administrator", "$admin");
						echo "<td>".$admin."</td>";
					}elseif($admin == '1'){
						$admin = str_replace("1", "Superadmin", "$admin");
						echo "<td>".$admin."</td>";
				  }
				  $active = $user['active'];
				  $id = $user['id'];
				  if ($active == '1'){
						$active = str_replace("1", "Active", "$active");
						echo "<td>".$active." - <a href='code/userHandler.php?id=". $id ."&action=deactivate'>Deactivate</a></td>";
					}elseif($active == '0'){
						$active = str_replace("0", "Deactivated", "$active");
						echo "<td>".$active." - <a href='code/userHandler.php?id=". $id ."&action=activate'>Activate</a></td>";
				  }
				?>
                <td><?php echo $user['created']; ?></td>
				<td><?php echo $user['edited']; ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $user['id']; ?>" class="glyphicon glyphicon-edit"></a>
                    <a href="code/userHandler.php?action=delete&id=<?php echo $user['id']; ?>" class="glyphicon glyphicon-trash" onclick="return confirm('Are you sure?');"></a>
                </td>
            </tr>
            <?php } }else{ ?>
            <tr><td>No user(s) found...</td></tr>
            <?php } ?>
        </table>
		
		<h3>Search</h3>
		<form action="index.php" method="post">
			<input name="s" type="search" placeholder="Search users">
			<input type="Submit" value="Search">
		</form>
		
		<?php
		if(isset($_POST['s'])){
		$search = $db->getRows('users',array('like'=>array('username'=>$_POST['s'])));
		if(!empty($search)){
		  echo "Users: <br>";
		  foreach($search as $user){?>
			<li><?php echo $user['username']; ?></li>
		<?php }}else{
			echo 'No users found by search "'.$_POST['s'].'"';
		}} ?>
		<br><br>
		
		<h3>Pagination</h3>
		<table class="table">
		<?php
		$records_per_page = 2; // Records per page
		$tblName = 'users';	// Table name
		isset($_GET['page_no'])?$start=($_GET["page_no"]-1)*$records_per_page:$start = 0;
		$paging = $db->getRows($tblName,array('order_by'=>'id DESC','start'=>$start,'limit'=>$records_per_page));
        if(!empty($paging)){
			foreach($paging as $user){?>
            <tr>
				<td><?php echo $user['username']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['created']; ?></td>
				<td><?php echo $user['edited']; ?></td>
            </tr>
			<?php }
		}else{ ?>
            <tr><td>No user(s) found...</td>
        <?php } ?>
		</table>
		<?php	
		$records = 0; 
		foreach($db->getRows($tblName) as $user){ $records++;}
		$db->pagingLink($records,$records_per_page); // Pagination links
		?>
		<br><br>
	
</div>
</body>
</html>