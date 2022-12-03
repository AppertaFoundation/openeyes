<?php
$settings = new SettingMetadata();
$this->pageTitle = ((string)SettingMetadata::model()->getSetting('use_short_page_titles') != "on" ? Yii::app()->name . ' - ' : '') . 'Login';
$institutions = \Institution::model()->with('authenticationMethods')->findAll();

$institution_required = $settings->getSetting('institution_required') == 'on';
$default_inst = !$institution_required ? Institution::model()->findByAttributes(['remote_id' => Yii::app()->params['institution_code']]) : null;
$sites = \Site::model()->findAllByAttributes(['active' => true ]);
$site_map = [];
$institution_defaults = [];
foreach ($institutions as $institution) {
    $site_map[$institution->id] = [];
    $institution_defaults[$institution->id] = $institution->first_used_site_id ?? ($institution->sites[0]->id ?? null);
}
foreach ($sites as $site) {
    $site_map[$site->institution_id][] = $site->id;
}

$tech_support_provider = Yii::App()->params['tech_support_provider'] ? htmlspecialchars(Yii::App()->params['tech_support_provider']) : htmlspecialchars($settings->getSetting('tech_support_provider'));
$tech_support_url = Yii::App()->params['tech_support_url'] ? htmlspecialchars(Yii::App()->params['tech_support_url']) : htmlspecialchars($settings->getSetting('tech_support_url'));

$institutions_count = count($institutions);
//Single Tenant Backwards Compatibility Mode
$has_site_specific_auth = false;
if ($institutions_count === 1 && count($sites) > 1) {
    foreach ($institutions[0]->authenticationMethods as $auth_method) {
        if (isset($auth_method->site_id)) {
            $has_site_specific_auth = true;
            break;
        }
    }
}
$display_institution = $institution_required || $institutions_count > 1;
$display_site = ($institution_required || $has_site_specific_auth) || $institutions_count > 1;
?>

<div class="oe-login">
    <div class="login multisite">
        <?php if (isset($login_type) && $login_type === "esigndevice") :?>
            <h1>OpenEyes e-Sign</h1>
        <?php else : ?>
            <h1>OpenEyes</h1>
        <?php endif; ?>
        <div class="login-details">
            <ul class="row-list">
                <li class="login-institution" style="display: <?= $display_institution ? '' : 'none;' ?>"></li>
                <li class="login-site" style="display: <?= $display_site ? '' : 'none;' ?>"></li>
            </ul>
        </div>

        <div class="login-steps">
            <ul class="step-options js-institutions" style="display: none">
                <?php foreach ($institutions as $institution) { ?>
                    <li class="js-institution" data-id='<?= $institution->id ?>'><?= $institution->name ?></li>
                <?php } ?>
            </ul>

            <ul class="step-options js-sites" style="display: none">
                <?php foreach ($sites as $site) { ?>
                    <li class="js-site" data-id='<?= $site->id ?>'><?= $site->name ?></li>
                <?php } ?>
            </ul>
        </div>

        <div class="user">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'loginform',
                'enableAjaxValidation' => false,
            )); ?>

            <?php echo $form->error($model, 'password', array('class' => 'alert-box error')); ?>
            <?php echo $form->error($model, 'institution_id', array('class' => 'alert-box error')); ?>
            <?php echo $form->error($model, 'site_id', array('class' => 'alert-box error')); ?>

            <?= $form->hiddenField($model, 'institution_id', ['class' => 'js-institution-id']) ?>
            <?= $form->hiddenField($model, 'site_id', ['class' => 'js-site-id']) ?>

            <?php echo $form->textField($model, 'username', array(
                'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                'placeholder' => 'Username',
            )); ?>

            <?php echo $form->passwordField(
                $model,
                'password',
                array(
                    'autocomplete' => 'off',
                    'placeholder' => 'Password'
                )
            ); ?>

            <i class="spinner" style="display:none"></i>

            <button type="submit" id="login_button" class="green hint"><?= isset($login_type) && $login_type === "esigndevice" ? "Link device" : "Login" ?></button>

            <div class="oe-user-banner">
                <?php $this->renderPartial('//base/_banner_watermark_full'); ?>
            </div>

            <?php $this->endWidget(); ?>
            <!-- user -->
        </div>
        <div class="info">
            <?php if (isset($login_type) && $login_type === "esigndevice") :?>
                Please login using your OpenEyes username to link with this device
            <?php else : ?>
                <div class="flex-layout">
                <span class="large-text"> Need Help?&nbsp;
                  <?php  $purifier = new CHtmlPurifier(); ?>
                    <?php if (SettingMetadata::model()->getSetting('helpdesk_phone') || SettingMetadata::model()->getSetting('helpdesk_email')) : ?>
                        <?= SettingMetadata::model()->getSetting('helpdesk_phone') ? "<strong>" . $purifier->purify(SettingMetadata::model()->getSetting('helpdesk_phone')) . "</strong>" : null ?></strong>
                        <?= SettingMetadata::model()->getSetting('helpdesk_email') ? "<br/>" . $purifier->purify(SettingMetadata::model()->getSetting('helpdesk_email')) : null ?>
                        <?= SettingMetadata::model()->getSetting('helpdesk_hours') ? "<br/> (" . $purifier->purify(SettingMetadata::model()->getSetting('helpdesk_hours')) . ")" : null ?>
                    <?php elseif ($tech_support_provider) : ?>
                        <strong><a href="<?= $tech_support_url ?>" target="_blank"><?= $tech_support_provider ?></a></strong>
                    <?php endif; ?>
                </span>
                    <a href="#" onclick="$('#js-openeyes-btn').click();">About</a>
                </div>
            <?php endif; ?>
            <!-- info -->
        </div>
        <!-- login -->
    </div>
</div>

<script type="text/javascript">
    let cookie_institution_id = OpenEyes.Util.getCookie('current_institution_id');
    let cookie_site_id = OpenEyes.Util.getCookie('current_site_id');
    let has_error = Boolean(<?= $model->hasErrors() ?>);
    const institution_required = Boolean(<?= $display_institution ?>);
    const default_institution_id = <?= $default_inst ? $default_inst->id : 'undefined' ?>;

    let site_map = JSON.parse('<?= JSON_encode($site_map) ?>');
    let institution_defaults = JSON.parse('<?= JSON_encode($institution_defaults) ?>');

    function beforeInstitutionSelection(set_default_institution = true) {
        document.querySelector(".step-options.js-sites").style.display = 'none';
        document.querySelector(".step-options.js-institutions").style.display = '';
        document.querySelector(".user").style.display = 'none';
        document.querySelector(".login-steps").style.display = '';
        document.querySelector(".login-institution").innerHTML = '<small>Please select an institution</small>';

        let institutions = document.getElementsByClassName("js-institution");
        for (let institution of institutions) {
            if (site_map[institution.dataset.id].length > 0) {
                institution.style.display = '';
            } else {
                institution.style.display = 'none';
            }
        }

        document.querySelector(".js-site-id").value = '';
        document.querySelector(".js-institution-id").value = '';
        document.querySelector(".login-site").innerHTML = '';

        if (set_default_institution && cookie_institution_id) {
            institution = findItemById(institutions, cookie_institution_id);
            afterInstitutionSelection(institution);
        }
    }

    function findItemById(items, id) {
        for (let item of items) {
            if (item.dataset.id == id) {
                return item;
            }
        }
    }

    function removeIcon(onclick) {
        return '<i class="oe-i remove-circle small-icon pad-left" onclick="' + onclick + '"></i>';
    }

    function setInstitutionText(institution) {
        document.querySelector(".login-institution").innerHTML = institution.innerHTML +
            (institution_required ? removeIcon('onInstitutionClear()') : '');
    }

    function setSiteText(site) {
        document.querySelector(".login-site").innerHTML = site.innerHTML + removeIcon('onSiteClear()');
    }

    function afterInstitutionSelection(institution, set_default_site = true) {
        if (institution === undefined) {
            const institution_id = document.querySelector(".js-institution-id").value;
            let institutions = document.getElementsByClassName("js-institution");
            institution = findItemById(institutions, institution_id);
        }

        const institution_id = institution.dataset.id;
        if (typeof institution_id === 'undefined') {
            return;
        }

        document.querySelector(".step-options.js-sites").style.display = '';
        document.querySelector(".step-options.js-institutions").style.display = 'none';
        document.querySelector(".login-site").innerHTML = '<small>Please select a site</small>';
        document.querySelector(".user").style.display = 'none';
        document.querySelector(".login-steps").style.display = '';

        let sites = document.getElementsByClassName("js-site");
        for (let site of sites) {
            if (site_map[institution_id].includes(site.dataset.id)) {
                site.style.display = '';
            } else {
                site.style.display = 'none';
            }
        }

        setInstitutionText(institution);

        document.querySelector(".js-institution-id").value = institution_id;
        document.querySelector(".js-site-id").value = '';

        if (set_default_site) {
            const default_site_id = (site_map[institution_id].includes(cookie_site_id) ? cookie_site_id : undefined) || institution_defaults[institution_id];
            if (default_site_id) {
                const sites = document.getElementsByClassName("js-site");
                const default_site = findItemById(sites, default_site_id);
                afterSiteSelection(default_site);
            }
        }
    }

    function onInstitutionClear() {
        beforeInstitutionSelection(false);
    }

    function onSiteClear() {
        afterInstitutionSelection(undefined, false);
    }

    function afterSiteSelection(site) {
        const site_id = site.dataset.id;
        if (typeof site_id === 'undefined') {
            return;
        }
        document.querySelector(".login-steps").style.display = 'none';
        document.querySelector(".user").style.display = '';
        setSiteText(site)
        document.querySelector(".js-site-id").value = site_id;
    }

    function onValidationError() {
        const institution_id = document.querySelector(".js-institution-id").value;
        const institutions = document.getElementsByClassName("js-institution");
        const institution = findItemById(institutions, institution_id);
        setInstitutionText(institution);

        const site_id = document.querySelector(".js-site-id").value;
        const sites = document.getElementsByClassName("js-site");
        const site = findItemById(sites, site_id);
        setSiteText(site);

        document.querySelector(".login-steps").style.display = 'none';
        document.querySelector(".user").style.display = '';
    }

    document.querySelector(".step-options.js-institutions").onclick = function (event) {
        if (event.target.classList.contains('js-institution')) {
            afterInstitutionSelection(event.target);
        }
    }

    document.querySelector(".step-options.js-sites").onclick = function (event) {
        if (event.target.classList.contains('js-site')) {
            afterSiteSelection(event.target);
        }
    }

    if (has_error) {
        onValidationError();
    } else {
        if (institution_required) {
            beforeInstitutionSelection();
        } else {
            document.querySelector(".js-institution-id").value = default_institution_id;
            afterInstitutionSelection();
        }
    }

    if ($('#LoginForm_username').val() == '') {
        $('#LoginForm_username').focus();
    } else {
        $('#LoginForm_password').select().focus();
    }
    handleButton($('#login_button'));
</script>
