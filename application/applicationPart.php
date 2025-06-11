<?php
session_start();

$selectedSpeciality = isset($_GET['speciality']) ? htmlspecialchars($_GET['speciality']) : '';

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once '../config/settings.php';

if(!isset($_SESSION['user_id'])){
// echo 'User ID: вы не вошли';
}
else {
// echo 'User Id: ' . $_SESSION['user_id'];
}
if(isset($_COOKIE['user_token'])){
$user_token = $_COOKIE['user_token'];
$stmt = $connect->prepare("SELECT * FROM user WHERE token = ?");
$stmt->bind_param("s", $user_token);
$stmt->execute();
$result = $stmt->get_result();

//Если токен валиден, регистрируем пользователя в сессии
if($result->num_rows > 0) {
$user = $result->fetch_assoc();
$fullName = $user['FullName'];
} else {
header('Location: /Registration/Vendor/SignIn.php');
exit();
}
} else {
header('Location: /Registration/Vendor/SignIn.php');
exit();
}

$specialityCount = 0; // Инициализация переменной по умолчанию

$stmt = $connect->prepare("SELECT FullName, specialityCount FROM user WHERE token = ?");
$stmt->bind_param("s", $user_token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
$user = $result->fetch_assoc();
$fullName = $user['FullName'];
$specialityCount = isset($user['specialityCount']) ? (int)$user['specialityCount'] : 0; // Получаем значение specialityCount
} else {
header('Location: ../Registration/Vendor/SignIn.php');
exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name = "viewport" content = "width=device-width, initial-scale=1.0">
<title> Подача заявок </title>

<!-- CSS СТИЛИ -->
<link rel = "stylesheet" href = "../css/application.css">
<link rel = "stylesheet" href = "../css/header.css">
<!-- CSS СТИЛИ -->

<!--=============== Шрифт ===============-->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<!--=============== Шрифт ===============-->

<!--=============== REMIXICONS ===============-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/3.5.0/remixicon.css" crossorigin="">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>

<!--=============== BOXICONS ===============-->
<link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

<!-- =============== SELECT2 =============== -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>


<script type="text/javascript">
    $(document).ready(function() {
        $('#speciality').select2({
            width: '100%',
            minimumResultsForSearch: Infinity //Отключение поисковой строки
        });

        var initialSpeciality = $('#speciality').val();
        $('#specialityHidden').val(initialSpeciality);

        $('#speciality').on('change', function(){
            var selectedSpeciality = $(this).val();
            $('#specialityHidden').val(selectedSpeciality);
        });
    });
</script>
</head>

<body>
    <!-- НАЧАЛО Навигационная панель -->
    <!-- Навигационная панель -->
    <header>
        <a href = "../main.php" class = "logo"> <h1 class = "title"> <span>АбитурГид</span></h1></a>

        <ul class = "navbar">
            <li><a href = "../Specialties/ownFunction.php">Калькулятор</a></li>
            <li><a href = "../search.php">Новости</a></li>
            <li><a href = "#">Подать заявку</a></li>
            <li><a href = "../Registration/personalOffice.php">Кабинет</a></li>
        </ul>

        <div class = "main">
            <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] !== ''): ?>
            <a href="../Registration/Vendor/logOut.php" class = "user"><i class = "ri-door-open-line"></i>Выйти</a>
            <?php else: ?>
            <a href="../Registration/autotification.php" class = "user"><i class="ri-user-fill"></i>Войти</a>
            <a href="../Registration/registration.php" class = "user">Зарегистрироваться</a>
            <?php endif; ?>
            <div class = "bx bx-menu" id = "menu-icon"></div>
        </div>
    </header>
    <!-- Навигационная панель -->

    <!-- Скрипты -->
    <script type="text/javascript">
        window.addEventListener("scroll", function() {
            const header = document.querySelector("header");
            if (window.scrollY > 0) {
            header.classList.add("scrolled");
            } else {
            header.classList.remove("scrolled");
            }
        });
    </script>
    <script type="text/javascript" src = "../header.js"></script>
    <div class="info-panel">
        <h1 class="info-title">Подача заявок на поступление</h1>
        <p class="info-text">
        <i class="ri-circle-fill"></i>
        Пожалуйста, заполните свои данные и выберите специальность, на которую вы хотите поступить.
        </p>
        <p class="info-text">
        <i class="ri-circle-fill"></i>
        Обратите внимание, что вы можете подать максимум 3 заявки.
        </p>
        <p class="info-text">
        <i class="ri-circle-fill"></i>
        В случае, если вы захотите просмотреть или отменить одну из заявок, это можно сделать в вашем личном кабинете.
        </p>
        <p class="info-text">
        <i class="ri-circle-fill"></i>
        В случае неверно введённых данных, противоречащих требованиям, заявка не будет рассмотрена и в течение некоторого времени - удалена. Пользователь, у которого будет удалено 3 заявки будет заблокирован из-за нарушений правил системы.
        </p>
    </div>
    <!-- НАЧАЛО Блок с институтами-->
    <h1> Выберите институт </h1>
    <div class = "institute-container">
        <div class = "instituteBlock" data-institute = "Политехнический институт">
            <h2>Политехнический институт</h2>
            <i class="ri-macbook-line"></i>
        </div>
        <div class = "instituteBlock" data-institute = "Юридический факультет">
            <h2> Юридический факультет </h2>
            <i class="ri-auction-fill"></i>
        </div>
        <div class = "instituteBlock" data-institute = "Педагогический институт">
            <h2>Педагогический институт</h2>
            <i class="ri-book-open-line"></i>
        </div>
        <div class = "instituteBlock" data-institute = "Институт физической культуры и спорта">
            <h2>Институт физической культуры и спорта</h2>
            <i class="ri-basketball-line"></i>
        </div>
        <div class = "instituteBlock" data-institute = "Институт экономики и управления">
            <h2>Институт экономики и управления</h2>
            <i class="ri-funds-box-line"></i>
        </div>
        <div class = "instituteBlock" data-institute = "Медицинский институт">
            <h2>Медицинский институт</h2>
            <i class="ri-heart-pulse-line"></i>
        </div>
    </div>
    <!-- КОНЕЦ Блок с институтами -->
    <br>
    <div id = "applicationContainer" class = "applicationContainer" style = "display:none;">
        <form action = "submitApplication.php" id = "applicationForm" method="POST">

        <h1 class = "formTitle"> Форма подачи заявки </h1>

        <div id = "specialityCount">
            <p>Текущее количество заявок: <? <span id = "currentCount"><?php echo $specialityCount; ?> </span> </p>
            </div>
            <h2 class = "titleBlock">Выберите специальность</h2>
            <div class = "specialitySelect" data-type = "speciality">
                <span>
                <select id = "speciality"name = "speciality" required>
                    <option value = "<?php echo $selectedSpeciality; ?>" selected><?php echo $selectedSpeciality ?: 'Выберите специальность'; ?></option>
                <!-- Загрузка специальностей через JS -->
                </select>
                </span>
            </div>
        <div class = "btnClose">
            <button type = "button" class = "hide-form" id = "closeForm">
            <i id = "closeBtn"class="ri-close-fill"></i>
            </button>
        </div>
        <h2 class = "titleBlock">Введите личные данные</h2>
        <div class = "formContainer1">
            <input type = "text" id = "student" name = "student" class = "inputName" placeholder="Ваше ФИО"required>
            <input type="email" id="email" name = "email" class = "inputEmail" placeholder="Электронная почта"required>
        </div>
        <div class = "formContainer1">
            <input type = "text" id = "phoneNumber" class="phoneNumber" name = "phoneNumber"placeholder="Номер телефона" required>

            <input type="place" class = "place" name="place" id = "place" placeholder="Населённый пункт" required>
        </div>
        <button type = "submit" class = "submitBtn"> Отправить заявку </button>
        <input type="hidden" id="specialityHidden" name="specialityHidden" value="">
    </form>
    <script type="text/javascript">
        document.getElementById('applicationForm').addEventListener('submit',function(e){
            e.preventDefault();
            const formData = new FormData(this);
            fetch('submitApplication.php',{
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageElement = document.getElementById('message');
                if(messageElement){
                    messageElement.textContent = data.message;
                    messageElement.style.color = data.success ? 'green' : 'red';
                    messageElement.style.display = 'block';
                } else {
                    console.error('Элемент сообщения не найден');
                }
            })
            .catch(error => {
                console.error('Ошибка при отправке заявки:', error);
            });
            
            const hiddenValue = document.getElementById('specialityHidden').value;
            console.log("Значение специальности перед отправкой формы:", hiddenValue);
            if(!hiddenValue){
                e.preventDefault();
                alert('Пожалуйста, выберите специальность');
            }
        });
    </script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {

            const instituteBlocks = document.querySelectorAll('.instituteBlock');
            const applicationContainer = document.getElementById('applicationContainer');
            const specialitySelect = document.getElementById('speciality');
            const specalityHidden = document.getElementById('specialityHidden');

            specialitySelect.addEventListener('change', function() {
                console.log("Изменение специальности: ", specialitySelect.value);
                specialityHidden.value = specialitySelect.value;
            });

            instituteBlocks.forEach(block=> {
                block.addEventListener('click', function() {
                    const selectedInstitute = this.getAttribute('data-institute');
                    loadSpecialities(selectedInstitute);
                    applicationContainer.style.display = 'block';
                    applicationContainer.classList.add('show');
                });
            });

            document.getElementById('closeForm').addEventListener('click', function(){
                applicationContainer.style.display = 'none';
                applicationContainer.classList.remove('show');
                document.getElementById('applicationForm').reset();
                document.getElementById('speciality').innerHTML = '';
            });

            //Функция загрузки специальностей
            function loadSpecialities(institute) {
                //отправляем сетевой запрос на сервер
                fetch('fetchSpecialities.php', {
                    method: 'POST',
                    headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    //передаём в теле запроса параметр институт
                    body: 'institute=' + encodeURIComponent(institute)
                })
                .then(response => response.json())
                .then(data=> {
                    specialitySelect.innerHTML = ''; //очищаем предыдущие варианты

                    //в случае если сервер вернул не пустой массив, проходимся по каждой специальности и создаём для неё элемент option, который добавляется в селектор
                    if(data.length > 0) {
                        data.forEach(speciality => {
                        let option = document.createElement('option');
                            option.value = speciality.Name;
                            option.textContent = speciality.Name;
                            specialitySelect.appendChild(option);
                        });
                    } else {
                        //иначе создаём один элемент option, сообщающий, что специальности не нашлись
                        let option = document.createElement('option');
                        option.value = '';
                        option.textContent = "Нет доступных специальностей";
                        specialitySelect.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Ошибка при загрузке специальностей:',error);
                });
            }
        });
    </script>
    </div>
    <script>
        function updateApplicationCount() {
            const userId = "<?php echo $_SESSION['user_id']; ?>";

            fetch('getApplicationCount.php' , {
                method: 'POST',
                headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'user_id=' + userId
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('currentCount').textContent = data.count;
            })
            .catch(error => console.error('Ошибка: ', error));
        }

        setInterval(updateApplicationCount, 5000); //Обновление количества заявок каждые 5 секунд

        document.addEventListener("DOMContentLoaded", updateApplicationCount);
    </script>
    <div id = "message"></div>
    <script type = "text/javascript" src="animationForm.js"></script>
    </body>
</html>