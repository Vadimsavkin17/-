<?php
$serverName = "localhost";
$userName = "root";
$password = "root";
$dbName = "webdevelopment";

$conn = new mysqli($serverName,$userName,$password,$dbName);

if($conn->connect_error) {
die("Ошибка подключения:" . $conn->connect_error);
}

$institute=$_POST['institute'] ?? '';

//Извлекаем специальности
$stmt = $conn->prepare("SELECT id, Name FROM specialities");
$stmt->execute();
$result = $stmt->get_result();

$specialities = [];
if($result->num_rows > 0 ) {
while($row = $result->fetch_assoc()){
$specialities[] = $row;
}
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($specialities);
?>