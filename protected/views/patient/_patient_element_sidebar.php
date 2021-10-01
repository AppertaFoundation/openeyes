<!-- event-header -->
<!-- examination event has a search facility for Left and Right Eye in edit mode -->
<?php $this->widget('application.widgets.IndexSearch', array('event_type' => $event_name)); ?>

<?php $this->renderPartial('//patient/episodes_sidebar'); ?>

<nav class="sidebar-header" id="manage-elements-sidebar">
    <div id="js-element-structure-btn" class="icon-button element-structure"></div>
</nav>

<nav class="sidebar element-overlay" id="episodes-and-events" style="display: none">

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
        
        let manage_btn = document.getElementById("js-element-structure-btn");
        let element_dropDown = document.getElementById("episodes-and-events"); 
        manage_btn.onclick = function() {
            manage_btn.classList.toggle('selected');
            if(element_dropDown.style.display == "none") {
                element_dropDown.style.display = "block";
            }
            else {
                element_dropDown.style.display = "none";
            }
        }

    });

</script>

<style>
    .oe-event-sidebar-edit a.error {
        background-color: #bf4040;
        color: #fff;
    }
    
    li[id^='side-element-'] {
        padding: 0px 0px 0px 0px;
        min-height: 0px;
        background-color: transparent;
    }
</style>
