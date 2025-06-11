document.addEventListener('DOMContentLoaded', function() {
	const applicationContainer = document.getElementById('applicationContainer');

	document.addEventListener('click', function(event) {
		if(event.target.classList.contains('ri-close-fill')) {
			applicationContainer.classList.remove('show');
			applicationContainer.addEventListener('transitioned',function() {
				if(!applicationContainer.classList.contains('show')){
					applicationContainer.style.display = 'none';
				}
			}, {once: true});
		}
	});
});