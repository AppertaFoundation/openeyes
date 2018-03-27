<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<div class="box reports">
    <div class="report-fields">
        <h2>Ready for second eye (unbooked) Report</h2>
        <?php
        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'module-report-form',
            'enableAjaxValidation' => false,
            'layoutColumns' => array('label' => 2, 'field' => 10),
            'action' => Yii::app()->createUrl('/'.$this->module->id.'/report/downloadReport'),
        ));
        ?>

        <input type="hidden" name="report-name" value="ReadyForSecondEyeUnbooked" />

        <?php $this->endWidget(); ?>
    </div>
    <div class="errors alert-box alert with-icon" style="display: none">
        <p>Please fix the following input errors:</p>
        <ul>
        </ul>
    </div>
    <div class="row field-row">
        <div class="large-6 column end">
            <button type="submit" class="classy blue mini display-module-report" name="run"><span class="button-span button-span-blue">Display report</span></button>
            <button type="submit" class="classy blue mini download-module-report" name="run"><span class="button-span button-span-blue">Download report</span></button>
            <img class="loader" style="display: none;" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." />&nbsp;
        </div>
    </div>
    <div class="reportSummary report curvybox white blueborder" style="display: none;">
    </div>
</div>
