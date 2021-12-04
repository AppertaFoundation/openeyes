<?php
/**
 * @var Worklist[] $worklists
 */
?>

<input type="hidden" id="wl_print_selected_worklist" value="" />

<div class="oe-full-header">
    <div class="sync-data" id="js-sync-data">
        <div class="sync-btn <?=$sync_interval_value === 'off' ? '' : 'on'?>" id="js-sync-btn">
            <div class="last-sync"><?=date('H:i')?></div>
            <div class="sync-interval"><?=$sync_interval_value === 'off' ? 'Sync OFF' : $sync_interval_options[$sync_interval_value]?></div>
        </div>
        <div class="sync-options" id="js-sync-options" style="display:none;">
            <ul>
                <?php foreach ($sync_interval_options as $key => $option) {?>
                    <li>
                        <button data-value="<?=$key === 'off' ? 'Sync OFF' : $option?>" data-value-key="<?=$key?>" class="header-tab">
                            <?=($key === 'off' ? '': 'Sync: ') . $option?>
                        </button>
                    </li>
                <?php }?>
            </ul>
        </div>
    </div>
    <div class="title wordcaps">Worklists</div>
    <div class="options-right">
        <button class="button header-tab icon" onclick="goPrint();" name="print" type="button" id="et_print"><i class="oe-i print"></i></button>
    </div>
</div>

<div class="oe-full-content subgrid oe-worklists">

    <nav class="oe-full-side-panel">
        <p>Automatic Worklists</p>
        <div class="row">
            <?php $this->renderPartial('//site/change_site_and_firm', array('returnUrl' => Yii::app()->request->url, 'mode' => 'static')); ?>
        </div>
        <h3>Filter by Date</h3>
        <div class="flex-layout">
            <input id="worklist-date-from" class="cols-4" placeholder="from" type="text" value="<?= Yii::app()->request->getParam('date_from', '') ?>">
            <input id="worklist-date-to" class="cols-4" placeholder="to" type="text" value="<?= Yii::app()->request->getParam('date_to', '') ?>">
            <a href="#" class="selected js-clear-dates" id="sidebar-clear-date-ranges">Today</a>
        </div>

        <h3>Select list</h3>
        <ul id="js-worklist-category">
            <li><a class="js-worklist-filter" href="#" data-worklist="all">All</a></li>
            <?php foreach ($worklists as $worklist) : ?>
                <li><a href="#" class="js-worklist-filter"
                       data-worklist="js-worklist-<?= $worklist->id ?>"><?= $worklist->name ?>  : <?= $worklist->getDisplayShortDate() ?></a></li>
            <?php endforeach; ?>
        </ul>
        <?=$assign_preset_btn;?>
    </nav>

    <main class="oe-full-main">
        <?php foreach ($worklists as $worklist) : ?>
            <?php echo $this->renderPartial('_worklist', array('worklist' => $worklist, 'is_prescriber' => $is_prescriber)); ?>
        <?php endforeach; ?>
    </main>

    <div class="oe-popup-wrap js-add-psd-popup" style="display:none">
        <?=$preset_popup;?>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        pickmeup('#worklist-date-from', {
            format: 'd b Y',
            hide_on_select: true,
            date: $('#worklist-date-from').val(),
            default_date: false,
        });
        pickmeup('#worklist-date-to', {
            format: 'd b Y',
            hide_on_select: true,
            date: $('#worklist-date-to').val(),
            default_date: false,
        });

        $('#worklist-date-from, #worklist-date-to').on('pickmeup-change change', function () {
            if ((input_validator.validate($(this).val(), ['date']) || $(this).val() === '')) {
                let parameter = this.id.includes('from') ? 'date_from' : 'date_to';
                window.location.href = jQuery.query
                    .set(parameter, $(this).val())
            }else {
                $(this).addClass('error');
            }
        });

        const worklist_selected = $.cookie("worklist_selected");
        if (worklist_selected){
            updateWorkLists(worklist_selected);
            $('.js-worklist-filter').filter('[data-worklist="'+worklist_selected+'"]').addClass('selected');
        }
    });

    $('.js-clear-dates').on('click', () => {
        $('#worklist-date-from').val(null);
        $('#worklist-date-to').val(null);

        window.location.href = '/worklist/cleardates';
    });

    $('.js-worklist-filter').click(function (e) {
        e.preventDefault();
        resetFilters();
        $(this).addClass('selected');
        updateWorkLists($(this).data('worklist'));
        $.cookie('worklist_selected', $(this).data('worklist'));
    });

    function resetFilters() {
        $('.js-worklist-filter').removeClass('selected');
    }

    function updateWorkLists(listID) {
        if (listID == 'all') {
            $('.worklist-group').show();
            $("#wl_print_selected_worklist").val("");
        } else {
            $('.worklist-group').hide();
            $('#' + listID + '-wrapper').show();
            $("#wl_print_selected_worklist").val(listID);
        }
    }

    function goPrint() {
        const v = $("#wl_print_selected_worklist").val().replace("js-worklist-","");
        const df = $("#worklist-date-from").val() === "" ? "" : "&date_from="+$("#worklist-date-from").val();
        const dt = $("#worklist-date-to").val() === "" ? "" : "&date_to="+$("#worklist-date-to").val();
        window.open("/worklist/print?list_id=" + v + df + dt, "_blank");
    }

    function autoSync(count_down){
        const $wl_ctn = $('.oe-worklists main.oe-full-main');
        const $wl_cat_ul = $('ul#js-worklist-category');
        const selected_category = $('ul#js-worklist-category a.selected').data('worklist');
        const $selected_patient = $('.js-select-patient-for-psd:checked, .work-ls-patient-all:checked');
        const $popup = $('.oe-popup-wrap.js-add-psd-popup');
        const $last_sync_time = $('.last-sync');
        init_time--;
        if(init_time === 0){
            // reset timer count
            init_time = count_down;
            $.get(
                '/worklist/AutoRefresh',
                {
                    date_from: $('#worklist-date-from').val(),
                    date_to: $('#worklist-date-to').val(),
                },
                function(resp){
                    if(!resp){
                        return;
                    }
                    $wl_ctn.html(resp['main']);
                    $wl_cat_ul.html(resp['filter']);
                    if($popup.is(":hidden")){
                        $popup.html(resp['popup']);
                    }
                    $selected_patient.each(function(index, item){
                        const table_selector = `table[id=js-worklist-${$(item).data('table-id')}]`;
                        $wl_ctn.find(`${table_selector} .js-select-patient-for-psd[value="${$(item).val()}"], ${table_selector} .work-ls-patient-all[value="${$(item).val()}"]`).prop('checked', true);
                    });
                    $('.patient-popup-worklist').remove();
                    
                    $('.js-select-patient-for-psd').trigger('change');
                    $wl_cat_ul.find(`a[data-worklist="${selected_category}"]`).trigger('click');
                    $last_sync_time.text(resp['refresh_time']);
                }
            );
        }
    }
    // init global timer count
    let init_time = '<?=$sync_interval_value?>';

    $(document).ready(function () {
        $('body').on('click', '.collapse-data-header-icon', function () {
            $(this).toggleClass('collapse expand');
            $(this).next('div').toggle();
        });

        // init timer obj
        let autorefresh_countdown = null;
        if(init_time !== 'off'){
            // if auto sync is not set to off, turn on the timer
            autorefresh_countdown = setInterval(autoSync.bind(null, init_time), 1000);
        }

        let $sync_btn = $('#js-sync-btn');
        let $sync_data = $('#js-sync-data');
        let $sync_opts = $('#js-sync-options');
        $sync_data.off('mouseenter').on('mouseenter', function(){
            $sync_btn.addClass('active');
            $sync_opts.show();
        });
        $sync_data.off('mouseleave').on('mouseleave', function(){
            $sync_btn.removeClass('active');
            $sync_opts.hide();
        });
        $sync_opts.off('click', 'ul button').on('click', 'ul button', function(){
            let selected_key = $(this).data('value-key');
            let selected_value = $(this).data('value');
            // send ajax call to save user's auto sync setting
            $.ajax({
                'type': 'GET',
                'url': "<?= \Yii::app()->createUrl('/profile/changeWorklistSyncInterval') ?>",
                'data': {
                    'sync_interval': selected_key,
                    'key': '<?=$sync_interval_setting_key?>',
                }
            });
            if(selected_key === 'off'){
                $sync_btn.removeClass('on');
                // turn off the timer
                clearInterval(autorefresh_countdown);
            } else {
                $sync_btn.addClass('on');
                // avoid duplicate timers
                if(autorefresh_countdown){
                    clearInterval(autorefresh_countdown);
                }
                init_time = selected_key;
                autorefresh_countdown = setInterval(autoSync.bind(null, selected_key), 1000);
            }
            $sync_btn.find('.sync-interval').text(selected_value);
            autoSync(selected_key);
        });
    })
</script>
