<?php
# USER HANDLER
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php'; // Include config

$tblName = 'users'; // Table name

// Response messages
$ms_insert = 'User has been created successfully. <a href="login.php">Login?</a>';
$ms_update = 'User data has been updated successfully.';
$ms_delete = 'User data has been deleted successfully.';
$ms_callback = 'Some problem occurred, please try again.';
$ms_login = 'The user is now logged in.';
$ms_exist = 'The username already exist.';
$login_callback = 'Wrong details.';
$ms_logout = 'The user is now logged out.';
$ms_active = 'The user was succesfully changed to ';

if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])){
	/* 
	 * Insert table into database 
	 */
    if($_REQUEST['action'] == 'add'){
		
		// Receive variables
		$username = trim($_POST['username']);
		$email = trim($_POST['email']);
		$password = trim($_POST['password']); 
		$password2 = trim($_POST['password2']);
		
		// Validation
		$valid::sameInput($password, $password2, $username);
		$valid::lenghtInput($password, $username);
		$valid::hasnumberandletter($password, $username);
		$valid::validMail($email, $username);
		$fejl = $valid->getFejl();
		
		// If inputs er valid
		if(count($fejl)<1){
		$crypt_pass = password_hash($password, PASSWORD_DEFAULT); // Hash password		
		$userData = array(
		  'username' => $username,
		  'password' => $crypt_pass,
		  'email' => $email
		);
		
		// Check if username exist
		$checkData = array(
		  'username' => $username
		);
		$check = $db->checkExist($tblName,$checkData);
		if($check == 0){
        $insert = $db->insert($tblName,$userData);
		$_SESSION['statusMsg'] = $insert?$ms_insert:$ms_callback;
		}else{
		$_SESSION['statusMsg'] = $ms_exist;		
		}
		}else{
		$_SESSION['statusMsg'] = $fejl;
		}
		$db->redirect('../signup.php');
		exit;
    }
	/*
	 * Update table from database 
	 */
	elseif($_REQUEST['action'] == 'edit'){
        if(!empty($_POST['id'])){
            $userData = array(
			  'username' => $_POST['username'],
			  'email' => $_POST['email']
			);
			$condition = array('id' => $_POST['id']);
            $update = $db->update($tblName,$userData,$condition);
            $statusMsg = $update?$ms_update:$ms_callback;
        }
    }
	/* 
	 * Insert table into database 
	 */
	elseif($_REQUEST['action'] == 'delete'){
        if(!empty($_GET['id'])){
            $condition = array('id' => $_GET['id']);
            $delete = $db->delete($tblName,$condition);
            $statusMsg = $delete?$ms_delete:$ms_callback;
        }
    }
	/* 
	 * Login to and create user_session 
	 */
	elseif($_REQUEST['action'] == 'login'){
		$pass = $_POST['password'];
		$condition = array(
			'username' => $_POST['username'],
			'email' => $_POST['username']
		);
		$login = $db->login($tblName,$pass,$condition);
		$statusMsg = $login?$ms_login:$login_callback;
		if(!$db->is_loggedin()){
			$_SESSION['statusMsg'] = $login_callback;
			$db->redirect('../login.php'); // Redirect
			exit;
		}
    }
	/* 
	 * Logout and unset user_session 
	 */
	elseif($_REQUEST['action'] == 'logout'){
        if(!empty($_SESSION['user_session'])){
            $logout = $db->logout();
            $statusMsg = $logout?$ms_logout:$ms_callback;
        }
    }
	/* 
	 * Activate and deactivates user in database 
	 */
	if ($_REQUEST['action'] == 'activate' OR $_REQUEST['action'] == 'deactivate') {
		if(!empty($_GET['id'])){
			if($_REQUEST['action'] == 'activate'){
				$active = 1;
			}elseif($_REQUEST['action'] == 'deactivate'){
				$active = 0;
			}
            $userData = array(
			  'active' => $active,
			);
			$condition = array('id' => $_GET['id']);
            $update = $db->update($tblName,$userData,$condition);
            $statusMsg = $update?$ms_active.$_REQUEST['action']:$ms_callback;
        }
	}

}else{
	$statusMsg = $ms_callback; // Callback error message
}
// Get operation status message
$_SESSION['statusMsg'] = $statusMsg;
// Redirect
$db->redirect('../index.php');
