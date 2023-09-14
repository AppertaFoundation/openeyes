<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$is_bottom = isset($bottom) && $bottom;

if (isset($errors) && !empty($errors)) { ?>
    <div class="alert-box error with-icon<?= $is_bottom ? ' bottom' : '' ?>"
        <?= !$is_bottom ? " data-test=\"validation-errors\"" : ''?>>
        <?php if (isset($errorHeaderMessage) && !empty($errorHeaderMessage)) { ?>
            <p><?= $errorHeaderMessage ?></p>
        <?php } else { ?>
            <p>Please fix the following input errors:</p>
        <?php } ?>
        <?php foreach ($errors as $field => $errs) { ?>
            <?php foreach ($errs as $err) { ?>
                <ul>
                    <li>
                        <?php echo $field . ': ' . $err ?>
                    </li>
                </ul>
            <?php } ?>
        <?php } ?>
    </div>
<?php } ?>
<script type="text/javascript">
    $(document).ready(function () {
        <?php if (isset($elements) && is_array($elements)) {
            foreach ($elements as $element) { ?>
        var errorObject = <?= json_encode($element->getFrontEndErrors()) ?>;
        for (k = 0; k < errorObject.length; k++) {
            var $field = $('#' + errorObject[k]);
            $field.closest('.element').find('.element-title').addClass('error');
            if ($field.length) {
                if ($field.is('tr') || $field.is('input')) {
                    $field.addClass('highlighted-error error');
                } else {
                    if (!$field.parent().hasClass('highlighted-error error')) {
                        $field.addClass('highlighted-error error');
                    }
                }
            } else {
                $('[id*="' + errorObject[k] + '"]').closest('.element').find('.element-title').addClass('error');
                if (!$('[id*="' + errorObject[k] + '"]').hasClass('error')) {
                    $('[id*="' + errorObject[k] + '"]:not(:hidden)').addClass('error');
                    $('[id*="' + errorObject[k] + '"]:not(:hidden)').parent().addClass('highlight error');

                }
            }
        }
            <?php }
        }?>
    });
</script>
