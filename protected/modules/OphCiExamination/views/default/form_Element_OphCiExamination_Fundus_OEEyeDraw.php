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
    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
        'doodleToolBarArray' => array(
            array('RRD', 'ChoroidalNaevusMelanoma', 'ChoroidalEffusion', 'UTear', 'RoundHole', 'Dialysis', 'GRT', 'StarFold', 'AntPVR', 'Lattice', 'Cryo', 'LaserCircle'),
            array('DrainageRetinotomy', 'Retinoschisis', 'OuterLeafBreak', 'InnerLeafBreak', 'Fovea', 'Freehand'),
        ),
        'onReadyCommandArray' => array(
            array('addDoodle', array('Fundus')),
            array('deselectDoodles', array()),
        ),
        'listenerArray' => array('autoReportListener'),
        'idSuffix' => $side.'_'.$element->elementType->id,
        'side' => ($side == 'right') ? 'R' : 'L',
        'mode' => 'edit',
        'model' => $element,
        'attribute' => $side.'_eyedraw',
        'template' => 'OEEyeDrawWidget_InlineToolbar',
        'maxToolbarButtons' => 12,
        'autoReport' => CHtml::modelName($element) . '_'.$side.'_ed_report',// 'OEModule_OphCiExamination_models_Element_OphCiExamination_Fundus_'.$side.'_ed_report',
        'autoReportEditable' => false,
        'fields' => $this->renderPartial($element->form_view.'_OEEyeDraw_fields', array(
            'form' => $form,
            'side' => $side,
            'element' => $element,
        ), true),
    ));

