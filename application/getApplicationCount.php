<?php
	session_start();
	require_once '../config/settings.php';

	if(isset($_SESSION['user_id'])) {
		$user_id = $_POST['user_id'];

		$stmt = $connect->prepare("SELECT COUNT(*) AS specialityCount FROM user WHERE userID = ?");
		$stmt->bind_param("s",$user_id);
		$stmt->execute();
		$result = $stmt->get_result();

		$row=$result->fetch_assoc();
		$count = $row['specialityCount'];

		try {
			echo json_encode(['specialityCount' => $count]);
		} catch(Exception $e) {
			http_response_code(500);
			echo json_encode(['specialityCount' => 0]);
		}
	} else {
		echo json_encode(['error' => 'Пользователь не авторизован']);
	}
?>