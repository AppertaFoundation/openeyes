<?php
$laterality_list = $laterality ?? null;
$aneathetic_row = $aneathetic_row ?? null;
// to avoid duplicated procedure entry
$proc_hidden_input_identifier = $proc_hidden_input_identifier ?? 'name$="[proc_id]"';
$trigger_benefits_risks_change = $trigger_benefits_risks_change ?? true;
$aneathetic_row = $aneathetic_row ?? null;
$is_extra_procedure = $is_extra_procedure ?? false;
?>
<script>
    $(function(){
        const is_extra_procedure = <?=json_encode($is_extra_procedure)?>;
        let $proc_table = $('<?= $entry_container ?>');
        const procedure_itemset_options = {
            'id': 'Procedures',
            'header': 'Procedures',
            'multiSelect': true,
        };
        const procedure_list = <?= json_encode($procedure_list) ?>;
        const procedure_itemset = new OpenEyes.UI.AdderDialog.ItemSet(procedure_list, procedure_itemset_options);
        let itemsets = [procedure_itemset];
        <?php if ($laterality_list) { ?>
            const laterality_options = {
                'id': 'Laterality',
                'header': 'Laterality',
                'multiSelect': false,
                'mandatory': true,
            };
            const laterality_list = <?= json_encode($laterality_list) ?>;
            const laterality_itemset = new OpenEyes.UI.AdderDialog.ItemSet(laterality_list, laterality_options);
            itemsets.unshift(laterality_itemset);
        <?php } ?>
        new OpenEyes.UI.AdderDialog({
            openButton: $('<?= $bind_add_button ?>'),
            itemSets: itemsets,
            <?php if ($search_source) { ?>
                searchOptions: {
                    searchSource: '<?= $search_source ?>',
                },
            <?php } ?>
            onReturn: function(adderDialog, selectedItems) {
                let laterality = {};
                let selected_procedures = [];
                let template_data = {};
                let $template_html = $('<?= $template ?>').html();
                selectedItems.forEach(item => {
                    // item from search
                    if (!item.itemSet) {
                        selected_procedures.push(item);
                        return;
                    }
                    // item from lists
                    switch (item.itemSet.options.id) {
                        case 'Laterality':
                            laterality = item;
                            break;
                        case 'Procedures':
                            selected_procedures.push(item);
                            break;
                    }
                });
                selected_procedures.forEach(proc => {
                    // check if there is any existing procedure
                    if ($(`input[<?= $proc_hidden_input_identifier ?>][value=${proc.id}]`).length && proc.id !== -1) {
                        return;
                    }
                    let template_data = {};
                    template_data.key = OpenEyes.Util.getNextDataKey($proc_table.find('tr.js-proc-row'), 'key');
                    template_data.eye_id = laterality.id;
                    template_data.right = laterality.right;
                    template_data.left = laterality.left;
                    template_data.proc_id = proc.id;
                    if (proc.html) {
                        template_data.html = proc.html;
                    } else {
                        template_data.term = proc.label;
                    }
                    const $proc_html = Mustache.render($template_html, template_data);
                    <?php if ($aneathetic_row) { ?>
                        $($proc_html).insertBefore($proc_table.find('<?= $aneathetic_row ?>'));
                    <?php } else { ?>
                        $proc_table.append($proc_html);
                    <?php } ?>
                    <?php if ($trigger_benefits_risks_change) { ?>
                        if (typeof(window.callbackAddProcedure) == 'function') {
                            callbackAddProcedure(proc.id, is_extra_procedure);
                        }
                    <?php } ?>
                });
            }
        });
        $proc_table.off('click', 'i.trash').on('click', 'i.trash', function() {
            const $tr = $(this).closest('tr');
            const procedure_id = $tr.find('input[<?= $proc_hidden_input_identifier ?>]').val();
            if (!procedure_id) {
                return;
            }
            $tr.remove();
            if (typeof(window.callbackRemoveProcedure) === 'function') {
                callbackRemoveProcedure(procedure_id);
            }
        });
    });
</script>