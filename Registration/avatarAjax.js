document.getElementById('avatarForm').addEventListener('submit', function(event){
	event.preventDefaulf();

	var formData = new FormData(this);
	var xhr = new XMLHttpRequest();

	xhr.open('POST', this.action, true);
	xhr.onload = function() {
		if(xhr.status == 200){
			document.getElementById('profile-picture').src = URL.createObjectURL(document.getElementById('avatar-input').files[0]);	
		} else {
			console.error('Ошибка при загрузке аватара!');
		}
	};

	xhr.send(formData);
});