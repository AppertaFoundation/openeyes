<?php
/* @var $this TrialController */
/* @var $trial Trial
 * @var TrialPermission $permission
 * @var UserTrialAssignment $newPermission
 * @var CDataProvider $permissionDataProvider
 */
?>

<?php $this->renderPartial('_trial_header', array(
    'trial' => $trial,
    'title' => CHtml::encode($trial->name),
    'permission' => $permission,
)); ?>

<?= CHtml::hiddenField('trial_id', $trial->id, array('id' => 'trial-id')); ?>
<div class="oe-full-content subgrid oe-worklists">

    <?php $this->renderPartial('_trialActions', array(
        'trial' => $trial,
        'permission' => $permission,
    )); ?>

  <main class="oe-full-main">
    <section class="element edit full cols-10">
      <div class="element-fields">

        <table id="currentPermissions" class="standard">
          <thead>
          <tr>
            <th>User</th>
            <th>User Role</th>
            <th>Permission</th>
            <th>Principal Investigator</th>
            <th>Study Coordinator</th>
                <?php if ($permission && $permission->can_manage) : ?>
                <th></th>
                <?php endif; ?>
          </tr>
          </thead>
          <tbody>
            <?php $this->widget('zii.widgets.CListView', array(
              'id' => 'permissionList',
              'dataProvider' => $permissionDataProvider,
              'itemView' => '/userTrialPermission/_view',
              'enablePagination' => false,
              'summaryText' => false,
          )); ?>
          </tbody>
          <tfoot class="pagination-container">
          <tr>
            <td colspan="6">
              <div class="pagination">
                    <?php
                    $this->widget('LinkPager', array(
                      'pages' => $permissionDataProvider->getPagination(),
                      'maxButtonCount' => 15,
                      'cssFile' => false,
                      'nextPageCssClass' => 'oe-i arrow-right-bold medium pad',
                      'previousPageCssClass' => 'oe-i arrow-left-bold medium pad',
                      'htmlOptions' => array(
                          'class' => 'pagination',
                      ),
                    ))
                    ?>
              </div>
            </td>
          </tr>
          </tfoot>
        </table>

            <?php if ($permission && $permission->can_manage) : ?>
            <div class="cols-6">
              <h3 class="element-title">Share with another user:</h3>
              <table class="standard">
                <colgroup>
                  <col class="cols-2">
                  <col class="cols-4">
                </colgroup>
                <tbody>
                <tr>
                  <td>
                      <?= CHtml::activeLabel($newPermission, 'user'); ?>
                  </td>
                  <td>
                      <?php
                        $this->widget('application.widgets.AutoCompleteSearch',
                          [
                              'field_name' => "autocomplete_user_id",
                              'htmlOptions' =>
                                  [
                                      'placeholder' => 'Search Users',
                                  ],
                              'hide_no_result_msg' => true
                          ]);
                        ?>
                  </td>
                </tr>
                <tr>
                  <td>
                      <?= CHtml::activeLabel($newPermission, 'role'); ?>
                  </td>
                  <td>
                      <?= CHtml::activeTextField(
                          $newPermission,
                          'role',
                          array('maxlength' => 255, 'name' => 'user_role')
                      ); ?>

                  </td>
                </tr>
                <tr>
                  <td>
                      <?= CHtml::activeLabel($newPermission, 'permission'); ?>
                  </td>
                  <td>
                      <?= CHtml::dropDownList(
                          'permission',
                          'Select One...',
                          CHtml::listData(TrialPermission::model()->findAll(), 'id', 'name'),
                          array('id' => 'permission')
                      ) ?>

                  </td>
                </tr>
                </tbody>
              </table>

              <div id="selected_user_wrapper" style="<?= !$newPermission->user_id ? 'display: none;' : '' ?>">
                <button class="secondary small js-save-permission">Share with &nbsp;
                  <span id="user_name">
                      <?= CHtml::encode($newPermission->user_id ? $newPermission->user->getFullName() : ''); ?>
                      </span>
                </button>
                &nbsp;
                <a href="javascript:void(0)" class="button event-action cancel small"
                   onclick="removeSelectedUser()">Clear</a>
                  <?= CHtml::hiddenField(
                      'user_id',
                      $newPermission->user_id,
                      array('class' => 'hidden_id')
                  ); ?>
              </div>

              <div class="alert-box info with-icon" id="no-user-result" style="display: none;">
                Can't find the user you're looking for? They might not have the permission to view trials.
                <br/>
                Please contact an administrator and ask them to give that user the "Create Trial" or "Trial User" role.
              </div>
            </div>
            <?php endif; ?>
      </div>
    </section>
  </main>
</div>

<?php if ($permission && $permission->can_manage) : ?>
  <script type="text/javascript">
    function addItem(wrapper_id, response) {
      var $wrapper = $('#' + wrapper_id);

      $('#user_name').text(response.label);
      $wrapper.show();
      $wrapper.find('.hidden_id').val(response.id);
    }

    function removeSelectedUser() {
      $('#no_user_result').hide();
      $('#user_name').text('');
      $('#selected_user_wrapper').hide();
      $('#autocomplete_user_id').val('');
    }

    $(document).ready(function () {
      $('#selected_user_wrapper').on('click', '.remove', function () {
        removeSelectedUser();
      });

      $('.is_principal_investigator, .is_coordinator').change(function () {
        var user_id = $(this).data('user');
        var trial_id = $(this).data('trial');
        var loader = $('#pi-change-loader-' + user_id);
        var checked = $(this).prop('checked')?'1':'0';
        var checkbox = $(this);
        loader.show();
        $.ajax({
          'type': 'POST',
          'url': '<?= $this->createUrl('trial/changeTrialUserPosition'); ?>',
          'data': {
            id: trial_id,
            user_id: user_id,
            isTrue: checked,
            column_name: $(this).hasClass('is_principal_investigator') ? 'is_principal_investigator': 'is_study_coordinator',
            YII_CSRF_TOKEN: YII_CSRF_TOKEN
          },
          complete: function (response) {
            var res_obj = response.responseText? JSON.parse(response.responseText): {};
            if (res_obj.Error){
              new OpenEyes.UI.Dialog.Alert({
                content: res_obj.Error
              }).open();
              checkbox.prop('checked', true);
            }
            loader.hide();
          },
          error: function (error) {
            new OpenEyes.UI.Dialog.Alert({
              content: "Sorry, an internal error occurred and we were unable to change the principal investigator.\n\nPlease contact support for assistance."
            }).open();
          },
        });
      });

      $('.trial-permission-pi-selector').change(function () {
        var user_id = $(this).closest('tr').find('.user_id').val();
        var loader = $('#pi-change-loader-' + user_id);
        loader.show();
        $.ajax({
          'type': 'POST',
          'url': '<?= $this->createUrl('trial/changePi'); ?>',
          'data': {
            id: <?= $trial->id; ?>,
            user_id: user_id,
            YII_CSRF_TOKEN: YII_CSRF_TOKEN
          },
          complete: function (response) {
            loader.hide();
          },
          error: function () {
            new OpenEyes.UI.Dialog.Alert({
              content: "Sorry, an internal error occurred and we were unable to change the principal investigator.\n\nPlease contact support for assistance."
            }).open();
          },
        });
      });

      $('.trial-permission-coordinator-selector').change(function () {
        var user_id = $(this).closest('tr').find('.user_id').val();
        var loader = $('#coordinator-change-loader-' + user_id);
        loader.show();

        $.ajax({
          'type': 'POST',
          'url': '<?= $this->createUrl('trial/changeCoordinator'); ?>',
          'data': {
            id: $('#trial-id').val(),
            user_id: user_id,
            YII_CSRF_TOKEN: YII_CSRF_TOKEN,
          },
          complete: function (response) {
            loader.hide();
          },
          error: function (response) {
            new OpenEyes.UI.Dialog.Alert({
              content: "Sorry, an internal error occurred and we were unable to study coordinator.\n\nPlease contact support for assistance."
            }).open();
          }
        });
      });

      $('.js-save-permission').click(function () {

        var user_id = $(this).siblings('#user_id').val();
        if (user_id == '') {
          new OpenEyes.UI.Dialog.Alert({
            content: "Please select a user and a permission level"
          }).open();
          return false;
        }

        $.ajax({
          'type': 'POST',
          'url': '<?= $this->createUrl('addPermission'); ?>',
          'data': {
            id: $('#trial-id').val(),
            user_id: user_id,
            permission: $('#permission').val(),
            role: $('#user_role').val(),
            YII_CSRF_TOKEN: YII_CSRF_TOKEN
          },
          'success': function (html) {
            if (html === '<?= Trial::RETURN_CODE_USER_PERMISSION_ALREADY_EXISTS; ?>') {
              new OpenEyes.UI.Dialog.Alert({
                content: "That user has already been shared to this trial. To change their permissions, please remove them first and try again."
              }).open();
            } else if (html === '<?= Trial::RETURN_CODE_USER_PERMISSION_OK; ?>') {
              location.reload();
            } else {
              new OpenEyes.UI.Dialog.Alert({
                content: "An unknown response code was returned by the system: " + html + "\n\nPlease contact support for assistance."
              }).open();
            }
          }
          ,
          'error': function () {
            new OpenEyes.UI.Dialog.Alert({
              content: "Sorry, an internal error occurred and we were unable to remove the permission.\n\nPlease contact support for assistance."
            }).open();
          }
        });

        return false;
      });


      $('.js-remove-permission').on('click', function () {
        var $container = $(this).closest('.js-user-trial-permission');
        var permissionId = $container.data('permission-id');
        var confirmDialog = new OpenEyes.UI.Dialog.Confirm({
          title: 'Remove User',
          content: 'Are you sure you want to remove this user?'
        });

        confirmDialog.content.on('click', '.ok', function () {
          removePermission(permissionId);
        });
        confirmDialog.open();
      });

      function removePermission(permission_id) {
        var $loader = $('#remove-permission-loader-' + permission_id);
        $loader.css('visibility', 'visible');

        $.ajax({
          'type': 'POST',
          'url': baseUrl + '<?= Yii::app()->controller->createUrl('/OETrial/trial/removePermission'); ?>',
          'data': {
            id: $('#trial-id').val(),
            permission_id: permission_id,
            YII_CSRF_TOKEN: YII_CSRF_TOKEN
          },
          'complete': function (result) {
            $loader.css('visibility', 'hidden');
          },
          'success': function (result) {
            if (result === '<?= Trial::REMOVE_PERMISSION_RESULT_SUCCESS; ?>') {
              var row = $('#currentPermissions tr[data-permission-id="' + permission_id + '"]');
              row.hide('slow', function () {
                row.remove();
              });
            } else if (result === '<?= Trial::REMOVE_PERMISSION_RESULT_CANT_REMOVE_SELF; ?>') {
              new OpenEyes.UI.Dialog.Alert({
                content: "You can't remove yourself from this Trial.\n\nYou will have to get another user with Manage privileges to remove you."
              }).open();
            } else if (result === '<?= Trial::REMOVE_PERMISSION_RESULT_CANT_REMOVE_LAST; ?>') {
              new OpenEyes.UI.Dialog.Alert({
                content: "You can't remove the last user from the Trial.\n\nThere must always be at least one person assigned to a Trial."
              }).open();
            } else {
              new OpenEyes.UI.Dialog.Alert({
                content: "Sorry, an internal error occurred and we were unable to remove the permission.\n\nPlease contact support for assistance."
              }).open();
            }
          },
          'error': function () {
            new OpenEyes.UI.Dialog.Alert({
              content: "Sorry, an internal error occurred and we were unable to remove the permission.\n\nPlease contact support for assistance."
            }).open();
          }
        });
      }
    });
  </script>
<?php endif; ?>

<script type="text/javascript">
    $(document).ready(function() {
        OpenEyes.UI.AutoCompleteSearch.init({
            input: $('#autocomplete_user_id'),
            url: '<?= $this->createUrl('userAutoComplete') ?>',
            params: {
                'id': function () {return "<?= $trial->id ?>"},
            },
            maxHeight: '200px',
            minimumCharacterLength: 1,
            onSelect: function () {
                let response = OpenEyes.UI.AutoCompleteSearch.getResponse();
                let input = OpenEyes.UI.AutoCompleteSearch.getInput();

                removeSelectedUser();
                addItem('selected_user_wrapper', response);
                $('#autocomplete_user_id').val($('#user_name').text());
            }
        });
    });
</script>