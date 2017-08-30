<div class="row field-row">
    <div  class="large-2 column">
        <label>Attachments:</label>
    </div>
    <div class="large-10 column end">
        <table id="correspondence_attachments_table">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Event Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(empty($patient)){
                    $patient = $this->patient;
                }
                foreach($associated_content as $key => $ac){
                    $method = $ac->initMethod->method;
                    $last_event = json_decode( $api->{$method}( $patient ));
                ?>
                <tr>
                    <td><?= $ac->display_title ?></td>
                    <td>
                        <?php
                        if( empty($last_event) ){
                            echo "None";
                        } else {
                        ?>
                            <input type="hidden" name="attachments_event_id[<?= $key ?>]" value="<?= $last_event->id ?>" />
                            <input type="hidden" name="attachments_init_method_id[<?= $key ?>]" value="<?= $ac->init_method_id ?>" />
                            <input type="hidden" name="attachments_system_hidden[<?= $key ?>]" value="<?= $ac->is_system_hidden ?>" />
                            <input type="hidden" name="attachments_print_appended[<?= $key ?>]" value="<?= $ac->is_print_appended ?>" />
                            <input type="hidden" name="attachments_short_code[<?= $key ?>]" value="<?= $ac->short_code ?>" />
                            <?= $last_event->event_date ?>
                        <?php } ?>
                    </td>
                    <td>
                        <button class="button small warning remove">remove</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
