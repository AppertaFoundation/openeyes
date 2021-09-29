<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                'id' => 'queueset-form',
                'enableAjaxValidation' => false,
                'layoutColumns' => array(
                        'label' => 3,
                        'field' => 8,
                ), ));

$qs_svc = Yii::app()->service->getService($this::$QUEUESET_SERVICE);
$roles = $qs_svc->getQueueSetRoles();

?>

<div class="data-group">
    <?php
    if (count($roles) > 1) {
        ?>
            <div class="alert-box issue">Support for multiple Patient Ticketing roles not yet implemented.</div>
        <?php
    } elseif (count($roles) === 1) {
        ?>
        <div class="alert-box info">User(s) will be given the "<?=$roles[0]?>" role if not already setup.</div>
        <input type="hidden" name="user_role" value="<?=$roles[0]?>" />
        <?php
    } else {
        ?>
            <div>Error: At least one role must be configured for Patient Ticketing!</div>
        <?php
    }
    ?>
</div>


<div class="data-group">
    <div id="current-users-col" class="column large-6">
        <h3>Current Users</h3>
        <ul id="current-users-list">
            <?php
            foreach ($queueset->permissioned_users as $user) {
                $this->renderPartial('form_queueset_perms_user', array(
                            'fullname' => $user->getFullName(),
                            'id' => $user->id,
                        ));
            }
            ?>
        </ul>
    </div>
    <div id="new-user-col" class="column large-6 end">
        <h3>Add User(s)</h3>
        <div class="autocomplete-row">
            <?php $this->widget('application.widgets.AutoCompleteSearch', ['htmlOptions' => ['placeholder' => 'Search by name or user'], 'layoutColumns' => ['field' => '4']]); ?>
        </div>
    </div>
    <script id="user-template" type="x-tmpl-mustache">
        <?php
            $this->renderPartial('form_queueset_perms_user', array(
                'fullname' => '{{fullname}}',
                'id' => '{{id}}',
            ));
            ?>
    </script>
</div>
<script>
    $(document).ready(function () {
        if(OpenEyes.UI.AutoCompleteSearch !== undefined){
            OpenEyes.UI.AutoCompleteSearch.init({
                input: $('#oe-autocompletesearch'),
                url: '<?=Yii::app()->createUrl('/admin/userfind');?>',
                onSelect: function(){
                    let autoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
                    let $current_user_list = $('#current-users-list');
                    let current_user_ids = [];
                    $current_user_list.find('input[name*=user_ids]').each(function () {
                        current_user_ids.push($(this).val());
                    });
                    if (!current_user_ids.includes(autoCompleteResponse.id)) {
                        $current_user_list.append(
                            Mustache.render($('#user-template').html(),
                                {
                                    fullname: autoCompleteResponse.value,
                                    id: autoCompleteResponse.id
                                }
                            )
                        );
                    } else {
                        let $alert = $('<div>',{'class':'alert-box issue'}).text(autoCompleteResponse.value + ' has already been added to the list').hide();
                        let $alert_div = $('#queueset-form').find('.data-group').first();
                        $alert.appendTo($alert_div).slideDown();
                        setTimeout(function () { $alert.slideUp(); }, 2000);
                        setTimeout(function () { $alert.remove(); }, 3000);
                    }
                    return false;
                }
            });
        }
    });
</script>

<?php
$this->endWidget();
