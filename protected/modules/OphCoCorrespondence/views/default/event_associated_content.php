<div class="row field-row">
    <div  class="large-2 column">
        <label>Attachments:</label>
    </div>
    <div class="large-10 column end">
        <table id="correspondence_attachments_table">
            <thead>
                <tr>
                    <th>Attachment type</th>
                    <th>Title</th>
                    <th>Event Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(empty($patient)){
                    $patient = $this->patient;
                }
                $row_index = 0;
                $event_id_compare = array();

                foreach($associated_content as $key => $value){
                    $method = null;
                    $event_name = null;

                    if(isset($value->initMethod)){
                        $method = $value->initMethod->method;
                        $ac = $value;
                    } else {
                        if(isset($value->initAssociatedContent->initMethod->method)){
                            $method = $value->initAssociatedContent->initMethod->method;
                            $ac = $value->initAssociatedContent;
                        } else {
                            $ac = $value;
                        }
                    }

                    if($method != null){
                        $event = json_decode( $api->{$method}( $patient ));
                        if($event !== null){
                            $event_name = $event->event_name;
                            $event_date = $event->event_date;
                        }
                    } else {
                        $event = Event::model()->findByPk( $ac->associated_event_id );
                        if(isset($event->eventType)){
                            $event_name = $event->eventType->name;
                        } else {
                            $event_name = $event->event_name;
                        }
                        $event_date = Helper::convertDate2NHS($event->event_date);
                    }

                    if( empty($event) || ($event == null)){
                        continue;
                    }
                    $event_id_compare[] = $event->id;
                ?>
                <tr data-id="<?= $row_index ?>">
                    <?php

                    if(isset($_POST['attachments_event_id'])){ ?>

                        <input type="hidden" class="attachments_event_id" name="attachments_event_id[<?= $row_index ?>]" value="<?= $_POST['attachments_event_id'][$row_index] ?>" />
                    <?php } else if(isset($value->associated_protected_file_id)){ ?>
                        <input type="hidden" name="file_id[<?= $row_index ?>]" value="<?= $value->associated_protected_file_id ?>" />
                        <input type="hidden" class="attachments_event_id" name="attachments_event_id[<?= $row_index ?>]" value="<?= $event->id ?>" />
                    <?php }

                    if(isset($_POST['attachments_display_title'])){
                        $display_title = $_POST['attachments_display_title'][$row_index];
                    } else {
                        $display_title = (isset($ac->display_title) ? $ac->display_title : $event_name);
                    }
                    ?>

                    <td><?= $event_name ?></td>
                    <td><input type="text" class="attachments_display_title" name="attachments_display_title[<?= $row_index ?>]"   value="<?= $display_title ?>" /></td>
                    <td>
                        <input type="hidden" name="attachments_event_id[<?= $row_index ?>]" value="<?= $event->id ?>" />
                        <input type="hidden" name="attachments_id[<?= $row_index ?>]" value="<?= $ac->id ?>" />
                        <input type="hidden" name="attachments_system_hidden[<?= $row_index ?>]" value="<?= $ac->is_system_hidden ?>" />
                        <input type="hidden" name="attachments_print_appended[<?= $row_index ?>]" value="<?= $ac->is_print_appended ?>" />
                        <input type="hidden" name="attachments_short_code[<?= $row_index ?>]" value="<?= $ac->short_code ?>" />
                        <?= $event_date ?>
                    </td>
                    <td>
                        <button class="button small warning remove">remove</button>
                    </td>
                </tr>
                <?php $row_index++;
                }

                if(isset($_POST['attachments_event_id'])){

                    $posted_data = array_diff_assoc($_POST['attachments_event_id'] , $event_id_compare);
                    if(!empty($posted_data)){

                        foreach($posted_data as $pdk => $pdv){
                            $event = Event::model()->findByPk($pdv);
                            $row_index++;
                            ?>

                            <tr data-id="<?= $row_index ?>">
                                <input type="hidden" name="file_id[<?= $row_index ?>]" value="<?= $_POST['file_id'][$pdk] ?>" />
                                <input type="hidden" class="attachments_event_id" name="attachments_event_id[<?= $row_index ?>]" value="<?=  $_POST['attachments_event_id'][$pdk] ?>" />
                                <td><?= $event->eventType->name ?></td>
                                <td><input type="text" class="attachments_display_title" name="attachments_display_title[<?= $row_index ?>]"   value="<?= $_POST['attachments_display_title'][$pdk] ?>" /></td>
                                <td>
                                    <?= Helper::convertDate2NHS($event->event_date); ?>
                                </td>
                                <td>
                                    <button class="button small warning remove">remove</button>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>

                <tr id="correspondence_attachments_table_last_row" data-id="<?= $row_index ?>">
                    <td colspan="2"><td>
                    <td>
                        <?php

                        $events = $this->getAttachableEvents($patient);

                        echo CHtml::dropDownList(
                            'attachment_events',
                            ' ',
                            CHtml::listData($events,'id',function($events) {
                                return CHtml::encode($events->eventType->name. ' - '.Helper::convertDate2NHS($events->event_date));
                            }), array('empty' => '- Select -')) ;
                            ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
