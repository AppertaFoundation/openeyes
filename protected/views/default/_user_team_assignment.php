<?php
$tasks_list = Team::getTasksList(Team::ALL_TASKS);

$assigned_tasks = $team->getUsersWithAssignedTasks();

$can_change_team_role = $super_user || $team->isNewRecord || $this->checkAccess('OprnChangeTeamMemberRole', $team->id);
$can_add_member = $super_user || $team->isNewRecord || $this->checkAccess('OprnAddTeamMember', $team->id);
$can_remove_member = $super_user || $this->checkAccess('OprnRemoveTeamMember', $team->id);
?>
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
                    <th>Team Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="team-user-selections">
                <?php foreach ($assigned_users as $key => $user) { ?>
                <tr data-key="<?= $key ?>">
                    <input type="hidden" name="<?= $prefix ?>[user][<?= $key ?>][id]" value="<?= $user['id'] ?>" data-test="team-user-id">
                    <td><?=$user['name']?></td>
                    <td><?=$user['grade']?></td>
                    <td><?=$user['can_prescribe']?></td>
                    <td><?=$user['is_med_administer']?></td>
                    <td><?=$user['consultant']?></td>
                    <td>
                        <?php if ($can_change_team_role) { ?>
                        <select name="<?= $prefix ?>[user][<?= $key ?>][task]" data-test="team-user-task">
                            <?php foreach ($tasks_list as $task => $name) { ?>
                            <option value="<?= $task ?>"<?= $task === $assigned_tasks[$user['id']] ? ' selected' : '' ?>>
                                <?= $name ?>
                            </option>
                            <?php } ?>
                        </select>
                        <?php } else { ?>
                        <input type="hidden" name="<?= $prefix ?>[user][<?= $key ?>][task]" value="<?= $assigned_tasks[$user['id']] ?>" data-test="team-user-task" />
                        <?= $tasks_list[$assigned_tasks[$user['id']]] ?>
                        <?php } ?>
                    </td>
                    <td>
                        <a href="javascript:void(0);"<?= $can_remove_member ? ' class="js-delete-user"' : '' ?>>
                            <i class="oe-i trash<?= !$can_remove_member ? ' disabled' : '' ?>"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
                <tr>
                    <td colspan="7">
                        <div class="flex-layout flex-right">
                            <button id="js-add-user"
                                    class="button hint green<?= !$can_add_member ? ' disabled' : '' ?>"
                                    type="button"
                                    data-test="add-user">
                                <i class="oe-i plus pro-theme"></i>
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
        <input type="hidden" name="{{prefix}}[user][{{key}}][id]" value="{{id}}" data-test="team-user-id">
        <td>{{label}}</td>
        <td>{{grade}}</td>
        <td>{{can_prescribe}}</td>
        <td>{{is_med_administer}}</td>
        <td>{{consultant}}</td>
        <td>
            <?php if ($can_change_team_role) { ?>
            <select name="{{prefix}}[user][{{key}}][task]" data-test="team-user-task">
                <?php foreach ($tasks_list as $task => $name) { ?>
                <option value="<?= $task ?>"<?= $task === Team::DEFAULT_TASK ? ' selected' : '' ?>>
                    <?= $name ?>
                </option>
                <?php } ?>
            </select>
            <?php } else { ?>
                <input type="hidden" name="<?= $prefix ?>[user][<?= $key ?>][task]" value="<?= Team::DEFAULT_TASK ?>" data-test="team-user-task" />
                <?= $tasks_list[Team::DEFAULT_TASK] ?>
            <?php } ?>
        </td>
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
                    item.key = $('#team-user-selections tr').length;
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
