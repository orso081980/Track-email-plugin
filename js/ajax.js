
let form = $('#contatto'),
pluginfolder = settings,
submitButton = $('#submit-netrack'),
form_messages = $('#form-message-netrack');

$('#contatto').submit(function(e) {
	var form_data = form.serialize();
	e.preventDefault();
	if ($(this).parsley().isValid()) {
		$.ajax({ 
			data: form_data +'&submit_form='+submitButton.val(),
			type: 'POST',
			url: pluginfolder.ajaxurl,
			dataType: 'json',
			success: function(data) {
				if (data[0] == true) {
					form_messages.html(data[1]);
					submitButton.prop("disabled", true);
					submitButton.val("Sent!");
				} else {
					form_messages.html(data[1]);
					submitButton.val("Please, change your email");
				}
				
			},
			error: function (request, status, error) {
				console.log(request.responseText);
			}
		});
	};
});
