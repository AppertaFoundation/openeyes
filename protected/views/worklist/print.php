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

<style>
  @media print {
    @page {
      /* size: landscape; */
      width: 100%;
    }

    #title {
      text-align: center;
      font-weight: bold;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
    }

    .oec-patients, .oec-group {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .oec-patients th,
    .oec-patients td {
      text-align: left;
      padding: 3px 10px;
      border: 1px double black;
    }

    .oec-patients tr:not(:last-child) {
      border-bottom: 1px solid black;
    }

    .oec-patients th:first-child,
    .oec-patients td:first-child {
      padding-left: 10px;
    }

    .oec-patients th:last-child,
    .oec-patients td:last-child {
      padding-right: 10px;
      text-align: right;
    }

    .label {
      font-weight: 600;
    }
  }
</style>

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
