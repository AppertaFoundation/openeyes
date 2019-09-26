<?php
/* copy latest copyright!*/
?>

<h2>Stored signature</h2>
<?php if ($user->checkSignature()) {?>
  <div class="standard">
    You have a captured signature in the system, if you want to check the signature image please enter your 4 digit PIN:
    <div id="div_signature_pin" class="data-group">
      <div class="cols-2">
        <label for="signature_pin">PIN:</label>
      </div>
      <div class="cols-2">
        <input type="password" maxlength="4" name="signature_pin" id="signature_pin">
      </div>
      <div class="cols-2">
        <button class=" primary event-action" name="show_signature" type="submit" id="et_show_signature">OK</button>
        <input type="hidden" name="YII_CSRF_TOKEN" id="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
      </div>
    </div>
    <div id="signature_image"></div>
    <br>
    If you want to change your current signature and PIN:
    <ul>
      <li>Visit <?= Yii::app()->params['signature_app_url'] ? : "the OpenEyes Phone Application" ?> on your mobile device.</li>
      <li>Scan the QR code displayed below with the application.</li>
      <li>Follow the prompts from there.</li>
    </ul>
  </div>
<?php } else {?>
  <table class="standard">
    <tbody>
    <tr>
      <td>You have not captured any signature yet. To do so please:</td>
    </tr>
    <tr>
      <td>Visit <?= Yii::app()->params['signature_app_url'] ? : "the OpenEyes Phone Application" ?> on your mobile device.</td>
    </tr>
    <tr>
      <td>Scan the QR code displayed below with the application.</td>
    </tr>
    <tr>
      <td>Follow the prompts from there.</td>
    </tr>
    <tr>
      <td>
        <img src="/profile/generateSignatureQR" border="0">
      </td>
    </tr>
    <tr>
      <td>
        After you've finished scanning your signature please press this button:
        <button class="primary button large green hint event-action" name="get_signature" type="submit" id="et_get_signature">Load my signature into the system</button>
      </td>
    </tr>
    </tbody>
  </table>
<?php } ?>