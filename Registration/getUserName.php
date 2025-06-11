<?php
	session_start();
	require_once '../config/settings.php';

	if(!isset($_SESSION['user_id'])){
		echo json_encode(['error' => "Пользователь не авторизован"]);
		exit();
	}

	$userId = $_SESSION['user_id'];

	$stmt = $connect->prepare("SELECT FullName FROM user WHERE UserID = ?");
	$stmt->bind_param("i", $userId);
	$stmt->execute();
	$stmt->bind_result($fullName);
	$stmt->fetch();
	$stmt->close();
	var_dump($fullName)
	if(empty($fullName)){
		$fullName = "Имя пользователя не установлено";
	}

	echo json_encode(["FullName" => $fullName]);
?>