let validateButtonElement = document.querySelector('#validate_button');
let otpElement = document.querySelector('#otp');
let phoneNumberElement = document.querySelector('#phone_number');
let errorMessageElement = document.querySelector('#error-msg');
let paymentFormElement = document.querySelector('#payment-form');

validateButtonElement.setAttribute('disabled', 'disabled');

otpElement.addEventListener('keyup', validateCode);
phoneNumberElement.addEventListener('keyup', validateCode);

function validateCode(){
    let userInput = otpElement.value;
    let errorMessage = null;

    let regexp = /^[0-9]{6}$/;

    if (!userInput.match(regexp)) {
        errorMessage = codeShouldHave6DigitsMessage;
    }
    else {
        errorMessage = null;
    }

    if(!errorMessage) {
        userInput = phoneNumberElement.value;
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
        errorMessageElement.innerHTML = errorMessage;
        validateButtonElement.setAttribute('disabled', 'disabled');
        errorMessageElement.style.display = 'block';
    }
    else {
        validateButtonElement.removeAttribute('disabled');
        errorMessageElement.style.display = 'none';
    }
}

paymentFormElement.addEventListener('submit', e => {
    e.preventDefault();

    HoldOn.open({
        theme: "sk-circle",
        message: "<h4>"+pleaseWaitMessage+"</h4>"
    });

    let url = paymentFormElement.getAttribute('action');

    fetch(url, {
        method: 'POST',
        body: new FormData(paymentFormElement)
    })
        .then(response => response.json())
        .then(data => {
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
        .catch(error => {
            HoldOn.close();
            Swal.fire({
                title: 'Erreur',
                text: requestErrorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            })
        });
})
