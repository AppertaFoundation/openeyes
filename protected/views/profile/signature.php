<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
/** @var User $user */
/** @var bool $recapture */
?>
<h2>Stored signature</h2>
<?php if (!$recapture && $user->checkSignature()): ?>
  <div class="standard">
    <p>You have a captured signature in the system:</p>
    <div id="signature_image">
        <?= $user->getSignatureImage(["width" => 450, "height" => 150]); ?>
    </div>
    <br>
    <a class="button primary" href="/profile/signature?recapture=1">Click here to replace it with a new signature</a>
  </div>
<?php else: ?>
    <?php if(!$recapture): ?>
    <div class="standard">
        <p>You have not captured any signature yet. To do so please use the form below.</p>
    </div>
    <?php endif; ?>
    <?php $this->widget("application.widgets.SignatureCapture", [
        "submit_url" => "/profile/uploadSignature",
        "after_submit_js" => "
            function(response, widget) {
                if(response.success) {
                    window.location.href = '/profile/signature';
                }
                else {
                    new OpenEyes.UI.Dialog.Alert({
                        content: response.message
                    }).open();
                }
            }"
    ]); ?>
<?php endif; ?>