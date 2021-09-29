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
        $address_targets = $address_targets + array('OTHER' => 'Other');
    $is_editable = isset($is_editable) ? $is_editable : true;
        $is_editable_contact_name = isset($is_editable_contact_name) ? $is_editable_contact_name : true;
        $is_editable_contact_targets = isset($is_editable_contact_targets) ? $is_editable_contact_targets : true;
    $option_styles = isset($option_styles) ? $option_styles : [];
    $contact_types = isset($contact_types) ? $contact_types : Document::getContactTypes();
foreach ($address_targets as $key=>$value) {
    if (strpos($key, 'Gp') !== false) {
        $contact_types['GP'] = substr($value, strpos($value, '(')+1, -1);
        break;
    }
}
    echo \CHtml::dropDownList(
        '',
        null,
        $address_targets,
        array(
                'empty' => '- Recipient -',
                'nowrapper' => true,
                'class' => 'full-width docman_recipient cols-full',
                'data-rowindex' => $row_index,
                'data-previous' => $contact_id,
                'data-name' => 'DocumentTarget['.$row_index.'][attributes][contact_id]',
                'id' => 'docman_recipient_' . $row_index,
                'disabled' => !$is_editable_contact_targets,
                'style' => (!$is_editable_contact_targets ? 'background-color: lightgray' : ''),
            )
    );
        echo CHtml::hiddenField('DocumentTarget['.$row_index.'][attributes][contact_nickname]', (isset($contact_nickname) ? $contact_nickname : ''));
        echo \CHtml::textField('DocumentTarget['.$row_index.'][attributes][contact_name]', $contact_name, array('readonly' => !$is_editable_contact_name, 'class' => 'cols-full', 'placeholder' => 'Name'));
    echo CHtml::dropDownList(
        'DocumentTarget['.$row_index.'][attributes][contact_type]',
        $contact_type,
        $contact_types,
        [       'empty' => '- Type -',
                'nowrapper' => true,
                'class' => 'full-width docman_contact_type',
                'data-rowindex' => $row_index,
                'options' => $option_styles,
                'disabled' => !$is_editable
        ]
    );

    if (!$is_editable) {
        echo CHtml::hiddenField('DocumentTarget['.$row_index.'][attributes][contact_type]', $contact_type, array(
            'id' => 'yDocumentTarget_'.$row_index.'_attributes_contact_type'));
    }



