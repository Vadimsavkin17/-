<?php
session_start();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Регистрация</title>
    <link rel="stylesheet" href="../css/Autintifications.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <button class="buttonEscape" id="ButtonBack">Назад</button>
    <script>
        document.getElementById("ButtonBack").onclick = function() {
            window.location.href = "http://localhost/pma/main.php"; // Укажите нужный URL
        };
    </script>
    <form action="Vendor/signUp.php" method="post">
        <section class="vh-100 gradient-custom">
            <div class="container py-5 h-100">
                <div class="row d-flex justify-content-center align-items-center h-100">
                    <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                        <div class="card bg-dark text-white" style="border-radius: 1rem;">
                            <div class="card-body p-5 text-center">
                                <div class="AutintificationsRegisterForm">
                                    <h2 class="titleRegister">Регистрация</h2>
                                    <p class="message">Пожалуйста заполните поля</p>

                                    <div class="form-outline form-white mb-4">
                                        <label class="form-label" for="fullName">ФИО</label>
                                        <input type="text" id="fullName" class="form-control form-control-lg" placeholder="Введите ФИО" name="FullName" aria-invalid = "true" required />
                                    </div>

                                    <div class="form-outline form-white mb-4">
                                        <label class="form-label" for="login">Логин</label>
                                        <input type="text" id="login" class="form-control form-control-lg" placeholder="Введите логин" name="Login" required />
                                    </div>

                                    <div class="form-outline form-white mb-4">
                                        <label class="form-label" for="email">Почта</label>
                                        <input type="email" id="email" class="form-control form-control-lg" placeholder="Введите почту" name="Email" required />
                                    </div>
                                    <div class = "custom-select" style = "width: 200px;">
                                        <select class="select" name="user_type">
                                            <option disabled>Выбрать</option>
                                            <option value="Абитуриент">Абитуриент</option>
                                            <option value="Преподаватель">Преподаватель</option>
                                            <option value="Администратор">Администратор</option>
                                        </select>
                                    </div>
                                    <div class="form-outline form-white mb-4">
                                        <label class="form-label" for="password">Пароль</label>
                                        <input type="password" id="password" class="form-control form-control-lg" placeholder="Введите пароль" name="Password" required />
                                    </div>

                                    <div class="form-outline form-white mb-4">
                                        <label class="form-label" for="passwordConfirm">Подтвердите пароль</label>
                                        <input type="password" id="passwordConfirm" class="form-control form-control-lg" placeholder="Введите пароль ещё раз" name="PasswordConfirm" required />
                                    </div>

                                    <button class="buttonRegister" type="submit" name="submit">Зарегистрироваться</button>
                                </div>

                                <div class="under">
                                    <p class="labelR">Есть аккаунт? <a href="Autotification.php" class="text-white-50 fw-bold">Авторизоваться</a></p>
                                </div>

                                <?php 
                                if (isset($_SESSION['message'])) {
                                    echo '<p class="msg" style="color: red;">' . $_SESSION['message'] .'</p>';
                                    unset($_SESSION['message']);
                                }
                                ?>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </section>
    </form>
</body>
</html>