        <?php if ($this->target_class) {
            ?>
            <input type="hidden" id="target_event_class" value="<?php echo $this->target_class?>" />
            <?php
        }?>
        <h3>Select an event type:</h3>
        <select class="EventTypeModuleEventType" name="EventTypeModuleEventType">
            <option value="">Select</option>
            <?php foreach (EventType::model()->findAll(array('order' => 'name asc')) as $event_type) {
                ?>
                <option value="<?php echo $event_type->id?>"<?php if (@$_POST['EventTypeModuleEventType'] == $event_type->id) {
                    ?> selected="selected"<?php
                               }
                                ?>><?php echo $event_type->name?></option>
                <?php
            }?>
        </select>
        <div id="EventTypeModuleEventTypeData">
            <div id="EventTypeModuleEventTypeProperties">
                <?php if (@$_POST['EventTypeModuleEventType']) {
                    EventTypeModuleCode::eventTypeProperties($_POST['EventTypeModuleEventType']);
                }?>
            </div>
            <div id="EventTypeModuleEventTypeElementTypes"<?php if (!@$_POST['EventTypeModuleEventType']) {
                ?> style="display: none;"<?php
                                                          }?>>
                <h3>Describe your element types:</h3>

                <div id="elementsModifyExisting">
                    <?php foreach ($_POST as $key => $value) {
                        if (preg_match('/^elementName([0-9]+)$/', $key, $m)) {
                            echo $this->renderPartial('element', array('element_num' => $m[1]));
                        } elseif (preg_match('/^elementId([0-9]+)$/', $key, $m)) {
                            echo $this->renderPartial('elementfields', array('element_num' => $m[1]));
                        }
                    }
                    ?>
                </div>

                <input type="submit" class="add_element" name="add" value="add element" />
                <input type="submit" class="add_field"name="add_field" value="add field to element" /><br/>
                <br/>

                <div class="tooltip">
                    The name should only contain word characters and spaces.    The generated module class will be named based on the specialty, event group, and name of the event type.  EG: 'Ophthalmology', 'Treatment', and 'Operation note' will take the short codes for the specialty and event group to create <code>OphTrOperationnote</code>.
                </div>
            </div>
        </div>
