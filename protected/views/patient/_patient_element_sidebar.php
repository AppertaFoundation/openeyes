<aside class="column sidebar episodes-and-events">
    <div class="oe-scroll-wrapper" style="height:2000px">
        <div class="all-panels"></div>
    </div>

</aside>


<script type="text/javascript">
    new OpenEyes.UI.Sidebar(
      $('.sidebar .oe-scroll-wrapper')
    );

    $(document).ready(function() {
        event_sidebar = new OpenEyes.UI.PatientSidebar($('aside.episodes-and-events'), {
            patient_sidebar_json: '<?php echo $this->getElementTree() ?>',
            tree_id: 'patient-sidebar-elements'
            <?php if ($this->event->id) {?>,
            event_id: <?= $this->event->id ?>
            <?php } ?>
        });
    });

</script>

<style>
    .oe-event-sidebar-edit a.error {
        background-color: #bf4040;
        color: #fff;
    }
</style>
