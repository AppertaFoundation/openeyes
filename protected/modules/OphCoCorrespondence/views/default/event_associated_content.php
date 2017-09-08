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
                        $last_event = json_decode( $api->{$method}( $patient ));
                        if($last_event){
                            $event_name = $last_event->event_name;
                            $event_date = $last_event->event_date;
                        }

                    } else {
                        $event = Event::model()->findByPk( $ac->associated_event_id );
                        $event_name = $event->eventType->name;
                        $event_date = Helper::convertDate2NHS($event->event_date);
                    }



                ?>
                <tr data-key = "<?= $value->id ?>" data-id="<?= $key ?>">
                    <td><?= $event_name ?></td>
                    <td><?= (isset($ac->display_title) ? $ac->display_title : $event->eventType->name); ?></td>
                    <td>
                        <?php
                        if( empty($last_event) ){
                            echo "None";
                        } else {
                        ?>
                            <input type="hidden" name="attachments_event_id[<?= $key ?>]" value="<?= $last_event->id ?>" />
                            <input type="hidden" name="attachments_id[<?= $key ?>]" value="<?= $ac->id ?>" />
                            <input type="hidden" name="attachments_system_hidden[<?= $key ?>]" value="<?= $ac->is_system_hidden ?>" />
                            <input type="hidden" name="attachments_print_appended[<?= $key ?>]" value="<?= $ac->is_print_appended ?>" />
                            <input type="hidden" name="attachments_short_code[<?= $key ?>]" value="<?= $ac->short_code ?>" />
                            <?= $event_date ?>
                        <?php } ?>
                    </td>
                    <td>
                        <button class="button small warning remove">remove</button>
                    </td>
                </tr>
                <?php } ?>
                <tr id="correspondence_attachments_table_last_row" data-id="<?= $key+1 ?>">
                    <td colspan="2"><td>
                    <td>
                        <?php
                        $current_episode = $patient->getEpisodeForCurrentSubspecialty();

                        $criteria = new CDbCriteria();
                        $criteria->with = array("eventType"=>array("select"=>"name"));
                        $criteria->compare('t.episode_id', $current_episode->id);
                        $criteria->order = 't.event_date desc, t.created_date desc';

                        $events = Event::model()->findAll($criteria);
                        ?>
                    <?= CHtml::dropDownList(
                            'description',
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
