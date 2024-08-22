<?php
/**
 * @var $pathway_type PathwayType
 * @var $source_pathway_type PathwayType
 * @var $errors array
 */
$label = null;

if (!isset($source_pathway_type)) {
    $source_pathway_type = null;
}

if ($pathway_type->isNewRecord) {
    $label = 'Create';
    if ($source_pathway_type) {
        $label = 'Duplicate';
    }
} else {
    $label = 'Edit';
}
?>
<div class="admin box">
    <h2><?= $pathway_type->isNewRecord ? 'Create' : 'Edit'?> Pathway Preset</h2>
    <?= $source_pathway_type ? "<p>Duplicating $source_pathway_type->name</p>" : null ?>

    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors))?>
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'pathway-type-form',
        'enableAjaxValidation' => false,
        'focus' => '#PathwayType_name',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ))?>

    <div class="cols-8">
        <table class="standard cols-full">
            <colgroup>
                <col class="cols-2">
                <col class="cols-8">
            </colgroup>
            <tbody>
            <tr>
                <td>Name</td>
                <td>
                    <?=\CHtml::activeTextField(
                        $pathway_type,
                        'name',
                        [
                            'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                            'class' => 'cols-full',
                            'field' => 2,
                            'value' => $source_pathway_type->name ?? $pathway_type->name ?? '',
                        ]
                    ) ?>
                </td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="5">
                    <?=\CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large',
                            'name' => 'save',
                            'id' => 'et_save'
                        ]
                    ); ?>
                    <?=\CHtml::submitButton(
                        'Cancel',
                        [
                            'data-uri' => '/Admin/worklist/presetPathways',
                            'class' => 'button large',
                            'name' => 'cancel',
                            'id' => 'et_cancel'
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
    <?php $this->endWidget()?>
</div>
