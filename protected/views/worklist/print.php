<?php
/**
 * @var Worklist[] $worklists
 */

$firm_id = Yii::app()->session['selected_firm_id'];
$firm = Firm::model()->findByPk($firm_id);

$site_id = Yii::app()->session['selected_site_id'];
$site = Site::model()->findByPk($site_id);

$date_from = Yii::app()->request->getParam('date_from');
$date_to = Yii::app()->request->getParam('date_to');

if ($date_from === '' && $date_to === '') {
    $date_range = "All dates";
} else {
    $date_range = "$date_from - $date_to";
}

$logo_helper = new LogoHelper();

if (empty($filter)) {
    $filter = new WorklistFilterQuery();
}

?>

<header class="print-header">
    <div class="logo">
        <?= $logo_helper->render() ?>
    </div>
</header>

<h1 class="print-title">Worklists</h1>

<main class="print-main">
    <table class="borders">
        <tbody>
        <tr>
            <th>Site</th>
            <td><?= CHtml::encode($site->name); ?></td>
        </tr>
        <tr>
            <th><?= Firm::contextLabel(); ?></th>
            <td><?= CHtml::encode($firm->name); ?></td>
        </tr>
        <tr>
            <th>Date range</th>
            <td><?= CHtml::encode($date_range); ?></td>
        </tr>
        </tbody>
    </table>

    <?php
    if ($filter->getCombineWorklistsStatus()) {
        echo $this->renderPartial('_worklist', array('worklist' => $worklists, 'is_printing' => true, 'filter' => $filter));
    } else {
        foreach ($worklists as $worklist) {
            echo $this->renderPartial('_worklist', array('worklist' => $worklist, 'is_printing' => true, 'filter' => $filter));
        }
    }
    ?>
</main>
