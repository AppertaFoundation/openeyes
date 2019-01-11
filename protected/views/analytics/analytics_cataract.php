<!--<main class="mdl-layout__content mdl-color--grey-100">-->
    <div id="pcr-risk-grid" class="analytics-cataract"></div>
    <div id="cataract-complication-grid" class="analytics-cataract"></div>
    <div id="visual-acuity-grid" class="analytics-cataract"></div>
    <div id="refractive-outcome-grid" class="analytics-cataract"></div>
<!--</main>-->
<div class="analytics-event-list" style="display: none">
    <div class="flex-layout">
        <h3 id="js-list-title"></h3>
        <a id="js-back-to-chart" class="selected" href="#">Back to chart</a>
    </div>
    <table>
        <colgroup>
            <col style="width: 100px;">
            <col style="width: 100px;">
            <col style="width: 100px;">
        </colgroup>
        <thead>
        <tr>
            <th>Event No</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($event_list as $event) { ?>
            <tr class="analytics-event-list-row clickable" id="<?=$event['event_id']?>" style="display: none;">
                <td><?= $event['event_id']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    $('.clickable').click(function () {
        var link = $(this).attr('id');
        window.location.href = '/OphTrOperationnote/default/view/' + link;
    });
</script>
