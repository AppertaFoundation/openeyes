<h2>Drug Administration Report</h2>
<div>
    <?php
        $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'module-report-form',
            'enableAjaxValidation' => false,
            'layoutColumns' => array('label' => 2, 'field' => 10),
            'action' => Yii::app()->createUrl('/' . $this->module->id . '/report/downloadReport'),
        )) ?>
    <input type="hidden" name="report-name" value="Da"/>
    <table class="standard cols-full">
        <tbody>
            <tr class="col-gap">
                <td>
                    Date Type
                    <i
                        class="oe-i info pad small js-has-tooltip"
                        data-tooltip-content="If date type is set to none, the date range below will be IGNORED"
                    ></i>
                </td>
                <td>
                    <select name="date_type">
                        <option value="0">None</option>
                        <option value="assignment">Assignment</option>
                        <option value="appointment">Appointment</option>
                        <option value="administration">Administration</option>
                    </select>
                </td>
                <td>Preset Name</td>
                <td>
                    <input type="text" name="preset_name">
                </td>
            </tr>
            <tr class="col-gap">
                <td>Date From</td>
                <td>
                    <input id="date_from"
                        placeholder="From"
                        class="start-date"
                        name="date_from"
                        autocomplete="off"
                        value=""
                    >
                </td>
                <td>Date To</td>
                <td>
                    <input id="date_to"
                        placeholder="To"
                        class="end-date"
                        name="date_to"
                        autocomplete="off"
                        value=""
                    >
                </td>
            </tr>
            <tr class="col-gap">
                <td>
                    Type
                </td>
                <td>
                    <input id="type-psd" type="radio" name="type" value="assigned" checked>
                    <label for="type-psd">
                        Assigned
                    </label>
                    <input id="type-direct" type="radio" name="type" value="0">
                    <label for="type-direct">
                        Direct Administration
                    </label>
                    <input id="type-all" type="radio" name="type" value="all">
                    <label for="type-all">
                        All
                    </label>
                </td>
                <td>Medication Name</td>
                <td>
                    <input type="text" name="med_name">
                </td>
            </tr>
            <tr class="col-gap">
                <td>
                    Patient Identifier
                </td>
                <td>
                    <input type="text" name="pi_value">
                </td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
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
    <div class="js-report-summary report-summary" style="display: none; overflow-y:scroll"></div>
</div>
<script>
    pickmeup(".start-date", {
        format: "d-m-Y",
        hide_on_select: true,
        default_date: false,
    });
    pickmeup(".end-date", {
        format: "d-m-Y",
        hide_on_select: true,
        default_date: false,
    });
</script>