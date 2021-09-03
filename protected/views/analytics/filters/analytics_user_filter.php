<?php
    $user_filter_list = $is_service_manager ? array_map(function ($user) {
        return ['label' =>"{$user->first_name} {$user->last_name}", 'id' => $user->id];
    }, $user_list) : array();
    ?>
<tr class="clinical-filter custom-filter vf-filter">
    <td>Users</td>
    <td data-name="analytics_user">
        <?php if ($is_service_manager) {?>
            <span
            data-label="all user"
            data-id=""
            >
                All Users
            </span>
        <?php } else {?>
        <span 
        data-label="<?=$current_user->getFullName();?>"
        data-id="<?=$current_user->id?>"
        ><?=$current_user->getFullName();?></span>
        <?php }?>
    </td>
    <td>
        <?php if ($is_service_manager) {?>
            <button class="button hint green thin js-add-select-btn" id="show-analytics-filter-user" type="button">
                <i class="oe-i plus pro-theme"></i>
            </button>
        <?php }?>
    </td>
</tr>

<script>
    $(function(){
        const is_service_manager = <?=json_encode($is_service_manager);?>;
        if(!is_service_manager){
            return;
        }
        const user_list = <?= json_encode($user_filter_list);?>;
        user_list.unshift({
            'label': 'All Users',
            'id': '',
            'defaultSelected': true,
        })
        new OpenEyes.UI.AdderDialog({
            openButton: $('#show-analytics-filter-user'),
            source: 'sidebar',
            parentContainer: 'body',
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(user_list)],
            onReturn: function(adderDialog, selectedItems){
                const user_selection_ctn = $('td[data-name="analytics_user"]');
                const user_selection = $('td[data-name="analytics_user"] span');
                user_selection.attr('data-label', selectedItems[0].label);
                user_selection.attr('data-id', selectedItems[0].id);
                user_selection.text(selectedItems[0].label);
                return true;
            }
        });
    })
</script>