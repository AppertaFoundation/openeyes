<script type="text/javascript">

// Add the manage element button to the sidebar
$(document).ready(function() {
    let sidebar_header = $(document.getElementById("manage-elements-sidebar"));
    let old_sidebar_header = document.getElementById("add-event-sidebar");
    if (old_sidebar_header) {
        old_sidebar_header.remove();
    }

    let node = document.createElement("div");
    node.classList.add("button", "green", "manage-elements");
    node.setAttribute("id", "js-manage-elements-btn");
    node.setAttribute("data-test", "manage-elements-btn");
    let ele = document.createTextNode("Manage Elements");
    node.appendChild(ele);
    sidebar_header.append(node);

    manage_elements = new OpenEyes.UI.ManageElements(
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