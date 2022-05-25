<!-- event-header -->
<!-- examination event has a search facility for Left and Right Eye in edit mode -->
<?php
    // cache the search index panel for 1 day.
    $cache_key = 'IndexSearch_' . $event_name;
if ($this->beginCache($cache_key, array('duration' => 86400))) {
    $this->widget('application.widgets.IndexSearch', array('event_type' => $event_name));
    $this->endCache();
} else {
    // Script files don't get registed when the cache is loaded,
    // so the widget's JS file must be registered manually
    Yii::app()->getAssetManager()->registerScriptFile('js/IndexSearch.js', 'application.widgets');
}
?>


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
