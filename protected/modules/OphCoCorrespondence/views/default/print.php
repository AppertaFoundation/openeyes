<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 *  You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="print-letter-div" >

    <?php if ($element->draft && Yii::app()->params['OphCoCorrespondence_printout_draft_background'] == true) :?>
        <img style="position: absolute; z-index: -1000;width:100%" src="<?php echo Yii::app()->assetManager->createUrl('img/bg_draft.png', 'application.modules.OphCoCorrespondence.assets') ?>" />
    <?php endif; ?>
    <?php $letter_address = isset($letter_address) ? $letter_address : null; ?>
    <?php echo $this->renderPartial('print_ElementLetter', array(
        'letter_header' => $letter_header,
        'element' => $element,
        'letter_address' => $letter_address,
        'contact_type' => $contact_type ?: null)) ?>
</div>
