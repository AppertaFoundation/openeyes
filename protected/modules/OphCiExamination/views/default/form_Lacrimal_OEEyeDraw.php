<?php

$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
    'doodleToolBarArray' => array(
        array('LidLaxity', 'Mucocele'),
    ),
    'onReadyCommandArray' => array(
        array('addDoodle', array('Lacrimal')),
        array('deselectDoodles', array()),
    ),
    'listenerArray' => array('autoReportListener'),
    'idSuffix' => $side . '_' . $element->elementType->id,
    'side' => ($side == 'right') ? 'R' : 'L',
    'mode' => 'edit',
    'model' => $element,
    'attribute' => $side . '_eyedraw',
    'template' => 'OEEyeDrawWidget_InlineToolbar',
    'maxToolbarButtons' => 12,
    'autoReport' => CHtml::modelName($element) . '_' . $side . '_ed_report',
    //'autoReportEditable' => false,
    'fields' => $this->renderPartial($element->form_view . '_OEEyeDraw_fields', array(
        'form' => $form,
        'side' => $side,
        'element' => $element,
    ), true),
));
