<tr class="custom-filter">
    <td>Time Interval</td>
    <td data-name="analytics_time_interval">
        <span data-label="1" data-type="num">1 </span>
        <span data-label="Week" data-type="unit">Week</span>
    </td>
    <td>
        <button class="button hint green thin js-add-select-btn" id="show-analytics-filter-timeinterval" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </td>
</tr>

<script>
    $(function(){
        const nums = [1, 2, 3, 4].map(function(item, i){
            const ret = {
                'label': item,
                'type': 'num',
            };
            if(item === 1){
                ret['defaultSelected'] = true;
            }
            return ret;   
        });        
        const units = ['Week', 'Month'].map(function(item, i){
            const ret = {
                'label': item,
                'type': 'unit',
            } 
            if(item === 'Week'){
                ret['defaultSelected'] = true;
            }
            return ret;
        });
        new OpenEyes.UI.AdderDialog({
            openButton: $('#show-analytics-filter-timeinterval'),
            source: 'sidebar',
            parentContainer: 'body',
            itemSets: [
            new OpenEyes.UI.AdderDialog.ItemSet(
                nums, 
                {
                    'id': 1, 
                }
            ),
            new OpenEyes.UI.AdderDialog.ItemSet(
                units,
                {
                    'id': 2
                }
            ), ],
            onReturn: function (adderDialog, selectedItems, selectedAdditions) {
                selectedItems.forEach(function(item, i){
                    $(`td[data-name="analytics_time_interval"] span[data-type="${item.type}"]`)
                    .attr('data-label', item.label)
                    .text(item.label)
                })
                return true;
            }
        })
    })
</script>