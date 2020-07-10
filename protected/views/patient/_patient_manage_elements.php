<script type="text/javascript">

// Add the manage element button to the sidebar
$(document).ready(function() {
    var node = document.createElement("div");
    node.classList.add("button", "green", "manage-elements");
    node.setAttribute("id", "js-manage-elements-btn");
    var ele = document.createTextNode("Manage Elements");
    node.appendChild(ele);
    $(document.getElementsByClassName("sidebar-header")).append(node);

    new OpenEyes.UI.ManageElements(
        $('#js-manage-elements-btn'),
        $('#episodes-and-events'), {
            manage_elements_json: '<?php echo $this->getElementTree() ?>',
            tree_id: 'patient-sidebar-elements'
            <?php if ($this->event->id) {
                ?>,
            event_id: <?= $this->event->id ?>
            <?php } ?>
        }
    );
});

</script>