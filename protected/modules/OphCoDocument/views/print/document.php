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
    if (!isset($id) && isset($this->event_type)) {
        $id = $this->event_type->class_name.'_print';
    }
?>

<?php $this->renderPartial('//print/event_header'); ?>

<?php foreach($this->getElements() as $element):  ?>
    <b><?php if($element->sub_type) echo $element->sub_type->name; ?></b>
    <?php if($element->single_document): ?>
        <?php $this->renderPartial('/default/print_'.$this->getTemplateForMimeType($element->single_document->mimetype), array('element'=>$element, 'index'=>'single_document')); ?>
    <?php endif; ?>
    <?php if(($element->right_document_id) || ($element->left_document_id)): ?>
        <table border="0">
            <thead>
                <tr>
                    <th width="50%">
                        <?php if($element->right_document_id): ?>Right side<?php endif; ?>
                    </th>
                    <th width="50%">
                        <?php if($element->left_document_id): ?>Left side<?php endif; ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td width="50%">
                        <?php if($element->right_document_id): ?>
                            <?php $this->renderPartial('/default/print_'.$this->getTemplateForMimeType($element->right_document->mimetype), array('element'=>$element, 'index'=>'right_document')); ?>
                        <?php endif; ?>
                    </td>
                    <td width="50%">
                        <?php if($element->left_document_id): ?>
                            <?php $this->renderPartial('/default/print_'.$this->getTemplateForMimeType($element->left_document->mimetype), array('element'=>$element, 'index'=>'left_document')); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
    <?php if($element->comment) echo "<br/><b>Comments: </b><br>".nl2br($element->comment); ?>
<?php endforeach; ?>
