<div class="cols-12">
    <div class="row divider">
        <h2><?php echo $title ?></h2>
    </div>
    
    <?php if (Yii::app()->user->hasFlash('success')) {?>
    <div id="flash-success" class="alert-box success">
        <?= Yii::app()->user->getFlash('success'); ?>
    </div>
    <?php } ?>
    <?php if (Yii::app()->user->hasFlash('info')) {?>
    <div id="flash-info" class="alert-box info">
        <?= Yii::app()->user->getFlash('info'); ?>
    </div>
    <?php } ?>
    <?php
        echo $this->renderPartial('//admin/_form_errors', array('errors' => $error_msg));
    ?>
    <?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#name',
        ]
    ) ?>
    <table class="standard generic-admin sortable valign-middle">
        <thead>
        <tr>
            <th>Name</th>
            <th>Alias</th>
            <th>Description</th>
        </tr>
        </thead>
        <colgroup>
            <col class="cols-2">
            <col class="cols-2">
            <col class="cols-7">
        </colgroup>
        <tbody>
        <?php foreach ($risk_status_entries as $risk_entry) { ?>
            <tr data-key="<?= $risk_entry->id ?>">
                <td>
                    <i class="oe-i triangle-<?= $risk_entry->getIndicatorColor();?> small"></i>
                    <?= $risk_entry->name;?>
                </td>
                <td>
                    <?= CHtml::activeTextField(
                        $risk_entry,
                        "[{$risk_entry->id}]attributes[alias]",
                        [
                            'maxlength' => '20'
                        ]
                    ) ?>
                </td>
                <td>
                    <?= CHtml::activeTextField(
                        $risk_entry,
                        "[{$risk_entry->id}]attributes[description]",
                        [
                            'class' => 'cols-full',
                            'maxlength' => '255'
                        ]
                    ) ?>
                </td>
            </tr>
        <?php } ?>
        </tbody>
        <tfoot class="pagination-container">
            <tr>
                <td colspan="3">
                    <?=\CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large primary event-action',
                            'name' => 'save',
                            'id' => 'et_admin-save',
                            'formmethod' => 'post',
                        ]
                    ); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<?php $this->endWidget() ?>