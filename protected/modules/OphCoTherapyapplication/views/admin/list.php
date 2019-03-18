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
$this->renderPartial('_menu_main');
?>

<div class="cols-3">

<div class="row divider">
    <h2>
        <?php echo $title ? $title : 'Therapy Application Admin' ?>
    </h2>
</div>

    <?php $this->widget('zii.widgets.CListView', array(
        'dataProvider' => $dataProvider,
        'itemView' => 'list_'.$dataProvider->modelClass,
        'itemsCssClass' => 'standard',
        'itemsTagName' => 'table',
    )); ?>

<div class="box-actions">
    <a href="<?php echo Yii::app()->createUrl('OphCoTherapyapplication/admin/create'.$dataProvider->modelClass); ?>" class="button small">Add New</a>
</div>

</div>