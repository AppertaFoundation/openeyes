<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php

    $is_editable = isset($is_editable) ? $is_editable : true;
    $option_styles = isset($option_styles) ? $option_styles : [];

    $contact_types = isset($contact_types) ? $contact_types : Document::getContactTypes();

    echo CHtml::dropDownList('DocumentTarget['.$row_index.'][attributes][contact_type]', $contact_type, $contact_types,
        [       'empty' => '- Type -',
                'nowrapper' => true,
                'class' => 'full-width docman_contact_type',
                'data-rowindex' => $row_index,
                'options' => $option_styles,
                'disabled' => !$is_editable,
                'style' => !$is_editable ? 'background-color:lightgray' : ''
        ]
    );

    if(!$is_editable){
        echo CHtml::hiddenField('DocumentTarget['.$row_index.'][attributes][contact_type]', $contact_type, array(
            'id' => 'yDocumentTarget_'.$row_index.'_attributes_contact_type')
        );
    }


    ?>
