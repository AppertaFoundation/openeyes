<div class="row divider">
    <div class="cols-12">
        <h3>Teams Assignment</h3>
        <table class="standard">
            <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Team Members</th>
                    <th>Team Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="team-team-selections">
                <?php foreach ($assigned_teams as $team) {?>
                <tr data-key="<?=$team->id?>">
                    <input type="hidden" value="<?=$team->id?>" name="<?=$prefix?>[team_assign][]">
                    <td class="team-name"><?=$team->name?></td>
                    <td>
                        <ul class="js-member-results">
                        <?php
                            $assigned_user_ids = array_map(function ($assigned_user) {
                                return $assigned_user->id;
                            }, $team->getAllUsers());
                            $user_auth_objs = $this->api->getInstitutionUserAuth(true, $assigned_user_ids);
                        foreach ($user_auth_objs as $user_auth) {
                            ?>
                            <li data-user-id="<?=$user_auth->user->id?>">
                            <?=$user_auth->user->getFullName()?>
                                <i class="oe-i info small js-has-tooltip" data-tooltip-content="<?=$user_auth->user->getUserPermissionDetails(true)?>"></i>
                            </li>
                        <?php } ?>
                        </ul>
                    </td>
                    <td><?=$team->email?></td>
                    <td>
                        <a href="javascript:void(0);" class="js-delete-team">
                            <i class="oe-i trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
                <tr>
                    <td colspan="4">
                        <div class="flex-layout flex-right">
                            <button id="js-add-team" class="button hint green" type="button">
                                <i class="oe-i plus pro-theme"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script type="x-tmpl-mustache" id="team_row_template" style="display:none">
    <tr data-key="{{id}}">
        <input type="hidden" value="{{id}}" name="{{prefix}}[team_assign][]">
        <td class="team-name">{{label}}</td>
        <td>
            <i class="spinner as-icon small"></i>
            <ul class="js-member-results"></ul>
        </td>
        <td>{{email}}</td>
        <td>
            <a href="javascript:void(0);" class="js-delete-team">
                <i class="oe-i trash"></i>
            </a>
        </td>
    </tr>
</script>
<script>
    $(function(){
        const prefix = '<?=$prefix;?>';
        const teams = <?=json_encode($teams);?>;
        const team_id_query = '<?= isset($current_team) && !$current_team->isNewRecord ? "?team_id={$current_team->id}" : ''?>';
        const team_search_uri = `/oeadmin/team/autocomplete${team_id_query}`;
        const team_member_search_uri = '/oeadmin/team/CheckTeamMembers';
        const $team_selections_tbl = $('#team-team-selections');
        const team_itemSet = new OpenEyes.UI.AdderDialog.ItemSet(teams, {
                'id': 'team',
                'multiSelect': true,
                'header': "Team",
            });
        new OpenEyes.UI.AdderDialog({
            openButton: $('#js-add-team'),
            itemSets: [team_itemSet],
            searchOptions: {
                searchSource: team_search_uri,
            },
            onReturn: function(adderDialog, selectedItems){
                selectedItems.forEach(function(item, i){
                    const team_id = item['id'];
                    if($team_selections_tbl.find(`input[name="${prefix}[team_assign][]"][value="${team_id}"]`).length){
                        return;
                    }
                    item.prefix = prefix;
                    let template = $('#team_row_template').html();
                    Mustache.parse(template);
                    let rendered = Mustache.render(template, item);
                    $team_selections_tbl.append(rendered);

                    const $spinner = $team_selections_tbl.find(`tr[data-key="${team_id}"] .spinner`);
                    const $search_result_ctn = $team_selections_tbl.find(`tr[data-key="${team_id}"] .js-member-results`);
                    $.get(
                        team_member_search_uri,
                        {
                            id: team_id
                        },
                        function(resp){
                            let response = Array.isArray(resp) ? resp : Object.values(resp);
                            $spinner.hide();
                            $search_result_ctn.html('');
                            response.forEach(function(item, i){
                                const icon = document.createElement('i');
                                $(icon).addClass('oe-i info small js-has-tooltip').attr('data-tooltip-content', item['tooltips']);
                                const result_li = document.createElement('li');
                                $(result_li).text(item['name']).attr('data-user-id', item['id']);
                                $(result_li).append(' ').append(icon);
                                $search_result_ctn.append(result_li);
                            });
                        }
                    );
                });
            }
        });
        $team_selections_tbl.off('click', '.js-delete-team').on('click', '.js-delete-team', function(e){
            $(this).closest("tr").remove();
        });
    });
</script>