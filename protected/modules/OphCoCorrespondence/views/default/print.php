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
?>
<?php 
/*
 * 
Trying to set the background img to the wkhtmltopdf
file:///var/www/openeyes/protected/modules/OphCoCorrespondence/assets/img/bg_draft.png
file:///home/wprince/web/v2_rails4/app/assets/images/test2.jpg');

style="background-image:url(file://<?php echo Yii::getPathOfAlias('application.modules.OphCoCorrespondence.assets.img') . '/bg_draft.png'; ?>);"
 * <?php echo 'file://' . Yii::getPathOfAlias('application.modules.OphCoCorrespondence.assets.img') . '/bg_draft.png'; ?>
*/
http://wkhtmltopdf.org/usage/wkhtmltopdf.txt
    
?>
<div class="print-letter-div" >

    <?php if($element->draft && Yii::app()->params['OphCoCorrespondence_printout_draft_background'] == true) :?>
        <img style="position: absolute; z-index: -1000;width:100%" src="<?php echo Yii::app()->assetManager->createUrl('img/bg_draft.png', 'application.modules.OphCoCorrespondence.assets') ?>" />
    <?php endif; ?>
    <?php $letter_address = isset($letter_address) ? $letter_address : null; ?>
    <?php echo $this->renderPartial('print_ElementLetter', array('element' => $element, 'letter_address' => $letter_address))?>
</div>
