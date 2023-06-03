$('#validate_button').attr('disabled' , 'disabled');

$('#transaction_id,#phone_number').keyup(function(event) {
    validateCode();
})

function validateCode(){
    var userInput = $('#transaction_id').val();
    var errorMessage = null;

	if(userInput.length != 6) {
		errorMessage = 'Le code doit contenir 6 chiffres';
	}
	else {
		var regexp = /^[0-9]+$/;
		if (!userInput.match(regexp)) {
            errorMessage = "Le code entré est invalide";
        }
        else {
            errorMessage = null;
        }
    }
    if(!errorMessage) {
        userInput = $('#phone_number').val();
        if(userInput.length != 8) {
            errorMessage = 'Le numéro de téléphone est invalide';
        }
        else {
            var regexp = /^[0-9]+$/;
            if (!userInput.match(regexp)) {
                errorMessage = "Le numéro de téléphone est invalide";
            }
            else {
                errorMessage = null;
            }
        }
    }
    handleMessageVisibility(errorMessage);
}

function handleMessageVisibility(errorMessage) {
    if(errorMessage) {
        $('#error-msg').html(errorMessage); 
        $('#validate_button').attr('disabled' , 'disabled');
        $('#error-msg').show();
    }
    else {
        $('#validate_button').removeAttr('disabled');
        $('#error-msg').hide();
    }
}

$('#payment-form').submit(function(e) {
    e.preventDefault();
    HoldOn.open({
        theme: "sk-circle",
        message: "<h4>Veuillez patienter SVP</h4>"
    });
    var url = $(this).attr('action');
    $.ajax({
        url: url,
        data: $(this).serialize(),
        method: 'POST'
    })
    .then(function(data) {
        data = jQuery.parseJSON(data);
        HoldOn.close();
        if(!data.success) {
            Swal.fire({
                title: 'Erreur',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            })
        }
        else {
            document.location.href = data.url;
        }
    })
    .fail(function() {
        HoldOn.close();
        Swal.fire({
            title: 'Erreur',
            text: "Une erreur inattendue est survenue, veuillez réessayer plus tard",
            icon: 'error',
            confirmButtonText: 'OK'
        })
    });
})