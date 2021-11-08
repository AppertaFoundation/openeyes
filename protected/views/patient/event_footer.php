<?php
$delete_action = null;
if (isset($this->event_actions)) {
    foreach ($this->event_actions as $i => $event_action) {
        if ($event_action->label == "Delete") {
            $delete_action = $event_action;
            unset($this->event_actions[$i]);
            break;
        }
    }
}

?>
<nav class="event-footer-actions">
  <i class="spinner" title="Loading..." style="display: none;"></i>
        <?php
            $print_actions = array();
        foreach ($this->event_actions as $key => $action) {
            // for the footer ids, update them so that they donot overlap
            if (array_key_exists("id", $action->htmlOptions)) {
                $action->htmlOptions['id'] = $action->htmlOptions['id'] . "_footer";
            }
            if (isset($action->htmlOptions['name']) && strpos(strtolower($action->htmlOptions['name']), 'print') === 0) {
                $print_actions[] = $action;
            } else {
                echo $action->toHtml();
            }
        }

        if (!empty($print_actions)) {
            echo EventAction::printDropDownButtonAsHtml($print_actions);
        }

        if (isset($delete_action)) {
            echo $delete_action->toHtml();
        }
        ?>
</nav>