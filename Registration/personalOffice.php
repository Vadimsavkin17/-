<?php
session_start();

require_once '../config/settings.php';

if(!isset($_SESSION['user_id'])){
  echo 'User ID: вы не вошли';
  exit();
}
  $userId = $_SESSION['user_id'];

  $stmt = $connect->prepare("SELECT avatar, FullName,user_type, email, Login, departmentWorker FROM user WHERE UserID = ?");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $stmt->bind_result($avatarUrl, $fullName, $userType, $email,$userName, $departmentWorker);
  $stmt->fetch();
  $stmt->close();

  $applicationsStmt = $connect->prepare("SELECT speciality, date, status FROM applications WHERE userID = ?");
  $applicationsStmt->bind_param("i", $userId);
  $applicationsStmt->execute();
  $applicationsStmt = $applicationsStmt->get_result();

  $applications = [];

  $consultationStmt = $connect->prepare("SELECT * FROM consultations WHERE idAbiturient = ?");
  $consultationStmt->bind_param("i", $userId);
  $consultationStmt->execute();
  $consultationResult = $consultationStmt->get_result();

  $statusMessage = "";
  $consultationDetails = "";

  if($consultationResult->num_rows > 0){
    $consultationData = $consultationResult->fetch_assoc();

    if($consultationData['workerFullName'] != '' && $consultationData['idWorker'] != 0)
    {
      $workerFullName = htmlspecialchars($consultationData['workerFullName']);
      $statusMessage = "Вашу заявку рассмотрел(а): " . $workerFullName;
      $consultationDate = htmlspecialchars($consultationData['consultationDate']);
      $meetingLocation = htmlspecialchars($consultationData['cabinet']);
      $workerPhone = htmlspecialchars($consultationData['workerPhoneNumber']);
      $consultationDetails = "Дата назначения консультации: " . $consultationDate . "<br>" . "Место проведения: " . $meetingLocation . "<br>" . "Номер работника: " . $workerPhone;
    } else {
      $statusMessage = "Ваша заявка на проведение консультации обрабатывается";
    }
  } else {
    $statusMessage = "";
  }

  $consultationStmt->close();

  while($row = $applicationsStmt->fetch_assoc()){
    $applications[] = [
      'speciality' => $row['speciality'],
      'date' => $row['date'],
      'status' => $row['status']
    ];
  }

  $applicationsStmt->close();

  if(empty($avatarUrl)){
    $avatarUrl = "standart.jpg";
  }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Personal Office</title>

<!--=============== Шрифт ===============-->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<!--=============== Шрифт ===============-->

<!-- CSS СТИЛИ -->
<link rel = "stylesheet" href = "../css/personalOffice.css">
<!-- CSS СТИЛИ -->

<!--=============== REMIXICONS ===============-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" crossorigin="">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
</head>
<body>
  <div class = "container">
  <div class = "profile">
  <form id = "avatarForm"action = "upload-avatar.php" method = "POST" enctype="multipart/form-data">
    <div class = "imgContainer">
      <button class = "back-btn"> <i class="ri-arrow-left-line"></i> </button>
      <script>
        document.querySelector('.back-btn').addEventListener('click', function(){
          window.location.href = '../main.php';
        })
      </script>
      <img src = "<?php echo htmlspecialchars($avatarUrl) ?>" alt = "Profile Picture" class = "profile-picture" id = "profilePicture">
      <div class = "button-container">
        <label for = "avatarInput" class = "circleBtn uploadBtn">+</label>
        <input type="file" name="avatar" accept = "image/*" id = "avatarInput" class = "hidden" required>
        <button type = "submit" id = "uploadBtn" class = "circleBtn hidden submitBtn" onclick = "uploadFile()"><i class = "ri-upload-2-fill"></i></button>
      </div>
    </div>
    <div class = "userIngo">
      <h2 id = 'userFullName' class = "userFullName"> <?php  echo htmlspecialchars($fullName)?></h2>
    </div>
  </form>
  <script>
    document.getElementById('avatarForm').addEventListener('submit', function(event){
      event.preventDefault();

      var formData = new FormData(this);
      var xhr = new XMLHttpRequest();

      xhr.open('POST', this.action, true);
      xhr.onload = function() {
        if(xhr.status == 200){
          document.getElementById('profilePicture').src = URL.createObjectURL(document.getElementById('avatarInput').files[0]);
          document.getElementById('uploadBtn').classList.add('hidden');
          console.log("Файл успешно загружен!");
        } else {
          console.error('Ошибка при загрузке аватара!');
        }
      };

      xhr.send(formData);
    });

    document.getElementById('avatarInput').addEventListener('change', function(){
      const uploadButton = document.getElementById('uploadBtn');

      if(this.files.length > 0) {
        uploadButton.classList.remove('hidden');
      }else {
        uploadButton.classList.add('hidden');
      }
    });
  </script>
  </div>
  <div class = "info" id = "infoContainer">
    <button id = "edit"><i class="ri-edit-fill"></i></button>
    <div class = "userData">
      <p class = "userType"><?php echo htmlspecialchars($userType)?> </p>
      <?php if($userType === 'Работник'): ?>
        <p class = "userEmail"><?php echo 'Кафедра: ' . htmlspecialchars($departmentWorker)?> </p>
      <?php endif; ?>
      <p class = "userEmail"> Электронная почта: <?php echo htmlspecialchars($email)?></p>
    </div>
    <div id = "editForm" class = "hidden">
      <h3>Редактировать информацию</h3>
      <form id = "updateForm" action="updateUserInfo.php" method = "POST">
        <div class = "inputContainer">
          <label> ФИО </label>
          <div class = "inputMiniContainer">
            <input class = "changeInput" type = "text" name ="fullName" placeholder="Новое ФИО" value = "<?php echo htmlspecialchars($fullName)?>">
          </div>
          <label> Логин </label>
          <div class = "inputMiniContainer">
            <input class = "changeInput" type="text" name="userName" placeholder="Новый логин" value = "<?php echo htmlspecialchars($userName)?>">
          </div>
          <label> Почта</label>
          <div class = "inputMiniContainer">
            <input class = "changeInput" type="email" name="email" placeholder="Новая почта" value = "<?php echo htmlspecialchars($email)?>">
          </div>
          <label> Пароль </label>
          <div class = "inputMiniContainer">
            <input class = "changeInput" type="password" name="password" placeholder="Новый пароль">
          </div>
        </div>
        <button type = "submit" id = "saveChanges">Сохранить</button>
      </form>
    </div>
    <div class = "applicationSpeciality" style = "<?php echo $userType === 'Абитуриент' ? '' : 'max-height: 400px;'; ?>">
      <?php if($userType === 'Абитуриент'): ?>
        <form method="POST" id = "appDelete">
          <h3 class = "applicationTitle"> Вы отправили заявки на следующие специальности</h3>
          <ul class = "applications">
            <?php
              if(empty($applications)){
                echo '<li> У вас нет отправленных заявок. <a href = "../application/applicationPart.php" class = "appPart"> Отправить заявку </a></li>';
              }else {
                foreach ($applications as $application) {
                  echo '<li>' .
                  "• ". htmlspecialchars($application['speciality']) .
                  ' (Дата: ' . htmlspecialchars($application['date']) .
                  ', Статус: ' .htmlspecialchars($application['status']) . 
                  ') <button class = "deleteApp" data-speciality="' . htmlspecialchars($application['speciality']) .'">
                  <i class="ri-delete-bin-line"></i></button><li>';
                }
              }
            ?>
          </ul>
        </form>
        <?php if (!empty($statusMessage)): ?>
          <div class = "consultationStatus">
            <p><?php echo $statusMessage; ?></p>
          </div>
          <?php if(!empty($consultationDetails)): ?>
            <div class = "consultationDetails">
              <p><?php echo $consultationDetails; ?></p>
            </div>
          <?php endif; ?>
        <?php endif; ?>
        <div class = "btnContainer">
          <button id = "openFormBtn"> Записаться на консультацию </button>
        </div>
        <div class = "overlay" id = "formOverlay">
          <div class = "form-container">
            <h2>Запись на консультацию</h2>
            <form id = "consultationForm">
              <label for ="phoneNumber"> Введите номер телефона</label>
              <input type="text" name="phoneNumber" id = "phoneNumber" required>
              <label for ="speciality">Выберите специальность</label>
              <div class = "specialitySelect" data-type = "speciality">
                <span>
                <select id = "speciality"name = "speciality" required>
                    <option value = "">Выберите</option>
                <!-- Загрузка специальностей через JS -->
                </select>
                </span>
              </div>
              <button type="submit" class = "requestConsultation">Отправить</button>
            </form>
            <span class = "close-btn" id = "closeFormBtn"><i class="ri-close-circle-line"></i></span>
            <div id = "responseMessage"></div>
          </div>
          <script type="text/javascript">
            $(document).ready(function() {
                $('#speciality').select2({
                    placeholder: "Выберите специальность", // Подсказка
                    allowClear: true // Возможность сбросить выбор
                });
            });

            document.addEventListener('DOMContentLoaded', function(){
              fetch('fetchAllSpecialities.php')
                .then(response => response.json())
                .then(data => {
                  const specialitySelect = document.getElementById('speciality');
                  data.forEach(speciality => {
                    const option = document.createElement('option');
                    option.value = speciality.Name;
                    option.textContent = speciality.Name;
                    specialitySelect.appendChild(option);
                  });
                })
                .catch(error => console.error('Ошибка при загрузке специальностей: ', error));
            });

            document.getElementById('openFormBtn').onclick = function(){
              document.getElementById('formOverlay').style.display = 'flex';
            };

            document.getElementById('closeFormBtn').onclick = function(){
              document.getElementById('formOverlay').style.display = 'none';
            };

            document.getElementById('consultationForm').onsubmit = function(event){
              event.preventDefault();

              // var phoneNumber = document.getElementById('phoneNumber').value;
              const formData = new FormData(this);

              var xhttp = new XMLHttpRequest();
              xhttp.onreadystatechange = function(){
                if(this.readyState == 4 && this.status == 200){
                  alert(this.responseText);
                  document.getElementById('formOverlay').style.display = 'none';
                  document.getElementById('consultationForm').reset();
                }
              };
              xhttp.open("POST", "consultation.php", true);
              xhttp.send(formData);
            };
          </script>
        </div>
      <?php elseif ($userType === 'Работник') : ?>
        <div class = "tableTitle">
          <h3> Список заявок</h3>
        </div>
        <div class = "applicatiionList">
          <table class = "table table-dark table-hover">
            <thead>
              <tr class = "fixedRow">
                <th>Специальность</th>
                <th>ФИО</th>
                <th>Номер телефона</th>
                <th>Адрес</th>
                <th>Дата</th>
                <th>Статус</th>
                <th>Действия</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $allApplicationsStmt = $connect->prepare("SELECT * FROM applications WHERE department = ?");
              $allApplicationsStmt->bind_param("s", $departmentWorker);
              $allApplicationsStmt->execute();
              $result = $allApplicationsStmt->get_result();

              if($result->num_rows === 0){
                echo '<li> Нет доступных заявок. </li>';
              }else{
                while($row = $result->fetch_assoc()){
                  echo '<tr>';
                  echo '<td>' .htmlspecialchars($row['speciality']) . '</td>';
                  echo '<td>' .htmlspecialchars($row['userFullName']) . '</td>';
                  echo '<td>' .htmlspecialchars($row['phoneNumber']) . '</td>';
                  echo '<td>' .htmlspecialchars($row['place']) . '</td>';
                  echo '<td>' .htmlspecialchars($row['date']) . '</td>';
                  echo '<td>' .htmlspecialchars($row['status']) . '</td>';
                  echo '<td>
                        <button class = "approveApp" data-id = "'.htmlspecialchars($row['id']).'"><i class="ri-check-fill"></i></button>
                        <button class = "denyApp" data-id = "'.htmlspecialchars($row['id']).'"><i class="ri-close-fill"></i></button>
                        </td>';
                  echo '</tr>';
                }
              }

              $allApplicationsStmt->close();
            ?>
            </tbody>
          </table>
        </div>
        
        </div>
        <div class = "applicationSpeciality" style = "max-height: 400px;">
          <div class = "tableTitle">
            <h3> Список консультаций </h3>
          </div>
        <div class = "applicatiionList">
          <table class = "table table-dark table-hover">
            <thead>
              <tr class = "fixedRow">
                <th style = "width: 5%;">ID</th>
                <th>Статус</th>
                <th>ФИО абитуриента</th>
                <th>Номер абитуриента</th>
                <th>ФИО работника</th>
                <th>Номер работника</th>
                <th>Корпус и кабинет</th>
                <th>Дата заявки</th>
                <th>Дата консультации</th>
                <th>Действия</th>
              </tr> 
            </thead>
            <tbody>
              <?php
              $consultationWorkerStmt = $connect->prepare("SELECT * FROM consultations WHERE department = ?");
              $consultationWorkerStmt->bind_param("s", $departmentWorker);
              $consultationWorkerStmt->execute();
              $consultationsForWorkerResult = $consultationWorkerStmt->get_result();

              if($consultationsForWorkerResult->num_rows === 0){
                echo '<li> Нет консультаций </li>';
              } else {
                while($row = $consultationsForWorkerResult->fetch_assoc()){
                    echo '<tr>';
                    echo '<td class = "fixedWidthId" style = "width: 5%;">' . htmlspecialchars($row['id']) . '</td>';
                    echo '<td class = "fixedWidth">' . htmlspecialchars($row['status']) . '</td>';
                    echo '<td class = "fixedWidth">' . htmlspecialchars($row['abiturientFullName']) . '</td>';
                    echo '<td class = "fixedWidth">' . htmlspecialchars($row['abiturientPhoneNumber']) . '</td>';
                    echo '<td class = "fixedWidth">' . htmlspecialchars($row['workerFullName']) . '</td>';
                    echo '<td class = "fixedWidth">' . htmlspecialchars($row['workerPhoneNumber']) . '</td>';
                    echo '<td class = "fixedWidth">' . htmlspecialchars($row['cabinet']) . '</td>';
                    echo '<td class = "fixedWidth">' . htmlspecialchars($row['applicationDate']) . '</td>';
                    echo '<td class = "fixedWidth">' . htmlspecialchars($row['consultationDate']) . '</td>';
                    echo '<td class = "fixedWidth"><button class = "editConsultationBtn" data-id ="' . htmlspecialchars($row['id']) . '"><i class="ri-edit-2-line"></i></button></td>';
                    echo '</tr>';
                }
              }

              $consultationWorkerStmt->close();
              ?>
            </tbody>
          </table>
        </div>
        <div id = "formOverlay">
          <div class = "containerConsultation">
            <h3>Заполнить данные</h3>
            <div class = "form-group">
              <label for ="workerPhone">Номер работника</label>
              <input type="text" id = "workerPhone" required>
            </div>
            <div class = "form-group">
              <label for ="meetingLocation">Место консультации</label>
              <input type="text" id = "meetingLocation" required>
            </div>
            <div class = "form-group">
              <label for ="consultationDate">Дата консультации</label>
              <input type="datetime-local" id = "consultationDate" required>
              <button id="updateConsultationBtn">Вставить</button>
            </div>
            <input type = "hidden" id = "consultationId">
            <span class = "closeFormBtn" id = "closeFormBtn"><i class="ri-close-circle-line"></i></span>
          </div>
        </div>
      <?php endif; ?>
    </div>
        <script>
          document.addEventListener('DOMContentLoaded', function(){
            //создаём переменные, содержащие элементы формы
            var editButtons = document.querySelectorAll('.editConsultationBtn');
            var formOverlay = document.getElementById('formOverlay');
            var closeFormBtn = document.getElementById('closeFormBtn');
            var updateBtn = document.getElementById('updateConsultationBtn');

            //при нажатии на кнопку редактирования консультации
            editButtons.forEach(function(button){
              //считываем клик на кнопку
              button.addEventListener('click', function(){
                //получаем id специальности
                var consultationId = this.getAttribute('data-id');
                //устанавливам значение скрытого поля для дальнешей отправки POST-запроса на сервер
                document.getElementById('consultationId').value = consultationId;
                formOverlay.style.display = 'flex';
              });
            });

            //убираем форму при нажатии кнопки закрыть
            closeFormBtn.onclick = function() {
              formOverlay.style.display = 'none';
            };

            //при нажатии на кнопку обновления консультации
            updateBtn.onclick = function() {
              //объявляем переменные с данными для отправки в будущем POST-запроса
              var workerPhone = document.getElementById('workerPhone').value.trim();
              var meetingLocation = document.getElementById('meetingLocation').value.trim();
              var consultationDate = document.getElementById('consultationDate').value.trim();
              var consultationId = document.getElementById('consultationId').value.trim();

              //устанавливаем обязательное заполнение всех полей
              if(!workerPhone || !meetingLocation || !consultationDate || !consultationId){
                alert("Пожалуйста, заполните все поля формы!");
                return;
              }

              //передаём в массив data данные формы
              const data = {
                consultationId: consultationId,
                workerPhone: workerPhone,
                meetingLocation: meetingLocation,
                consultationDate: consultationDate
              };

              //отправляем POST-запрос на сервер 
              fetch('updateConsultation.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify(data) //передаём массив с данными в теле запроса
              })
              .then(response=> {
                if(!response.ok){
                  //в случае если ответ от сервера ошибочен, выводим статус ответа
                  throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.text();
              })
              .then(responseText => {
                //в случае успеха скрываем форму и перезагружаем страницу
                alert(responseText);
                formOverlay.style.display = 'none';
                location.reload();
              })
              .catch(error=>{
                alert("Ошибка при отправке запроса: " + error.message);
              });
            };
          });

          document.addEventListener('DOMContentLoaded',function() {
            const approveButtons = document.querySelectorAll('.approveApp');
            const denyButtons = document.querySelectorAll('.denyApp');

            approveButtons.forEach(button => {
              button.addEventListener('click', function(){
                const applicationId = this.getAttribute('data-id');

                if(confirm("Вы уверены, что хотите одобрить эту заявку?")){
                  const xhr = new XMLHttpRequest();
                  xhr.open('POST', 'updateApplicationStatus.php', true);
                  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                  xhr.onload = function(){
                    const response = JSON.parse(xhr.responseText);
                    if(response.status === 'success'){
                      alert(response.message);
                      location.reload();
                    }else{
                      alert(response.message);
                    }
                  };
                  xhr.send('id=' + encodeURIComponent(applicationId)+'&status=Одобрено');
                }
              });
            });

            denyButtons.forEach(button => {
              button.addEventListener('click', function(){
                const applicationId = this.getAttribute('data-id');

              if(confirm("Вы уверены, что хотите отклонить эту заявку?")){

                const xhr = new XMLHttpRequest();
                  xhr.open('POST', 'updateApplicationStatus.php', true);
                  xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                  xhr.onload = function(){
                    const response = JSON.parse(xhr.responseText);
                    if(response.status === 'success'){
                      alert(response.message);
                      location.reload();
                    }else{
                      alert(response.message);
                    }
                  };
                  xhr.send('id=' + encodeURIComponent(applicationId)+'&status=Отклонено');
                }
              });
            });
          });

          document.getElementById('edit').addEventListener('click', function(){
            const editForm = document.getElementById('editForm');
            console.log(editForm);
            if(editForm.classList.contains('hidden')){
              editForm.classList.remove('hidden');
            } else {
              editForm.classList.add('hidden');
            }
          });

          document.getElementById('updateUserForm').addEventListener('submit',function(event){
            event.preventDefault();

            var formData = new FormData(this);
            var xhr = new XMLHttpRequest();

            xhr.open('POST', this.action, true);
            xhr.onload = function() {
              if(xhr.status === 200){
                alert('Данные успешно обновлены!');
                location.reload();
              }else {
                alert('Ошибка при обновлении данных!');
              }
            };

            xhr.send(formData); 
          });
        </script>
        <script>
        document.addEventListener('DOMContentLoaded', function(){
          const deleteButtons = document.querySelectorAll('.deleteApp');
          deleteButtons.forEach(button=>{
            button.addEventListener('click', function(event){
              event.preventDefault();

              const specialityName = this.getAttribute('data-speciality');

              if(confirm("Вы уверены, что хотите удалить эту специальность?")){
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'delete-application.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onload = function(){
                  const response = JSON.parse(xhr.responseText);
                  if(response.status === 'success'){
                    event.target.closest('li').remove();
                    alert(response.message);
                  } else {
                    alert(response.message);
                  }
                };
                xhr.send('specialityName=' + encodeURIComponent(specialityName));
              }
            });
          });
        });
      </script>
    </div>
  </div>
   <script src = "https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>