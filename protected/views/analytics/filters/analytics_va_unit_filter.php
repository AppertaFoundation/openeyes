<?php
    $va_unit_list = array();
foreach ($va_units as $key => $value) {
    if ($key === $default_va_unit) {
        $va_unit_list[] = array(
            'label' => $key,
            'id' => $value,
            'defaultSelected' => true,
        );
    } else {
        $va_unit_list[] = array(
            'label' => $key,
            'id' => $value,
        );
    }
}
?>
<tr class="custom-filter">
    <td>VA Units</td>
    <td data-name="analytics_va_unit">
        <span class="selected-filter" data-label="<?=$default_va_unit?>" data-id="<?=$va_units[$default_va_unit]?>"><?=$default_va_unit?></span>
    </td>
    <td>
        <button class="button hint green thin js-add-select-btn" id="show-analytics-filter-va-units" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </td>
</tr>
<script>
    $(function(){
        const va_unit_list = <?= json_encode($va_unit_list);?>;

        new OpenEyes.UI.AdderDialog({
            openButton: $('#show-analytics-filter-va-units'),
            source: 'sidebar',
            parentContainer: 'body',
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(va_unit_list)],
            onReturn: function(adderDialog, selectedItems){
                const va_unit_selection = $('td[data-name="analytics_va_unit"] span');
                va_unit_selection.attr('data-label', selectedItems[0].label);
                va_unit_selection.attr('data-id', selectedItems[0].id);
                va_unit_selection.text(selectedItems[0].label);
                return true;
            }
        });
    })
</script>