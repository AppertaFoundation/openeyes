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
    $signature_div= "widget_".CHtml::encode($role)."_signature_div_".$this->unique_id;
    $pin_input_name= "widget_".CHtml::encode($role)."_pin_".$this->unique_id;
    $pin_button_id= "widget_".CHtml::encode($role)."_pin_button_".$this->unique_id;
?>
<?php ?>
<tr>
    <td><span class="highlighter">1</span></td>
    <td><?php echo CHtml::encode($this->label) ?></td>
    <td><?php echo CHtml::encode($logged_user_name) ?></td>

        <?php if($this->isSigned()){ ?>
            <td>
                <?php echo Helper::convertDate2NHS($element->created_date) ?>
            </td>
            <td>
                <img src="empty">
            </td>
        <?php } else { ?>
            <td>
                <div class="oe-user-pin">
                    <?php echo CHtml::passwordField($pin_input_name, '', array(
                        'placeholder'=>"********",
                        'maxlength'=>"8",
                        'inputmode'=>"numeric",
                        'class'=>"user-pin-entry"
                    )); ?>
                    <button id="<?php echo CHtml::encode($pin_button_id) ?>" class="try-pin js-idg-ps-popup-btn" data-action="next">PIN sign</button>
                </div>
            </td>
        <?php } ?>
    </td>
    <td>
        <div id="<?php echo CHtml::encode($signature_div) ?>"></div>
    </td>
</tr>

<script language="JavaScript">
    (function() {
        let pin_input = document.getElementById("<?php echo $pin_input_name ?>");
        let signature_div = document.getElementById("<?php echo $signature_div ?>");

        document.getElementById('<?php echo CHtml::encode($pin_button_id) ?>').addEventListener('click',() => {
            let signature_pin = pin_input.value;
            let params = {signature_pin,YII_CSRF_TOKEN:YII_CSRF_TOKEN};
            const searchParams = Object.keys(params).map((key) => {
                return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
            }).join('&');

            fetch(baseUrl + "/" + moduleName + "/default/<?php echo $this->action ?>",{
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
                },
                body: searchParams,
                method: 'POST',
            })
                .then(response => response.json())
                .then(data => {
                    if(data) {
                        if( data.code !== 0){
                            signature_div.innerHTML = '<span class="error">'+data.error+'</span>';
                        } else {
                            signature_div.innerHTML = '<img src="data:image/png;base64, '
                                +(data.singature_image_base64)
                                +'">';
                        }
                    }
            });
        })
    })();
</script>