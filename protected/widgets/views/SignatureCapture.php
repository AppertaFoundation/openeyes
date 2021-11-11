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
/** @var SignatureCapture $this */
?>
<div class="esign-capture">
    <div class="flag-esign">Please sign here</div>
    <!-- set up canvas using a 3:1 ratio, then use the JS to resize to fill area -->
    <canvas id="SignatureCapture_<?=$this->uid?>_canvas" width="1192" height="397" style="touch-action: none;"></canvas>
    <div class="js-signature-buttons-container esign-actions">
        <button id="SignatureCapture_<?=$this->uid?>_gofull" class="button hint blue" type="button" data-toggle-text="Back to embedded view">Use Drawing Tablet</button>
        <button id="SignatureCapture_<?=$this->uid?>_erase" class="button hint red" type="button">Erase signature</button>
        <button id="SignatureCapture_<?=$this->uid?>_save" class="button hint green" type="button">Save signature</button>
    </div>
</div>
<script type="text/javascript">
    (function(){
        function initWidget() {
            if (typeof window.sc_<?=$this->uid?> === "undefined") {
                window.sc_<?=$this->uid?> = new OpenEyes.UI.SignatureCapture({
                    canvasSelector: "#SignatureCapture_<?=$this->uid?>_canvas",
                    submitURL: "<?=$this->submit_url?>",
                    csrf: {
                        name: "<?=Yii::app()->request->csrfTokenName?>",
                        token: "<?=Yii::app()->request->csrfToken?>"
                    },
                    afterSubmit: <?=$this->after_submit_js?>,
                    openButtonSelector: "#signature_open_<?=$this->uid?>",
                    widgetId: "<?=$this->uid?>",
                    eraseButtonSelector: "#SignatureCapture_<?=$this->uid?>_erase",
                    saveButtonSelector: "#SignatureCapture_<?=$this->uid?>_save",
                    toggleFullScreenButtonSelector: "#SignatureCapture_<?=$this->uid?>_gofull"
                });
            }
        }
        if(self !== top) {
            // in an iframe
            $(window.frameElement).load(initWidget);
        }
        else {
            $(document).ready(initWidget);
        }
    })();
</script>
