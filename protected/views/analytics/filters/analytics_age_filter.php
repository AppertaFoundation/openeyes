<tr class="custom-filter vf-filter">
    <td>Ages</td>
    <td data-name="analytics_age">
        <span>All Ages</span>
    </td>
    <td>
        <button class="button hint green thin js-add-select-btn" id="show-analytics-filter-age" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </td>
</tr>
<script>
    $(function(){
        const age_category = [
            {
                'label': 'All',
                'type': 'category',
                'target': 'all',
                'defaultSelected': true
            },
            {
                'label': 'Range',
                'type': 'category',
                'target': 'range',
            }
        ]
        new OpenEyes.UI.AdderDialog({
            openButton: $('#show-analytics-filter-age'),
            deselectOnReturn: false,
            source: 'sidebar',
            parentContainer: 'body',
            itemSets:[
                new OpenEyes.UI.AdderDialog.ItemSet(age_category, {
                    'header': 'Ages',
                    'mandatory': true,
                    'id': 'category'
                }),
                new OpenEyes.UI.AdderDialog.ItemSet([], {
                    'header': 'Youngest',
                    'id': 'min',
                    'hideByDefault': true,
                    'splitIntegerNumberColumnsTypes': ['100', '10', '1'],
                    'splitIntegerNumberColumns': [{'min': 0, 'max': 1}, {'min': 0, 'max': 9}, {'min': 0, 'max': 9}],
                }),
                new OpenEyes.UI.AdderDialog.ItemSet([], {
                    'header': 'Oldest',
                    'id': 'max',
                    'hideByDefault': true,
                    'splitIntegerNumberColumnsTypes': ['100', '10', '1'],
                    'splitIntegerNumberColumns': [{'min': 0, 'max': 1}, {'min': 0, 'max': 9}, {'min': 0, 'max': 9}],
                }),
            ],
            onSelect: function(e){
                const selected = $(e.target)
                const $item = selected.is("span") ? $(e.target).closest("li") : $(e.target);
                const $tr = $item.closest("tr");
                const $all_options = $tr.children("td:eq(1), td:eq(2)");
                if($item.data('target') === 'all'){
                    $all_options.hide();
                } else {
                    $all_options.show();
                }
            },
            onReturn: function(adderDialog, selectedItems, selectedAdditions){
                let max = 0;
                let min = 0;
                let selected_category = '';
                selectedItems.forEach(function(item, i){
                    if(item.target){
                        selected_category = item.target
                    }
                    if(item.target === 'all'){
                        const all_span = document.createElement('span');
                        all_span.innerText = 'All Ages';
                        $('td[data-name="analytics_age"]').html(all_span)
                    } else {
                        if(!isNaN(item.min)){
                            min += item.min * item.type;
                        } else if(!isNaN(item.max)){
                            max += item.max * item.type;
                        }
                    }
                });
                // swap max and min if max is smaller than min
                if(selected_category === 'range'){
                    if(max < min){
                        [max, min] = [min, max];
                    }
                    const min_span = `<span data-id="${min}" data-label="${min}">${min}</span>`;
                    const max_span = `<span data-id="${max}" data-label="${max}">${max}</span>`;
    
                    $('td[data-name="analytics_age"]').html(`${min_span} to ${max_span}`);
                }

                return true;
            }
        })
    })
</script>