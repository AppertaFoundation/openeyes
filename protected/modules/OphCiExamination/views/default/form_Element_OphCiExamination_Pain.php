<?php
    $element_model_name = \CHtml::modelName($element);
?>

<div class="element-fields full-width">
    <!-- Chronologically sorted pain recordings -->
    <div class="cols-11">
        <table id="pain-entries-table" class="cols-full">
            <colgroup>
                <col class="cols-icon" span="12">
                <col class="cols-1"><!-- crying icon -->
            </colgroup>
            <thead>
                <tr>
                    <th></th>
                    <th class="center">0</th>
                    <th class="center">1</th>
                    <th class="center">2</th>
                    <th class="center">3</th>
                    <th class="center">4</th>
                    <th class="center">5</th>
                    <th class="center">6</th>
                    <th class="center">7</th>
                    <th class="center">8</th>
                    <th class="center">9</th>
                    <th class="center">10</th>
                    <th></th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Comments</th>
                    <th><!--trash, edit only --></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $entries = $element->entries;

                $row_count = 0;

                foreach ($entries as $entry) {
                    $entry = $entry->attributes;

                    $entry_editable = $entry['element_id'] === $element->id;

                    $this->renderPartial(
                        'form_Element_OphCiExamination_Pain_Entry',
                        array('model' => $entry, 'element' => $element, 'row_count' => $row_count, 'editable' => $entry_editable)
                    );
                    $row_count++;
                }
                ?>
            </tbody>
        </table>
    </div>
    <hr class="divider" style="clear: both"></hr>
    <!-- add a pain scale reading -->
    <div class="cols-11 flex">
        <table class="cols-full">
            <colgroup>
                <col class="cols-icon">
                <col style="width:440px;">
                <col class="cols-1"><!-- crying icon -->
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <i class="oe-i happy medium no-click"></i></td>
                <td class="center">
                    <fieldset id="pain-score-radio-group">
                        <label class="highlight inline"><input value="0" name="pain-score" type="radio"> 0</label>
                        <label class="highlight inline"><input value="1" name="pain-score" type="radio"> 1</label>
                        <label class="highlight inline"><input value="2" name="pain-score" type="radio"> 2</label>
                        <label class="highlight inline"><input value="3" name="pain-score" type="radio"> 3</label>
                        <label class="highlight inline"><input value="4" name="pain-score" type="radio"> 4</label>
                        <label class="highlight inline"><input value="5" name="pain-score" type="radio"> 5</label>
                        <label class="highlight inline"><input value="6" name="pain-score" type="radio"> 6</label>
                        <label class="highlight inline"><input value="7" name="pain-score" type="radio"> 7</label>
                        <label class="highlight inline"><input value="8" name="pain-score" type="radio"> 8</label>
                        <label class="highlight inline"><input value="9" name="pain-score" type="radio"> 9</label>
                        <label class="highlight inline"><input value="10" name="pain-score" type="radio"> 10</label>
                    </fieldset>
                </td>
                <td><i class="oe-i crying medium no-click"></i></td>
                <td><input id="pain-date-field" class="date"></td>
                <td><input id="pain-time-field" type="time" class="fixed-width-medium"></td>
                <td>
                    <div class="cols-full">
                        <button id="pain-show-comment-button" class="button js-add-comments" type="button" data-comment-container="#pain-comment-container">
                            <i class="oe-i comments small-icon "></i>
                        </button> <!-- comments wrapper -->
                        <div class="cols-full comment-container" style="display: block;">
                            <!-- comment-group, textarea + icon -->
                            <div id="pain-comment-container"  class="flex-layout flex-left js-comment-container" style="display: none;" data-comment-button="#pain-show-comment-button">
                                <textarea id="pain-comment-field" placeholder="Comments" autocomplete="off" rows="1" class="js-comment-field cols-full" style=""></textarea>
                                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                            </div>
                        </div>
                    </div>
                </td>
                <td><button id="add-new-pain-row-btn" type="button" class="adder js-add-select-btn"></button></td>
            </tr>
            </tbody>
        </table>
    </div>
    <input id="pain-ids-to-delete" type="hidden" name="pain_ids_to_delete" value="[]">
</div>
<script type="x-tmpl-mustache" id="add-new-pain-row-template">
<?php $model_name = CHtml::modelName(\OEModule\OphCiExamination\models\Element_OphCiExamination_Pain::model());
        $template_model_name = $model_name . '[entries][{{row_count}}]'; ?>
  <tr>
    <td>
        <i class="oe-i happy medium no-click"></i>
    </td>
        <?php
        for ($i = 0; $i <= 10; $i++) {
            echo "<td class=\"center\"><span id=\"pain-entry-row-{{row_count}}-score-$i\" class=\"dot-list-divider fade\"></span></td>";
        }
        ?>
    <td>
        <i class="oe-i crying medium no-click"></i>
    </td>
    <td>
        <?= CHtml::hiddenField($template_model_name . '[datetime]', '{{datetime}}'); ?>
        <span class="oe-date">
            <span class="day">{{day}}</span>
            <span class="mth">{{month}}</span>
            <span class="yr">{{year}}</span>
        </span>
    </td>
    <td>
        <small>at</small> {{time}}
    </td>
    <td>
        <i class="oe-i comments-who small pad-right js-has-tooltip"
           {{comment_icon_display}}
           data-tt-type="basic"
           data-tooltip-content="<small>User comment by </small><br/>{{created_user_name}}">
        </i>
        <?= CHtml::hiddenField($template_model_name . '[comment]', '{{comment}}'); ?>
        <span class="user-comment">{{comment}}</span>
    </td>
    <td>
        <i class="oe-i pain-trash trash"></i>
    </td>
</tr>
</script>
<script type="text/javascript">
    function attachTrashEvents($object){
        $object.click(function() {
            let $to_delete_input = $('input#pain-ids-to-delete');
            let to_delete_array = JSON.parse($to_delete_input.val());
            let $row = $(this).parent().parent();
            let record_id = $row.children('input.pain-entry-id').val();
            //Only add ID to remove if the record has one, otherwise don't
            if (record_id !== undefined){
                to_delete_array.push(record_id);
                $to_delete_input.val(JSON.stringify(to_delete_array));
            }
            $row.remove();
        });
    }

    $(document).ready(function () {
        let currentTime = new Date();
        $('#pain-time-field').val(`${currentTime.getHours()}:${currentTime.getMinutes()}`);

        let row_count = <?= $row_count ?>;

        pickmeup('#pain-date-field', {
            format: 'd b Y',
        });

        attachTrashEvents($('i.pain-trash'));

        $('button#add-new-pain-row-btn').click(function() {
            let selected_pain_radio = $('fieldset#pain-score-radio-group > label > input:radio:checked');

            if (selected_pain_radio.length === 1) {
                let pain_score = selected_pain_radio.val();

                let $pain_table_body = $('table#pain-entries-table > tbody');

                $pain_table_body.append(function() {
                    let fulldate = $('#pain-date-field').val();
                    let time = $('#pain-time-field').val();

                    let datetime = new Date(`${fulldate} ${time}`);

                    //passed individually because we can't do any processing in the template/php-land
                    let day = datetime.getDate().toString();
                    let month = (datetime.getMonth() + 1).toString();//Months in js are 0 based. Everything else is 1 based.
                    let month_string = datetime.toLocaleString('default', { month: 'short' });
                    let year = datetime.getFullYear().toString();

                    //Suprised we don't have a library for this.
                    let month_padded = month.padStart(2, '0');
                    let day_padded = day.padStart(2, '0');
                    let hours_padded = datetime.getHours().toString().padStart(2, '0');
                    let minutes_padded = datetime.getMinutes().toString().padStart(2, '0');
                    let datetimeString = `${year}-${month_padded}-${day_padded} ${hours_padded}:${minutes_padded}:00`;

                    let created_user_name = window.user_full_name;

                    let $comment_field = $('textarea#pain-comment-field');

                    let comment = $comment_field.val();

                    $comment_field.val("");

                    let comment_icon_display = "";

                    if (comment === "") {
                        comment_icon_display = 'style=display:none;';
                    }

                    let data = {
                        'row_count': row_count,
                        'pain_score': pain_score,
                        'datetime': datetimeString,
                        'day': day,
                        'month': month_string,
                        'year': year,
                        'time': time,
                        'created_user_name': created_user_name,
                        'comment': comment,
                        'comment_icon_display': comment_icon_display,
                    };

                    return Mustache.render($('#add-new-pain-row-template').text(), data);
                });

                let $last_pain_row = $pain_table_body.children('tr').last();
                let $pain_span = $(`#pain-entry-row-${row_count}-score-${pain_score}`);

                let pain_color = 'good';
                if (pain_score <= 3) {
                    pain_color = 'good';
                } else if (pain_score <= 7) {
                    pain_color = 'issue';
                } else {
                    pain_color = 'warning';
                }

                $pain_span.removeClass();
                $pain_span.addClass('highlighter');
                $pain_span.addClass(pain_color);
                $pain_span.text(pain_score);

                $pain_span.parent().append(function() {
                    return $(`<input type="hidden" name="<?= $model_name ?>[entries][${row_count}][pain_score]" value="${pain_score}">`);
                });

                attachTrashEvents($last_pain_row.find('i.pain-trash'));

                row_count++;
            }
        });
    });
</script>