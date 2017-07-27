<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php
$cross_section_ed = null;

if ($element->isNewRecord || $element->{$side . '_eyedraw2'}) {
    // only display the cross section eyedraw for elements created after it was introduced
    // legacy records will not have the the eyedraw2 property
    $cross_section_ed = $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
        'onReadyCommandArray' => array(
            array('deselectDoodles', array()),
        ),
        'listenerArray' => array('anteriorSegmentListener'),
        'syncArray' => array(
            $side.'_'.$element->elementType->id => array(
                'AntSegCrossSection' => array('AntSeg' => array('parameters' => array('apexY') ) ),
                'LensCrossSection' => array('Lens' => array('parameters' => array('originY') ) ),
                'ACIOLCrossSection' => array('ACIOL' => array('parameters' => array('originY') ) ),
                'PCIOLCrossSection' => array('PCIOL' => array('parameters' => array('originY', 'fx') ) ),
                // no controls for corneal opacity in cross section so don't need to sync back to primary
                //'CornealOpacityCrossSection' => array('CornealOpacity' => array('parameters' => array('yMidPoint', 'd', 'h', 'w', 'iW') ) )
            ),
        ),
        'idSuffix' => $side.'_'.$element->elementType->id . '_side',
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
        array('Lens', 'PCIOL', 'Bleb', 'PI', 'Fuchs', 'CornealOedema', 'PosteriorCapsule', 'CornealPigmentation',
            'TransilluminationDefect', 'Hypopyon', 'Hyphaema', 'CornealScar', 'Rubeosis', 'SectorIridectomy', 'ACIOL',
            'LasikFlap', 'CornealSuture', 'ConjunctivalSuture', 'TrabySuture', 'DendriticUlcer','AdenoviralKeratitis',
            'CornealLaceration', 'MarginalKeratitis', 'MetallicForeignBody', 'Pingueculum', 'Pterygium'),
        array('SPEE', 'CornealEpithelialDefect', 'CornealOpacity', 'Conjunctivitis', 'PosteriorSynechia',
            'KeraticPrecipitates', 'Episcleritis', 'TrabyFlap', 'Tube', 'TubeExtender', 'Supramid', 'TubeLigation',
            'Patch', 'SidePort', 'RK',)
    ),
    'onReadyCommandArray' => array(
        array('addDoodle', array('AntSeg')),
        array('addDoodle', array('Lens')),
        array('addDoodle', array('Cornea')),
        array('deselectDoodles', array()),
    ),
    'listenerArray' => array('anteriorSegmentListener', 'autoReportListener'),
    'syncArray' => array(
        $side .'_'.$element->elementType->id . '_side' => array(
            'AntSeg' => array('AntSegCrossSection' => array('parameters' => array('apexY', 'colour') ) ),
            'Lens' => array('LensCrossSection' => array('parameters' => array('originY', 'nuclearGrade', 'corticalGrade', 'posteriorSubcapsularGrade', 'phakodonesis') ) ),
            'ACIOL' => array('ACIOLCrossSection' => array('parameters' => array('originY') ) ),
            'PCIOL' => array('PCIOLCrossSection' => array('parameters' => array('originY', 'fx') ) ),
            'CornealOpacity' => array('CornealOpacityCrossSection' => array('parameters' => array('yMidPoint','d','h','w','iW','originY','minY','maxY') ) ),
            'Hypopyon' => array('HypopyonCrossSection' => array('parameters' => array('apexY'))),
            'Hyphaema' => array('HyphaemaCrossSection' => array('parameters' => array('apexY')))
        )
    ),
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
    'popupDisplaySide' => 'left',
    'autoReport' => 'OEModule_OphCiExamination_models_Element_OphCiExamination_AnteriorSegment_'.$side.'_ed_report',
    'autoReportEditable' => false,
    'fields' => $cross_section_ed
));

?>
