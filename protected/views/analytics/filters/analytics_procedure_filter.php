<?php
    $procedure_list = array();
foreach ($procedures as $key => $value) {
    $procedure_list[] = array(
        'label' => $key,
        'id' => $value,
        'hidden' => false,
    );
}
    $default_ids = implode(',', $procedures[$default_procedure]);
?>
<tr class="custom-filter vf-filter">
    <td>Procedure</td>
    <td data-name="analytics_procedure">
        <span
            data-label="All Procedures"
            data-id=""
        >All Procedures</span>
    </td>
    <td>
        <button class="button hint green thin js-add-select-btn" id="show-analytics-filter-procedure" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </td>
</tr>

<script>
    $(function(){
        let procedure_list = <?= json_encode($procedure_list);?>;
        const service_default_proc = {
            'label': 'All Procedures',
            'id': '',
            'defaultSelected': true,
        }
        procedure_list.unshift(service_default_proc);

        new OpenEyes.UI.AdderDialog({
            openButton: $('#show-analytics-filter-procedure'),
            source: 'sidebar',
            parentContainer: 'body',
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(procedure_list)],
            onReturn: function(adderDialog, selectedItems){
                const proc_selection = $('td[data-name="analytics_procedure"] span');
                proc_selection.attr('data-label', selectedItems[0].label);
                proc_selection.attr('data-id', selectedItems[0].id);
                proc_selection.text(selectedItems[0].label);
                return true;
            }
        });
    })
</script>