<?php
	session_start();
	require_once '../config/settings.php';

	if(!isset($_SESSION['user_id'])){
	  echo 'User ID: вы не вошли';
	  exit();
	}

	$userId = $_SESSION['user_id'];
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		$applicationName = $_POST['specialityName'];

		$deleteStmt = $connect->prepare("DELETE FROM applications WHERE userID = ? AND speciality = ?");
  		$deleteStmt->bind_param("is", $userId, $applicationName);

  		if($deleteStmt->execute()){
  			$countStmt = $connect->prepare("SELECT specialityCount FROM user WHERE UserID = ?");
  			$countStmt->bind_param("i", $userId);
  			$countStmt->execute();
  			$countStmt->bind_result($currentCount);
  			$countStmt->fetch();
  			$countStmt->close();

  			if($currentCount > 0){
  				$updateStmt = $connect->prepare("UPDATE user SET specialityCount = specialityCount - 1 WHERE UserID = ?");
			    $updateStmt->bind_param("i", $userId);
			    $updateStmt->execute();
			    $updateStmt->close();
  			}

		    echo json_encode(['status' => 'success', 'message' => "Заявка успешно удалена!"]);
	  	} else {
	    	echo json_encode(['status' => 'error', 'message'=> "Ошибка при удалении заявки!"]);
	  	}

	  	$deleteStmt->close();
	}

	$connect->close();
?>