<div class="row divider">
    <div class="cols-12">
        <h3>Users Assignment</h3>
        <div class="alert-box issue" id="js-user-warning" style="display: none;">
            The following user(s) is(are) included in a selected team and they will be saved with the team
            <ul>
            </ul>
        </div>
        <table class="standard">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Grade</th>
                    <th>Can Prescribe</th>
                    <th>Is Med Administer</th>
                    <th>Is Consultant</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="team-user-selections">
                <?php foreach ($assigned_users as $key => $user) { ?>
                <tr data-key="<?=$key?>">
                    <input type="hidden" name="<?=$prefix?>[user][]" value="<?=$user['id']?>">
                    <td><?=$user['name']?></td>
                    <td><?=$user['grade']?></td>
                    <td><?=$user['can_prescribe']?></td>
                    <td><?=$user['is_med_administer']?></td>
                    <td><?=$user['consultant']?></td>
                    <td>
                        <a href="javascript:void(0);" class="js-delete-user">
                            <i class="oe-i trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
                <tr>
                    <td colspan="7">
                        <div class="flex-layout flex-right">
                            <button id="js-add-user" class="button hint green" type="button"><i
                                        class="oe-i plus pro-theme"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script type="x-tmpl-mustache" id="user_row_template" style="display:none">
    <tr data-key="{{id}}">
        <input type="hidden" name="{{prefix}}[user][]" value="{{id}}">
        <td>{{label}}</td>
        <td>{{grade}}</td>
        <td>{{can_prescribe}}</td>
        <td>{{is_med_administer}}</td>
        <td>{{consultant}}</td>
        <td>
            <a href="javascript:void(0);" class="js-delete-user">
                <i class="oe-i trash"></i>
            </a>
        </td>
    </tr>
</script>
<script>
    $(function(){
        const prefix = '<?=$prefix;?>';
        const users = <?=json_encode($users);?>;
        const user_search_uri = '/user/autocomplete';
        const $user_selections_tbl = $('#team-user-selections');
        const user_itemSet = new OpenEyes.UI.AdderDialog.ItemSet(users, {
                'id': 'user',
                'multiSelect': true,
                'header': "User",
            });
        new OpenEyes.UI.AdderDialog({
            openButton: $('#js-add-user'),
            itemSets: [user_itemSet],
            searchOptions: {
                searchSource: user_search_uri,
            },
            onReturn: function(adderDialog, selectedItems){
                selectedItems.forEach(function(item, i){
                    const user_name = item['label'];
                    const user_id = item['id'];
                    if($user_selections_tbl.find(`input[value="${user_id}"]`).length){
                        return;
                    }
                    item.prefix = prefix;
                    let template = $('#user_row_template').html();
                    Mustache.parse(template);
                    let rendered = Mustache.render(template, item);
                    $user_selections_tbl.append(rendered);
                });
            }
        });
        $user_selections_tbl.off('click', '.js-delete-user').on('click', '.js-delete-user', function(e){
            $(this).closest("tr").remove();
        });
    });
</script>