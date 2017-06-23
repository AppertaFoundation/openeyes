<aside class="column sidebar episodes-and-events">
    <div class="oe-scroll-wrapper" style="height:300px">
        <div class="all-panels"></div>
    </div>
    <div class="show-scroll-tip">scroll down</div>
    <div class="scroll-blue-top" style="display:none;"></div>
</aside>


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

