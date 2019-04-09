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
$cross_section_ed = null;

if ($element->isNewRecord || $element->{$side . '_eyedraw2'} || $element->has_side_view) {
    // only display the cross section eyedraw for elements created after it was introduced
    // legacy records will not have the the eyedraw2 property
    // Having checked it though, we set the value to null, so that it's contents are driven
    // by the stored data in the core eyedraw attribute.
    $element->{$side . '_eyedraw2'} = null;

    $cross_section_ed = $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
        'listenerArray' => array('anteriorSegmentListener'),
        'idSuffix' => $side . '_' . $element->elementType->id . '_side',
        'side' => ($side == 'right') ? 'R' : 'L',
        'mode' => 'edit',
        'width' => 198,
        'height' => 300,
        'model' => $element,
        'attribute' => $side . '_eyedraw2',
        'offsetX' => 10,
        'offsetY' => 10,
        'toolbar' => false,
        'showDrawingControls' => false,
        'showDoodlePopup' => true,
        'showDoodlePopupForDoodles' => array('CorneaCrossSection'),
        'popupDisplaySide' => 'left',
        'template' => 'OEEyeDrawWidget_InlineToolbar',
    ), true);
}

$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
    'doodleToolBarArray' => array(
        array('Lens', 'PCIOL', 'ToricPCIOL', 'Bleb', 'PI', 'Fuchs', 'CornealOedema', 'PosteriorCapsule', 'CornealPigmentation',
            'TransilluminationDefect', 'Hypopyon', 'Hyphaema', 'CornealScar', 'Rubeosis', 'SectorIridectomy', 'ACIOL',
            'LasikFlap', 'CornealSuture', 'ConjunctivalSuture', 'TrabySuture', 'DendriticUlcer','AdenoviralKeratitis',
            'CornealLaceration', 'MarginalKeratitis', 'MetallicForeignBody', 'Pingueculum', 'Pterygium' , 'CapsularTensionRing'),
        array('SPEE', 'CornealEpithelialDefect', 'CornealOpacity', 'Conjunctivitis', 'PosteriorSynechia',
            'KeraticPrecipitates', 'Episcleritis', 'TrabyFlap', 'Tube', 'TubeExtender', 'Supramid', 'TubeLigation',
            'Patch', 'SidePort', 'RK', 'CornealGraft', 'EndothelialKeratoplasty', 'CornealSuture', 'ContinuousCornealSuture', 'CornealThinning', 'PeripheralVascularisation')
    ),
    'listenerArray' => array('anteriorSegmentListener', 'OphCiExamination_Gonioscopy_AnteriorSegment_Listener', 'autoReportListener'),
    'idSuffix' => $side.'_'.$element->elementType->id,
    'side' => ($side == 'right') ? 'R' : 'L',
    'mode' => 'edit',
    'width' => 300,
    'height' => 300,
    'model' => $element,
    'attribute' => $side.'_eyedraw',
    'maxToolbarButtons' => 7,
    'template' => 'OEEyeDrawWidget_InlineToolbar',
    'toggleScale' => 0.72,
    'popupDisplaySide' => 'right',
    'autoReport' => 'OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_'.$side.'_ed_report',
    'autoReportEditable' => false,
    'fields' => $cross_section_ed
));

?>
