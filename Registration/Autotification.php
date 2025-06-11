<?php
	session_start();

	if(!empty($_SESSION['messageR'])) 
   	$message = $_SESSION['messageR'];
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Авторизация и регистрация</title>
		<!-- Подключение CSS -->
    <link rel = "stylesheet" href = "../css/Autintifications.css">
    <!-- <link rel = "stylesheet" href = "../css/style.css"> -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  </head>
  <body>
  	<button class = "buttonEscape" id = "ButtonBack"> Назад </button>
  	<script>
    	document.getElementById("ButtonBack").onclick = function() {
        	window.location.href = "http://localhost/pma/main.php"; // Укажите нужный URL
    	};
		</script>
  	<form action = "Vendor/signIn.php" method = "post">
			<section class="vh-100 gradient-custom">
			  <div class="container py-5 h-100">
			    <div class="row d-flex justify-content-center align-items-center h-100">
			      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
			        <div class="card bg-dark text-white" style="border-radius: 1rem;">
			          <div class="card-body p-5 text-center">

			            <div class="AutintificationsForm">

			              <h2 class="title">Авторизация</h2>
			              <p class="message">Пожалуйста введите логин и пароль</p>

			              <div data-mdb-input-init class="form-outline form-white mb-4">
			                <input type="text" id="typeEmailX" class="form-control form-control-lg" placeholder="Введите логин" name = "Login"/>
			                <label class="form-label" for="typeEmailX">Логин</label>
			              </div>

			              <div data-mdb-input-init class="form-outline form-white mb-4">
			                <input type="password" id="typePasswordX" class="form-control form-control-lg" placeholder="Введите пароль" name = "Password"/>
			                <label class="form-label" for="typePasswordX">Пароль</label>
			              </div>

			              <p class="forPsw"><a class="linkP" href="#!">Забыли пароль</a></p>

			              <button class="button" type="submit">Войти</button>
			            </div>
			            <div class = "under">
			              <p class="labelR">Нет аккаунта? <a href="registration.php" class="text-white-50 fw-bold">Зарегистрироваться</a>
			              </p>
			            </div>
			          </div>
			        </div>
			        <br>
			        <?php 
		        		if (isset($_SESSION['messageR'])) {
    							echo '<p class = "msg" style = "border: 2px solid #6a11cb; border-radius: 3px; padding: 10px; text-align: center; font-weight: bold ; font-size: 20px;">' . $_SESSION['messageR'] .'</p>';
    							unset($_SESSION['messageR']);
								}
		        	?>
			      </div>
			    </div>
			  </div>
			</section>
		</form>
  </body>
 </html>