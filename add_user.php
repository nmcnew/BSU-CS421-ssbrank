<?php
include_once('PDOFactory.php');
session_start();
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];
$check = $_POST['password-check'];
$location = $_POST['location'];
$n64 = checkCheckbox('n64');
$ssbm = checkCheckbox('melee');
$ssbb = checkCheckbox('ssbb');
$ssbpm = checkCheckbox('pm');
$roa = checkCheckbox('roa');
$ssb4 = checkCheckbox('sm4sh');
$user_id = uniqid('', true);
$role = 1;

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
     header('Location: register.php?error=Please Enter a valid email' . $email);
}
if($email === "" or $username === "" or $password === "" or $check === ""){
	header('Location: register.php?error=Please enter all data please');
}
if($password !== $check){
	header('Location: register.php?error=Passwords don\'t match');
}
$db = PDOFactory::getConnection();

$stmt = $db->prepare('INSERT INTO user (id, name,  location, password, email, role, melee, n64, sm4sh, brawl, roa, pm)
	VALUES (:id, :username, :location, :password, :email, :role, :melee, :n64, :sm4sh, :brawl, :roa, :pm)');

$stmt->bindParam(':id', $user_id, PDO::PARAM_STR, 23);
$stmt->bindParam(':username', $username, PDO::PARAM_STR, 32);
$stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR, 256);
$stmt->bindParam(':location', $location, PDO::PARAM_STR, 100);
$stmt->bindParam(':email', $email, PDO::PARAM_STR, 254);
$stmt->bindParam(':role', $role, PDO::PARAM_INT, 11);
$stmt->bindParam(':melee', $ssbm, PDO::PARAM_BOOL);
$stmt->bindParam(':n64', $n64, PDO::PARAM_BOOL);
$stmt->bindParam(':sm4sh', $ssb4, PDO::PARAM_BOOL);
$stmt->bindParam(':brawl', $ssbb, PDO::PARAM_BOOL);
$stmt->bindParam(':roa', $roa, PDO::PARAM_BOOL);
$stmt->bindParam(':pm', $ssbpm, PDO::PARAM_BOOL);
$stmt->execute();

mkdir('/users/'.$user_id.'/', 0777, true);
$_SESSION['user_id'] = $user_id;
header('Location: index.php');
function checkCheckbox($val){
	if(isset($_POST[$val])){
		return $_POST[$val] === "TRUE";
	}
	return false;
}