<main class="oe-home">
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">Upload</div>
    </div>
    <div class="oe-full-content  flex-top">
        <button onclick="window.location.href='<?= isset($backuri) ? $backuri : '/OETrial/trial/' ?>'">
                Back to previous page
        </button>

        <div class="errorSummary">
            <?php
            if (isset($errors) and $errors !== null) {
                echo '<div class="alert-box warning">';
                echo Helper::array_dump_html($errors);
                echo '</div>';
            }
            ?>
        </div>

        <?php
        $form = $this->beginWidget(
            'CActiveForm',
            array(
                'id' => 'upload-form',
                'action' => Yii::app()->createURL('csv/preview', array('context' => $context)),
                'enableAjaxValidation' => false,
                'htmlOptions' => array('enctype' => 'multipart/form-data'),
            )
        );

        echo $form->fileField(new Csv(), 'csvFile');
        echo CHtml::submitButton('Submit');
        $this->endWidget();
        ?>
    </div>
</main>