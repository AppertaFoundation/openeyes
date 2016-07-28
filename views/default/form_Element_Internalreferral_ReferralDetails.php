<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<div class="element-fields row">
	<?php echo $form->dropDownList($element, 'referrer_id', CHtml::listData(User::model()->findAll(array('order'=> 'last_name asc')),'id','last_name'),array('empty'=>'- Please select -'))?>
	<?php echo $form->dropDownList($element, 'from_subspecialty_id', CHtml::listData(Subspecialty::model()->findAll(array('order'=> 'name asc')),'id','name'),array('empty'=>'- Please select -'))?>
	<?php echo $form->dropDownList($element, 'to_subspecialty_id', CHtml::listData(Subspecialty::model()->findAll(array('order'=> 'name asc')),'id','name'),array('empty'=>'- Please select -'))?>
</div>

