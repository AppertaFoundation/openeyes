<?php
use OEModule\OphCiExamination\models\Element_OphCiExamination_Pain;
/**
 * @var Element_OphCiExamination_Pain $element
 */

$model_name = \CHtml::modelName($element);
$entries = $element->entries;

$draft_data = [];
$draft_pain_score = null;
$draft_comment = null;
if(isset($this->draft)) {
    $draft_json = json_decode($this->draft->data);
    $draft_data = $draft_json->{$model_name};
    $draft_pain_score = $draft_data->{'pain-score'} ?? null;
    $draft_date_field = $draft_data->{'pain-date-field'};
    $draft_time_field = $draft_data->{'pain-time-field'};
    $entries = $draft_data->entries ?? [];
    $draft_comment = trim($draft_data->{'pain-comment-field'}) ? $draft_data->{'pain-comment-field'} : null;
}
?>

<div class="element-fields full-width">
    <!-- Chronologically sorted pain recordings -->
    <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
    <div class="cols-17">
        <table id="pain-entries-table" class="cols-full" data-test="pain-entries-table">
            <colgroup>
                <col class="cols-icon">
                <col class="cols-icon" span="11">
                <col class="cols-icon">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-icon">
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
                $row_count = 0;
                foreach ($entries as $entry) {
                    $entry_editable = isset($entry->element_id) && $element->id === $entry->element_id;

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
    <div class="cols-17">
        <table class="cols-full">
            <colgroup>
                <col class="cols-icon">
                <col class="cols-icon" span="11">
                <col class="cols-icon">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-icon">
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <i class="oe-i happy medium no-click"></i>
                </td>
                <?php for ($pain_score_value = $element::MINIMUM_PAIN_SCORE; $pain_score_value <= $element::MAXIMUM_PAIN_SCORE; $pain_score_value++) { ?>
                    <td>
                        <label class="highlight inline">
                            <input id="pain-radio" value="<?= $pain_score_value ?>" name="<?= $model_name ?>[pain-score]" type="radio"
                                <?= (isset($_POST['pain-score']) && $_POST['pain-score'] == $pain_score_value) || $draft_pain_score === "$pain_score_value" ? "checked" : "" ?>
                                data-test="pain-value-<?= $pain_score_value ?>"> <?= $pain_score_value ?>
                        </label>
                    </td>
                <?php } ?>
                <td><i class="oe-i crying medium no-click"></i></td>
                <td><input id="pain-date-field" name="<?=$model_name ?>[pain-date-field]" <?= isset($draft_date_field) ? "value='{$draft_date_field}'" : ''?> class="date"></td>
                <td><input id="pain-time-field" name="<?=$model_name ?>[pain-time-field]" <?= isset($draft_time_field) ? "value='{$draft_time_field}'" : ''?> type="time" class="fixed-width-medium"></td>
                <td>
                    <div class="cols-full">
                        <button id="pain-show-comment-button" class="button js-add-comments" type="button" style="display: <?= $draft_comment ? 'none' : ''?>;" data-comment-container="#pain-comment-container">
                            <i class="oe-i comments small-icon "></i>
                        </button> <!-- comments wrapper -->
                        <div class="cols-full comment-container" style="display: block;">
                            <!-- comment-group, textarea + icon -->
                            <div id="pain-comment-container"  class="flex-layout flex-left js-comment-container" style="display: <?= $draft_comment ? '' : 'none'?>;" data-comment-button="#pain-show-comment-button">
                                <textarea id="pain-comment-field" name="<?=$model_name ?>[pain-comment-field]" placeholder="Comments" autocomplete="off" rows="1" cols="10" class="js-comment-field cols-full"><?= $draft_comment ?></textarea>
                                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                            </div>
                        </div>
                    </div>
                </td>
                <td><button id="add-new-pain-row-btn" type="button" class="adder js-add-select-btn" data-test="pain-add-entry"></button></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script type="x-tmpl-mustache" id="add-new-pain-row-template">
<?php $template_model_name = $model_name . '[entries][{{row_count}}]'; ?>
  <tr>
    <td>
        <i class="oe-i happy medium no-click"></i>
    </td>
        <?php
        for ($pain_score_value = $element::MINIMUM_PAIN_SCORE; $pain_score_value <= $element::MAXIMUM_PAIN_SCORE; $pain_score_value++) {
            echo "<td class=\"center\"><span id=\"pain-entry-row-{{row_count}}-score-$pain_score_value\" class=\"dot-list-divider fade\"></span></td>";
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
    <td class="nowrap">
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
            let $row = $(this).parent().parent();
            $row.remove();
        });
    }

    $(document).ready(function () {
        const currentTime = new Date();
        // If this is a draft the then time will already be set and we dont want to change that back to the current time
        if (!document.getElementById('pain-time-field').value) {
            $('#pain-time-field').val(`${OpenEyes.Util.addZeroBefore(currentTime.getHours())}:${OpenEyes.Util.addZeroBefore(currentTime.getMinutes())}`);
        }

        let row_count = <?= $row_count ?>;

        pickmeup('#pain-date-field', {
            format: 'd b Y',
        });

        attachTrashEvents($('i.pain-trash'));

        $('button#add-new-pain-row-btn').click(function() {
            let selected_pain_radio = $('input#pain-radio:checked');

            timeVal = $('input#pain-time-field').val();

            if (selected_pain_radio.length === 1 && timeVal) {
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

                    let currentTime = new Date();
                    $('#pain-time-field').val(`${OpenEyes.Util.addZeroBefore(currentTime.getHours())}:${OpenEyes.Util.addZeroBefore(currentTime.getMinutes())}`);

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

                selected_pain_radio.prop('checked', false);

                row_count++;
            }
        });
    });
</script>
