<?php
session_start();
require_once '../config/settings.php';

if(isset($_SESSION['FullName']) && !isset($_SESSION['user_id'])) {
	echo json_encode(['status' => 'error', 'message' => 'Пользователь не аутентифицирован.']);
	exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$fullName = $_SESSION['FullName'];
	$userId = $_SESSION['user_id']; 

	if(!isset($_POST['phoneNumber']) || empty(trim($_POST['phoneNumber'])) || !isset($_POST['speciality']) || empty(trim($_POST['speciality']))){
		echo json_encode(['status' => 'error', 'message']);
		exit();
	}

	$checkStmt = $connect->prepare("SELECT * FROM consultations WHERE idAbiturient = ? AND status = 'Консультация назначена'");
	$checkStmt->bind_param("i", $userId);
	$checkStmt->execute();
	$checkResult = $checkStmt->get_result();

	if($checkResult->num_rows > 0){
		echo 'Вы уже отправляли заявку!';
		$checkStmt->close();
		exit();
	}

	$applicationDate = date('Y-m-d H:i:s');
	$workerId = 0;
	$workerFullName = "";
	$workerPhoneNumber = "";
	$cabinet = "";
	$phoneNumber = trim($_POST['phoneNumber']);
	$status  = "Ожидание назначения работника";
	$consultationDate = "";
	$speciality = trim($_POST['speciality']);

	$stmt = $connect->prepare("SELECT department FROM specialities WHERE Name = ?");
	$stmt->bind_param("s", $speciality);
	$stmt->execute();
	$result = $stmt->get_result();
	$departmentRow = $result->fetch_assoc();
	$department = $departmentRow['department'] ?? '';
	
	$stmt = $connect->prepare("INSERT INTO consultations (speciality, abiturientFullName, workerFullName, abiturientPhoneNumber, workerPhoneNumber, cabinet, idWorker, idAbiturient, status, applicationDate, consultationDate, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");	
	$stmt->bind_param("ssssssiissss", $speciality, $fullName, $workerFullName, $phoneNumber, $workerPhoneNumber, $cabinet, $workerId, $userId, $status, $applicationDate, $consultationDate, $department);

	if($stmt->execute()){
		header('Content-Type: application/json; charset=utf-8');
		// echo json_encode(['status' => 'success', 'message' => utf8_encode('Заявка на консультацию успешно отправлена!')]);
		echo 'Заявка успешно отправлена';
	} else {
		$error = $connect->error;
		echo json_encode(['status' => 'error', 'message' => 'Ошибка при отправке заявки: '. $error]);
	}

	$stmt->close();
	// echo json_encode($response);
	exit();
}
?>