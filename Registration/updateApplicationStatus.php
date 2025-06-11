<?php
session_start();
require_once '../config/settings.php';

if(!isset($_SESSION['user_id'])){
	echo json_encode(['status' => 'error', 'message' => 'Пользователь не авторизован']);
	exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
	$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
	$status = isset($_POST['status']) ? $_POST['status'] : '';

	if($id > 0 && !empty($status)){
		$stmt = $connect->prepare("UPDATE applications SET status = ? WHERE id = ?");
		$stmt->bind_param("si", $status, $id);
		$result = $stmt->execute();
		$stmt->close();

		if($result){
			echo json_encode(['status' => 'success', 'message' => 'Статус заявки обновлён']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'Ошибка при обновлении статуса заявки!']);
		}
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Некорректные данные']);
	}
}
?>