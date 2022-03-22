<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
    $is_editable_address = isset($is_editable_address) ? $is_editable_address : true;
    echo CHtml::hiddenField('DocumentTarget['.$row_index.'][attributes][contact_id]', $contact_id); ?>
<div>
    <textarea class="increase-text cols-full autosize" placeholder="Address" rows="1" cols="10" style="width: 100%; max-height: 100%;" <?php echo !$is_editable_address ? 'readonly' : ''; ?> name="DocumentTarget[<?php echo $row_index;?>][attributes][address]" id="Document_Target_Address_<?php echo $row_index;?>" data-rowindex="<?php echo $row_index ?>"><?php echo $address; ?></textarea>
</div>
<?php if ( Yii::app()->params['send_email_immediately'] === 'on' || Yii::app()->params['send_email_delayed'] === 'on' ) : ?>
        <?php
        $isEmailDisplayable = false;
        if ( (isset($email) && $email !== '') ) {
            $isEmailDisplayable = true;
        }
        if (isset($_POST['DocumentTarget'][$row_index]['DocumentOutput'])) {
            foreach ($_POST['DocumentTarget'][$row_index]['DocumentOutput'] as $document_output) {
                // check when the data is posted back, if the email or email (delayed) is checked then show the email textbox.
                if (isset($document_output['output_type'])) {
                    if ($document_output['output_type'] === \DocumentOutput::TYPE_EMAIL || $document_output['output_type'] === \DocumentOutput::TYPE_EMAIL_DELAYED) {
                        $isEmailDisplayable = true;
                    }
                }
            }
        }
        if ( $contact_type === 'GP' && $can_send_electronically) {
            $isEmailDisplayable = false;
        }

        if ( $contact_type === 'INTERNALREFERRAL') {
            $isEmailDisplayable = false;
        }
        ?>
        <input class="cols-full" placeholder="Email" style="display: <?php echo $isEmailDisplayable ? '' : 'None'; ?>" type="text" <?php echo isset($email) ? 'readonly' : ''; ?> name="DocumentTarget[<?php echo $row_index;?>][attributes][email]" id="DocumentTarget_<?php echo $row_index;?>_attributes_email" value="<?php echo isset($email) ? $email : ''; ?>">
<?php endif; ?>
