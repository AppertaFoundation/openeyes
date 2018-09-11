<?php foreach ($field_options as $field) : ?>
    <tr>
        <td><?= $page->getAttributeLabel($field); ?></td>
        <td>
            <?= CHtml::activeTextField($page, $field, [
                'autocomplete' => Yii::app()->params['html_autocomplete'],
                'class' => 'cols-full'
            ]); ?>
        </td>
    </tr>
<?php endforeach; ?>
