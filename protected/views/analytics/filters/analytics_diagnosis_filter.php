<?php
    $disorders = isset($common_disorders) ?
        array_map(function ($disorder) {
                return [
                    'label' =>$disorder['term'],
                    'id' => $disorder['id'],
                    'filter_value' => $disorder['id'],
                    'is_filter' => true,
                    'category' => 'all'
                ];
        }, $common_disorders) : [];
    $disorders = json_encode($disorders);
    $all_diagnosis_txt = 'All Diagnosis';
    ?>

<tr class="service-filter custom-filter vf-filter">
    <td>Diagnosis</td>
    <td data-name="analytics_diagnosis">
        <span
        data-label="<?= $all_diagnosis_txt ?>"
        data-id="none"
        ><?= $all_diagnosis_txt ?></span>
    </td>
    <td>
        <button class="button hint green thin js-add-select-btn" id="show-analytics-filter-diagnosis" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </td>
</tr>

<script>
    $(function(){
        let disorder_list = <?= $disorders ?>;

        disorder_list.unshift({
            'label': '<?= $all_diagnosis_txt ?>',
            'id': 'none',
            'category': 'all',
            'defaultSelected': true
        })
        const disorder_category = [
            {
                'label': 'All',
                'type': 'category',
                'target': 'all',
                'defaultSelected': true
            },
            {
                'label': 'Selected',
                'type': 'category',
                'target': 'selected',
            }
        ]
        new OpenEyes.UI.AdderDialog({
            openButton: $('#show-analytics-filter-diagnosis'),
            source: 'sidebar',
            parentContainer: 'body',
            resetSelectionToDefaultOnReturn: false,
            deselectOnReturn: false,
            itemSets: [
            new OpenEyes.UI.AdderDialog.ItemSet(disorder_category, 
            {
                'id': 1, 
            }),
            new OpenEyes.UI.AdderDialog.ItemSet(
                disorder_list,
                {
                    'multiSelect': true,
                    'id': 2
                }
            ), ],
            searchOptions: {
                searchSource: '/disorder/autocomplete'
            },
            onOpen: function(adderDialog){
                // first click on adder popup button
                if(!adderDialog.$tr.find('li[data-type="category"]').hasClass('selected')){
                    adderDialog.$tr.find('li[data-type="category"][data-target="all"]').addClass('selected');
                }
            },
            onSelect: function(e){
                const $selected = $(e.target)
                const $item = $selected.is("span") ? $(e.target).closest("li") : $(e.target);
                const $tr = $item.closest("tr");
                // $tr.children("td:eq(1)") is the second column of the adder popup
                const $all_options = $tr.children("td:eq(1)").find("ul.add-options li");
                const $relevant_options = $tr.children("td:eq(1)").find("ul.add-options li[data-category=" + $item.data('target') + "]");

                // select from search result and find the existing one in diagnosis list
                if($selected.closest('ul.js-search-results').length){
                    $tr.children("td:eq(1)").find("ul.add-options li[data-id='" + $item.data('id') + "']").trigger('click');
                }
                // switch category: All <-> Selected
                if($item.data('type') === 'category'){
                    $all_options.hide();
                    $relevant_options.show();
                } else {
                    // if 'All' is selected in 'All' category
                    if($selected.data('label') === '<?= $all_diagnosis_txt ?>' && $selected.data('id') === 'none'){
                        $all_options.not($selected).removeClass('selected');
                    } else {
                        $tr.children("td:eq(1)").find("ul.add-options li[data-label='<?= $all_diagnosis_txt ?>'][data-id='none']").removeClass('selected');
                    }
                }
            },
            onReturn: function (adderDialog, selectedItems, selectedAdditions) {
                const disorder_filter_ctn = $('td[data-name="analytics_diagnosis"]');
                const $tr = adderDialog.$tr;
                disorder_filter_ctn.html('');
                selectedItems.forEach(function(item, i){
                    const sperator = item.label === '<?= $all_diagnosis_txt ?>' ? '' : ', ';
                    if(item.type !== 'category' && disorder_filter_ctn.find(`span[data-id='${item.id}']`).length === 0){
                        const disorder_span = document.createElement('span');
                        $(disorder_span).attr({
                            'data-id': item.id,
                            'data-label': item.label
                        }).text(item.label + sperator);
                        disorder_filter_ctn.append(disorder_span);
                    }
                });

                // categorise everything into all section
                $tr.children("td:eq(1)").find('li').attr('data-category', 'all');
                // categorise selected li to selected section, except it is the li with 'All'
                $tr.children("td:eq(1)").find('li.selected[data-id!="none"][data-label!="All"]').attr('data-category', 'selected');
                const selected_category = $tr.children("td:eq(0)").find('li.selected').data('target');
                const $all_options = $tr.children("td:eq(1)").find("ul.add-options li");
                const $relevant_options = $tr.children("td:eq(1)").find("ul.add-options li[data-category=" + selected_category + "]");
                $all_options.hide();
                $relevant_options.show();
                return true;
            }
        })
    })
</script>