<?php
session_start();
require_once '../config/settings.php';

if(!isset($_SESSION['user_id'])) {
	exit('Ошибка: вы не вошли в систему.');
}

$user_id = $_SESSION['user_id'];
$user_token = $_COOKIE['user_token'] ?? '';

$stmt=$connect->prepare("SELECT FullName FROM user WHERE token = ?");
$stmt->bind_param("s", $user_token);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0) {
	$user = $result->fetch_assoc();
	$fullName = $user['FullName'];
} else {
	header('Location: ../Registration/Vendor/SignIn.php');
	exit();
}

if($_SERVER['REQUEST_METHOD'] == "POST") {
	header('Content-Type: application/json');

	$student = isset($_POST['student']) ? $_POST['student'] : '';
	$email = isset($_POST['email']) ? $_POST['email'] : '';
	if(isset($_POST['specialityHidden'])){
		$speciality = $_POST['specialityHidden'];
	}else{
	}
	$phone = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : '';
	$place = isset($_POST['place']) ? $_POST['place'] : '';
	$currentDateTime = date('Y-m-d H:i:s');
	$status = "Рассматривается";

	if(empty($speciality)) {
		exit('Ошибка: специальность не выбрана!');
	}

	$stmt = $connect->prepare("SELECT specialityCount FROM user WHERE UserID = ?");
	$stmt->bind_param("s",$user_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$specialityCount = $row['specialityCount'];

	$response = [];

	if($specialityCount >= 3) {
		$response['success'] = false;
		$response['message'] = "Ошибка: максимум 3 заявки!";
		echo json_encode($response);
		exit();
	}

	$stmt = $connect->prepare("SELECT * FROM applications WHERE userID = ? AND speciality = ?");
	$stmt->bind_param("ss", $user_id, $speciality);
	$stmt->execute();
	$result = $stmt->get_result();

	if($result->num_rows > 0){
		$response['success'] = false;
		$response['message'] = "Заявка с такой специальностью уже существует.";
		echo json_encode($response);
		exit();
	}

	$stmt = $connect->prepare("SELECT department FROM specialities WHERE Name = ?");
	$stmt->bind_param("s", $speciality);
	$stmt->execute();
	$result = $stmt->get_result();
	$departmentRow = $result->fetch_assoc();
	$department = $departmentRow['department'] ?? '';

	$stmt = $connect->prepare("INSERT INTO applications (student, email, speciality, userID, userFullName, phoneNumber, place, date, status, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("sssissssss", $student, $email, $speciality, $user_id, $fullName, $phone, $place, $currentDateTime, $status, $department);

	if($stmt->execute()){
		$stmt = $connect->prepare('UPDATE user SET specialityCount = specialityCount + 1 WHERE token = ?');
		$stmt->bind_param("s", $user_token);
		$stmt->execute();

		$stmt = $connect->prepare('UPDATE specialities SET applicationCount = applicationCount + 1 WHERE Name = ?');
		$stmt->bind_param("s", $speciality);
		$stmt->execute();

		$response['success'] = true;
		$response['message'] = "Заявка добавлена успешно";
	} else {
		$response['success'] = false;
		$response['message'] = "Ошибка при отправке заявки";
	}
	$stmt->close();
	echo json_encode($response);
	exit();
}

$connect->close();
?>