<script type="text/javascript">

    $(document).ready(function() {
        new OpenEyes.UI.PatientSidebar($('aside.episodes-and-events'), {
            patient_sidebar_json: '<?php echo $this->getElementTree() ?>',
            tree_id: 'patient-sidebar-elements'
        });
    });

</script>

<style>
    .oe-event-sidebar-edit a.error {
        color: #c90000;
    }
</style>

