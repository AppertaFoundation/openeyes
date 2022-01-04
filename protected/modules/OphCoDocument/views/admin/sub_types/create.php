<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

    <h2>Create sub type</h2>

<?php echo $this->renderPartial('/admin/sub_types/_form_errors', array('errors' => $errors)) ?>

<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'subtypeform',
    'enableAjaxValidation' => false,
    'focus' => '#username',
    'layoutColumns' => array(
        'label' => 2,
        'field' => 5,
    ),
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
)) ?>

<?php $this->renderPartial(
    '/admin/sub_types/_form',
    [
        'model' => $model,
        'form' => $form,
    ]
); ?>

<?php $this->endWidget() ?>