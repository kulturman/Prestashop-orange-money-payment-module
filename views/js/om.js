$('#validate_button').attr('disabled' , 'disabled');

$('#transaction_id,#phone_number').keyup(function(event) {
    validateCode();
})

function validateCode(){
    let userInput = $('#transaction_id').val();
    let errorMessage = null;

    let regexp = /^[0-9]{6}$/;

    if (!userInput.match(regexp)) {
        errorMessage = codeShouldHave6DigitsMessage;
    }
    else {
        errorMessage = null;
    }

    if(!errorMessage) {
        userInput = $('#phone_number').val();
        let regexp = /^[0-9]{8}$/;
        
        if (!userInput.match(regexp)) {
            errorMessage = invalidPhoneNumberMessage;
        }
        else {
            errorMessage = null;
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
        message: "<h4>"+pleaseWaitMessage+"</h4>"
    });
    let url = $(this).attr('action');
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
            text: requestErrorMessage,
            icon: 'error',
            confirmButtonText: 'OK'
        })
    });
})