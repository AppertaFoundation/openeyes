    <h2>Effective use of resources (EUR) Report</h2>
    <div>
    <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'module-report-form',
            'enableAjaxValidation' => false,
            'layoutColumns' => array('label' => 2, 'field' => 10),
            'action' => Yii::app()->createUrl('/' . $this->module->id . '/report/downloadReport'),
        )) ?>
    <input type="hidden" name="report-name" value="EUR"/>
    <table class="standard cols-full">
        <tbody>
            <tr class="col-gap">
                <td>
                    <?=\CHtml::label('Choose Consultant', 'consultant_id') ?>
                </td>
                <td>
                    <?=\CHtml::dropDownList('consultant_id', null, $consultant, array('empty' => 'All surgeons')) ?>
                </td>
            </tr>
            <tr class="col-gap">
                <td>Date Range</td>
                <td>
                    <input id="date_from"
                        placeholder="From"
                        class="start-date"
                        name="date_from"
                        autocomplete="off"
                        value= <?= @$_GET['date_from']; ?>
                    >
                </td>
                <td>
                    <input id="date_to"
                        placeholder="To"
                        class="end-date"
                        name="date_to"
                        autocomplete="off"
                        value= <?= @$_GET['date_to']; ?>
                    >
                </td>
            </tr>
            <tr>

        </tbody>
    </table>
    <div class="js-report-summary report-summary" style="display: none; overflow-y:scroll"></div>
    <?php $this->endWidget() ?>
    <table class="standard cols-full">
        <tbody>
        <tr>
            <td>
            <div class="row flex-layout flex-right">
                <button type="submit" class="button green hint display-module-report" name="run"><span
                    class="button-span button-span-blue">Display report</span></button>
                &nbsp;
                <button type="submit" class="button green hint download-module-report" name="run"><span
                    class="button-span button-span-blue">Download report</span></button>
                <i class="spinner loader" style="display: none;"></i>
            </div>
            </td>
        </tr>
        </tbody>
    </table>
    </div>