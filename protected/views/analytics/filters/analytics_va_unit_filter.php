<?php
$va_unit_list = array();
$default_va_unit = strtolower($default_va_unit);
foreach ($va_units as $key => $value) {
    $option = array(
        'label' => $value['name'],
        'id' => $value['id'],
    );
    if(strtolower($key) === $default_va_unit){
        $option['defaultSelected'] = true;
    }
    $va_unit_list[] = $option;
}

$default_va_name = null;
$default_va_id = null;
if(isset($va_units[$default_va_unit])){
    $default_va_name = $va_units[$default_va_unit]['name'];
    $default_va_id = $va_units[$default_va_unit]['id'];
}
?>
<tr class="custom-filter">
    <td>VA Units</td>
    <td data-name="analytics_va_unit">
        <span class="selected-filter" data-label="<?=$default_va_name?>" data-id="<?=$default_va_id?>"><?=$default_va_name?></span>
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