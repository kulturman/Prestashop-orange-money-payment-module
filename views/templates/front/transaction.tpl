{extends "$layout"}

{block name="content"}
    <h2>{l s='Procédure de paiement par Orange money' mod='orangemoneypayment'}</h2>
    <p>
        {l s='Le coût de votre commande est de' mod='orangemoneypayment'} {$formattedAmount}, {l s='payez par OM en faisant:' mod='orangemoneypayment'}
        <strong style="color: red; font-size: 1.2em;">
            *144*4*6*{$amountToPay}*{l s='mot de passe' mod='orangemoneypayment'}#
        </strong>
    </p>
    <p style="color:red;font-size: 1.2em;">({l s="Si vous ne disposez pas d'un compte, vous pouvez vous rendre chez un agent Orange Money" mod='orangemoneypayment'})</p>
    <p style='font-weight:bold'>
        {l s='Vous recevrez ensuite un message contenant un code appelé OTP, veuillez le renseigner ainsi que le numéro de téléphone dans les champs ci dessous' mod='orangemoneypayment'}
    </p>
    <div class="alert alert-block alert-danger" id="error-msg">
        {l s='Veuillez entrer un numéro OTP valide!' mod='orangemoneypayment'}
    </div>
    <form action="index.php?fc=module&module=orangemoneypayment&controller=validation" 
          method="post" class = "form-horizontal"
          id="payment-form">
        <div>
            <div class = "form-group" style = "width:40%;display:inline-block">
                <label>{l s='Code OTP (reçu par SMS)' mod='orangemoneypayment'}</label>
                <input type = "password" name = "transaction_id" id = "transaction_id" class = "form-control"><br/>
            </div>
            <div class = "form-group" style = "width:40%;display:inline-block">
                <label>{l s="Numéro de téléphone ayant servi à créer l'OTP (sans espaces ni de tirets)" mod='orangemoneypayment'}</label>
                <input type = "text" name = "phone_number" id = "phone_number" class = "form-control"><br/>
            </div>
            <div>
                <button class = "btn btn-info" id = "validate_button">{l s="Confirmer le paiement" mod='orangemoneypayment'}</button>
            </div><br/>
        </div>

    </form>
    <script>
        let codeShouldHave6DigitsMessage = '{l s='Le code doit contenir six (6) chiffres' mod='orangemoneypayment'}'
        let pleaseWaitMessage = '{l s='Veuillez patientez SVP' mod='orangemoneypayment'}'
        let invalidPhoneNumberMessage = '{l s='Le numéro de téléphone est invalide' mod='orangemoneypayment'}'
        let requestErrorMessage = '{l s='Une erreur inattendue est survenue, veuillez réessayer plus tard' mod='orangemoneypayment'}'
    </script>
{/block}
