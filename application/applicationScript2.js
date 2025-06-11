$(document).ready(function(){
	$('instituteBlock').on('click',function(){
		var selectedInstitute = $(this).data('institute');

		$.ajax({
			type: 'POST',
			url: 'fetchSpecialities.php',
			data: {institute: selectedInstitute},
			success: function(response){
				var specialitySelect = $('#speciality');
				specialitySelect.empty();
				specialitySelect.append('<option value = ""> Выберите специальность </option>');

				$.each(response, function(index, speciality){
					specialitySelect.append('<option value ="' + speciality.id + '">' + speciality.Name + '</option>');

				});

				specialitySelect.val('').trigger('change');
			},
			error: function(){
				alert("Ошиибка при загрузке специальностей, попробуйте позже");
			},
			dataType: 'json'
		});

		$('#applicationContainer').show();
	});
});