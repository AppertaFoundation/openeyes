<?php
    $assigned_teams = $pgdpsd->teams ? : $pgdpsd->temp_teams;
    $assigned_users = $pgdpsd->users ? : $pgdpsd->temp_users;
    $assigned_meds = $pgdpsd->assigned_meds ? : $pgdpsd->temp_meds;
    $assigned_user_ids = array_map(function ($assigned_user) {
        return $assigned_user->id;
    }, $assigned_users);
    $assigned_users = $assigned_user_ids ? $this->api->getInstitutionUserAuth(true, $assigned_user_ids) : array();
    $assigned_users = array_map(function ($assigned_user) {
        return $assigned_user->user->getUserPermissionDetails();
    }, $assigned_users);

    // setup default pgdpsd type
    $pgdpsd->type = $pgdpsd->type ? : 'PSD';
    ?>
<h2><?=$title_action?> PGD/PSD</h2>

<?php
    $this->renderPartial('//admin/_form_errors', array('errors' => $errors));
?>
<?php
    $form = $this->beginWidget(
        'BaseEventTypeCActiveForm',
        [
            'id' => 'adminform',
            'enableAjaxValidation' => false,
            'focus' => '#name',
            'layoutColumns' => array(
                'label' => 2,
                'field' => 4,
            ),
        ]
    );
    ?>
<div class="row divider">
    <div class="cols-full">
        <table class="large">
            <tbody>
                <tr>
                    <td>Type*</td>
                    <td>
                        <?= \CHtml::activeRadioButtonList(
                            $pgdpsd,
                            'attributes[type]',
                            ['PGD' => 'PGD', 'PSD' => 'PSD'],
                            ['separator' => ' ', ]
                        ); ?>
                    </td>
                </tr>
                <tr>
                    <td>Name*</td>
                    <td>
                        <?= \CHtml::activeTextField(
                            $pgdpsd,
                            'attributes[name]',
                            [
                                'autocomplete' => Yii::app()->params['html_autocomplete'],
                                'class' => 'cols-full'
                            ]
                        ); ?>
                    </td>
                </tr>
                <tr>
                    <td>Description<span class="pgd-required-field">*</span></td>
                    <td>
                        <?= \CHtml::activeTextField(
                            $pgdpsd,
                            'attributes[description]',
                            [
                                'autocomplete' => Yii::app()->params['html_autocomplete'],
                                'class' => 'cols-full'
                            ]
                        ); ?>
                    </td>
                </tr>
                <tr>
                    <td>Active*</td>
                    <td>
                        <?=
                            \CHtml::activeCheckBox(
                                $pgdpsd,
                                'attributes[active]',
                                array('checked' => $pgdpsd->isNewRecord ? true : $pgdpsd->active)
                            );
                            ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
    $this->renderPartial(
        'application.views.default._team_assignment',
        array(
            'teams' => $teams,
            'assigned_teams' => $assigned_teams,
            'pgd' => $pgdpsd,
            'prefix' => $prefix
        )
    );

    $this->renderPartial(
        'application.views.default._user_assignment',
        array(
            'users' => $users,
            'assigned_users' => $assigned_users,
            'prefix' => $prefix,
        )
    );
    $this->renderPartial(
        'pgdpsdsettings/_meds_assignment',
        array(
            'assigned_meds' => $assigned_meds,
            'medications' => $medications,
            'prefix' => $prefix,
        )
    );
    echo \OEHtml::submitButton();
    echo \OEHtml::cancelButton(
        "Cancel",
        [
            'data-uri' => $cancel_url,
        ]
    );
    $this->endWidget();
    ?>

<script>
    const switchType = function(type){
        if(type.toLowerCase() === 'pgd'){
            $('a.js-pgd-only.has-comment').hide();
            $('tr.js-pgd-only.has-comment').show();
            $('a.js-pgd-only.no-comment').show();
            $('tr.js-pgd-only.no-comment').hide();
            $('.js-copy-meds').hide();
            $('.pgd-required-field').show();
            $('.js-pgd-only').find('input, select').prop('disabled', false);
        } else {
            $('.js-pgd-only').find('input, select').prop('disabled', true);
            $('.js-add-pgd-comments').hide();
            $('.js-comment-container').hide();
            $('.js-copy-meds').show();
            $('.pgd-required-field').hide();
        }
    }
    $(function(){
        const $radio_type = $('input[type="radio"][name$="[type]"]');
        $radio_type.off('change').on('change', function(){
            switchType(this.value);
        });
        $('input[type="radio"][name$="[type]"]:checked').trigger('change');
    });
</script>