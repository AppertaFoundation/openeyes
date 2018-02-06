<!-- event-header -->
<!-- examination event has a search facility for Left and Right Eye in edit mode -->
<nav class="sidebar-header">
  <input id="js-element-search-right" class="search right cols-6" type="text">
  <input id="js-element-search-left" class="search left cols-6" type="text">
</nav>
<!-- Examination Element Search Results Popup -->
<!--<div class="elements-search-results" id="elements-search-results" style="display: none;">-->
<!---->
<!--  <div class="close-icon-btn"><i class="oe-i remove-circle"></i></div>-->
  <!--
      ******  Following DOM follows existing DOM and class naming. *****
      ******  However, the DOM is updated for Eyedraw icons *****
      -->
<!--  <ul class="results_list">-->
<!--  </ul>-->
<!-- results_list -->
<!--</div>-->
<!-- elements-search-results -->

<nav class="sidebar " id="episodes-and-events">
  <ul class="oescape-icon-btns">
    <li class="icon-btn"><a href="#" class="inactive">AD</a></li>
    <li class="icon-btn"><a href="#" class="inactive">CO</a></li>
    <li class="icon-btn"><a href="#" class="inactive">CA</a></li>
    <li class="icon-btn"><a href="#" class="active">GL</a></li>
    <li class="icon-btn"><a href="#" class="inactive">SB</a></li>
    <li class="icon-btn"><a href="#" class="active">MR</a></li>
    <li class="icon-btn"><a href="#" class="inactive">VR</a></li>
    <li class="icon-btn"><a href="#" class="inactive">UV</a></li>
    <li class="icon-btn"><a href="#" class="inactive">RF</a></li>
    <li class="icon-btn"><a href="#" class="lightning-viewer-icon active"></li>
  </ul>
  <!-- oescape -->

</nav>


<script type="text/javascript">
  new OpenEyes.UI.Sidebar(
      $('#episodes-and-events')
    );

    $(document).ready(function() {
        event_sidebar = new OpenEyes.UI.PatientSidebar($('#episodes-and-events'), {
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
