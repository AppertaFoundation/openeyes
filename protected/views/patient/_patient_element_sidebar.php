<!-- event-header -->
<!-- examination event has a search facility for Left and Right Eye in edit mode -->
<?php $this->widget('application.widgets.IndexSearch', array('event_type' => $event_name)); ?>


<nav class="sidebar " id="episodes-and-events">

</nav>

<script type="text/javascript">
  new OpenEyes.UI.Sidebar(
      $('#episodes-and-events')
    );

    $(document).ready(function() {
        event_sidebar = new OpenEyes.UI.PatientSidebar($('#episodes-and-events'), {
            patient_sidebar_json: '<?php echo $this->getElementTree() ?>',
            tree_id: 'patient-sidebar-elements'
            <?php if ($this->event->id) {
                ?>,
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
