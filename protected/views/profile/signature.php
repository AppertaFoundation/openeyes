<?php
/* copy latest copyright!*/
?>

<div class="box admin">
    <h2>Stored signature</h2>

    <?php
    if($user->checkSignature())
    {?>
        <div>
            You have a captured signature in the system, if you want to check the signature image please enter your 4 digit PIN:
            <div id="div_signature_pin" class="row field-row">
                <div class="large-2 column">
                    <label for="signature_pin">PIN:</label>
                </div>
                <div class="large-2 column">
                    <input type="password" maxlength="4" name="signature_pin" id="signature_pin">
                </div>
                <div class="large-2 column end">
                    <button class=" primary event-action" name="show_signature" type="submit" id="et_show_signature">OK</button>
                    <input type="hidden" name="YII_CSRF_TOKEN" id="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
                </div>
            </div>
            <div id="signature_image">

            </div>

            <br>
            If you want to change your current signature and PIN:
            <ol>
                <li>Visit <?= Yii::app()->params['signature_app_url'] ? : "the OpenEyes Phone Application" ?> on your mobile device.</li>
                <li>Scan the QR code displayed below with the application.</li>
                <li>Follow the prompts from there.</li>
            </ol>
        </div>
    <?php
    }else
    {?>
        <div>
            You have not captured any signature yet. To do so please:
            <ol>
                <li>Visit <?= Yii::app()->params['signature_app_url'] ? : "the OpenEyes Phone Application" ?> on your mobile device.</li>
                <li>Scan the QR code displayed below with the application.</li>
                <li>Follow the prompts from there.</li>
            </ol>
        </div>

    <?php }?>
    <div>
        <img src="/profile/generateSignatureQR" border="0">
    </div>
    After you've finshed scanning your signature please press this button:
    <div>
        <button class=" primary event-action" name="get_signature" type="submit" id="et_get_signature">Load my signature into the system</button>
    </div>
</div>