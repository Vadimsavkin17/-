error_reporting(E_ALL);
ini_set('display_errors',1);

//Ожидание загрузки всех элементов страницы
document.addEventListener('DOMContentLoaded', function() {

    //Получаем форму по id
    const applicationForm = document.getElementById('applicationForm');

    //Запускаем обработчик событий при нажатии
    applicationForm.addEventListener('submit', function(event){
        event.preventDefault();
        sendApplication();
    });

    function sendApplication(){
        //получаем форму
        const formData = new FormData(applicationForm);
        //отправляем POST-запрос к файлу и передаём в теле запроса форму с данными
        fetch('submitApplication.php', {
            method: 'POST',
            body: formData
        })
        //Получаем ответ в формате json
        .then(response => response.json())
        .then(data => {
            //объявляем переменную, которая будет содержать сообщение с результатом операции
            const messageElement = document.getElementById('message');
            messageElement.textContent = data.message;
            messageElement.style.color = data.success ? 'green' : 'red';
        })
        .catch(error => {
            console.error('Ошибка при отправке заявки: ', error);
        });
    }

    //Объявляем переменные и заносим туда элементы со страницы
    const instituteBlocks = document.querySelectorAll('.instituteBlock');
    const applicationContainer = document.getElementById('applicationContainer');
    const specialitySelect = document.getElementById('speciality');
    const specialityHidden = document.getElementById('specialityHidden');

    //Обработчик события изменения специальности
    specialitySelect.addEventListener('change', function() {
        console.log("Изменение специальности: ", specialitySelect.value);
        //в скрытое поле заносим название специальности
        specialityHidden.value = specialitySelect.value;
    });

    //ко всем блокам с институтами добавляем обработчик событий при нажатии
    instituteBlocks.forEach(block=> {
        block.addEventListener('click', function() {
            //Заносим название института
            const selectedInstitute = this.getAttribute('data-institute');
            //Вызываем функцию loadSpecialities и в качестве аргумента передаём ей название института
            loadSpecialities(selectedInstitute);
            applicationContainer.style.display = 'block';
            applicationContainer.classList.add('show');
        });
    });

    //Обработчик события закрытия формы
    document.getElementById('closeForm').addEventListener('click', function(){
        applicationContainer.style.display = 'none';
        applicationContainer.classList.remove('show');
        document.getElementById('applicationForm').reset();
        specialitySelect.innerHTML = '';
        specialityHidden.value = '';
    });

    //Функция загрузки специальностей
    function loadSpecialities(institute) {
        //Отправляем POST-запрос к файлу fetchSpecialities.php
        fetch('fetchSpecialities.php', {
            method: 'POST',
            headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
            },
            //в теле передаём название института
            body: 'institute=' + encodeURIComponent(institute)
        })
        //получаем ответ в json формате
        .then(response => response.json())
        .then(data=> {
            specialitySelect.innerHTML = ''; //очищаем предыдущие варианты
            console.log("Полученные данные:", data);

            //добавляем опцию по умолчанию в выпадающем списке
            let emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = "Выберите специальность";
            specialitySelect.appendChild(emptyOption);
            console.log("Добавлена пустая опция:",emptyOption);

            //Заносим список специальностей в выпадающий список
            if(data.length > 0) {
                data.forEach(speciality => {
                    let option = document.createElement('option');
                    option.value = speciality.Name;
                    option.textContent = speciality.Name;
                    specialitySelect.appendChild(option);
                });
                //Устанавливаем значение по умолчанию в скрытое поле hiddenSpeciality
                //для корректной передачи его на сервер
                const defaultSpeciality = data[0].Name;
                specialitySelect.value = defaultSpeciality;
                specialityHidden.value = defaultSpeciality;

                $('#speciality').val(defaultSpeciality).trigger('change');
                console.log("Специальность по умолчанию: ", defaultSpeciality);
                console.log("Значение скрытого поля после установки: ", specialityHidden.value);
            } else {
                //в случае если список специальностей не был получен
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