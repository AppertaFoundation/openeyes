<main class="oe-home">
    <div class="oe-full-header flex-layout">
        <div class="title wordcaps">Import Preview</div>
    </div>
    <div class="oe-full-content">
        <div class="errorSummary">
            <?php
            if (isset($errors) and $errors !== null) {
                echo '<pre>';
                echo ArrayHelper::array_dump_html($errors);
                echo '</pre>';
            }
            ?>
        </div>
        <?php $form = $this->beginWidget(
            'CActiveForm',
            array(
                'id' => 'import-form',
                'action' => Yii::app()->createURL('csv/import', array('context' => $context, 'csv' => $csv_id)),
                'enableAjaxValidation' => false,
                'htmlOptions' => array('enctype' => 'multipart/form-data',
                'class' => 'oe-full-main',
                 ),
            )
        );

        if(empty($csv_id)) {
        	?>
					<div id="import-file-upload-error"> No CSV file found. Please select a file to upload </div>
					<?php
				}

        if (!empty($table) && !empty($csv_id)): ?>
            <div style="overflow: auto">
                <table class="standard highlight-rows">
                    <tr>
                        <?php foreach (array_keys($table[0]) as $header): ?>
                            <th>
                                <?php echo $header; ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                    <?php foreach ($table as $row): ?>
                        <tr>
                            <?php foreach ($row as $column): ?>
                                <td>
                                    <?php echo $column ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php
        echo CHtml::submitButton('Import');
				endif;
        $this->endWidget();
        ?>
    </div>
</main>
