<?php
    use \OEModule\OphCiExamination\models\Element_OphCiExamination_Safeguarding;

    $outcome_actionable =
        Yii::app()->user->checkAccess('Safeguarding') &&
        (!isset($element->outcome_id) ||
        (int)$element->outcome_id === Element_OphCiExamination_Safeguarding::FOLLOWUP_REQUIRED);
    $display_paediatric_fields = $element->under_protection_plan || $element->has_social_worker || isset($element->responsible_parent_name) || isset($element->accompanying_person_name);
    ?>
<div class="cols-11">
    <?php if ($element->no_concerns) { ?>
        <span>Patient has no safeguarding concerns.</span>
    <?php } else { ?>
        <table class="cols-full last-left">
            <colgroup><col class="cols-4"></colgroup>
            <tbody>
                <?php if ($display_paediatric_fields) { ?>
                <tr>
                    <td>Does the child have a social worker?</td>
                    <td><?= $element->has_social_worker ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                    <td>Is the child under a child protection plan?</td>
                    <td><?= $element->under_protection_plan ? 'Yes' : 'No' ?></td>
                </tr>
                <tr>
                    <td>Who is accompanying the child and their relationship?</td>
                    <td><?= $element->accompanying_person_name ?></td>
                </tr>
                <tr>
                    <td>Who has parental responsibility for the child?</td>
                    <td><?= $element->responsible_parent_name ?></td>
                </tr>
                    <?php
                }

                $entries = \OEModule\OphCiExamination\models\OphCiExamination_Safeguarding_Entry::model()->findAllByAttributes(array('element_id' => $element->id));
                $row_count = 0;

                foreach ($entries as $entry) {
                    $this->renderPartial('form_Element_OphCiExamination_Safeguarding_Entry', array('element' => $element, 'entry' => $entry, 'row_count' => $row_count++, 'editable' => false));
                }
                ?>
            </tbody>
        </table>
        <hr class="divider">
        <div class="flex-t">
            <div class="highlighter">Safeguarding outcome review</div>
            <div class="cols-2"></div>
                <div id="safeguarding-outcome-summary" class="cols-6">
                    <table class="last-left">
                        <tbody>
                            <tr>
                                <th>Status:</th>
                                <td><?= isset($element->outcome_id) ? $element->outcome->term : 'Pending' ?></td>
                            </tr>
                            <?php if (isset($element->outcome_comments) && !empty($element->outcome_comments)) { ?>
                                <tr>
                                    <th>Comment:</th>
                                    <td><?= $element->outcome_comments ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php
            if ($outcome_actionable) {
                ?>
                <div class="cols-1"></div>
                <div id="safeguarding-action-buttons" class="cols-8">
                    <table class="last-left">
                        <tbody>
                            <tr>
                                <th>Add safeguarding as Risk in record and remove from list</th>
                                <td><button id="safeguarding-confirm-concerns" type="button" class="blue hint">Confirm safeguarding concerns</button></td>
                            </tr>
                            <tr>
                                <th>More information required, record as "in progress"</th>
                                <td><button id="safeguarding-follow-up-required" type="button" class="blue hint">Follow up required</button></td>
                            </tr>
                            <tr>
                                <th>Remove from safeguarding list</th>
                                <td><button id="safeguarding-no-concerns" type="button" class="green hint">No safeguarding concerns</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div id="safeguarding-actions-clickthrough" class="cols-8" style="display: none">
                    <input id="safeguarding-outcome-id" type="hidden" name="safeguarding_outcome_id" value="">
                    Please provide a comment:
                    <input id="safeguarding-outcome-comments" type="textarea">
                    <button id="safeguarding-outcome-save" type="button">Save</button>
                </div>
                <div id="safeguarding-actions-failed" class="cols-8 alert-box error with-icon" style="display: none">
                    Please fix the following input errors:
                    <ul id="safeguarding-actions-error-list"></ul>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<?php if ($outcome_actionable) { ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#safeguarding-confirm-concerns").click(function() {
                $("#safeguarding-action-buttons").hide();
                $("#safeguarding-actions-clickthrough").show();

                $("#safeguarding-outcome-id").val(<?= Element_OphCiExamination_Safeguarding::CONFIRM_SAFEGUARDING_CONCERNS ?>);
            });

            $("#safeguarding-follow-up-required").click(function() {
                $("#safeguarding-action-buttons").hide();
                $("#safeguarding-actions-clickthrough").show();

                $("#safeguarding-outcome-id").val(<?= Element_OphCiExamination_Safeguarding::FOLLOWUP_REQUIRED ?>);
            });

            $("#safeguarding-no-concerns").click(function() {
                $("#safeguarding-action-buttons").hide();
                $("#safeguarding-actions-clickthrough").show();

                $("#safeguarding-outcome-id").val(<?= Element_OphCiExamination_Safeguarding::NO_SAFEGUARDING_CONCERNS ?>);
            });

            $("#safeguarding-outcome-save").click(function() {
                $("#safeguarding-actions-clickthrough").hide();
                $("#safeguarding-actions-failed").hide();

                let existingComment = "<?= $element->outcome_comments ?>";

                if (existingComment) {
                    existingComment = existingComment + "<br>";
                }

                $.ajax({
                    url: "../ResolveSafeguardingElement",
                    type: "POST",
                    dataType: "json",
                    async: false,
                    data: {
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN,
                        element_id: <?= $element->id ?>,
                        outcome_id: $("#safeguarding-outcome-id").val(),
                        outcome_comments: existingComment + $("#safeguarding-outcome-comments").val()
                    },
                    success: function(response) {
                        console.log(response.success);
                        if (response.success) {
                            //Prevent browser alert warning of unsaved data from opening
                            $(window).unbind('beforeunload');
                            // Reload the page without sending an additional POST request
                            window.location.replace(window.location);
                        } else {
                            //TODO: Ensure this works as intended- force a failure to handle errors
                            $("#safeguarding-actions-error-list").empty();

                            response.errors.forEach(function(error) {
                                $("#safeguarding-actions-error-list").append(function() {
                                    return `<li>${error}</li>`;
                                });
                            });

                            $("#safeguarding-actions-failed").show();
                            $("#safeguarding-action-buttons").show();
                        }
                    },
                    error: function(err) {
                       window.alert("Failed to resolve safeguarding concerns");
                    }
                });
            });
        });
    </script>
<?php } ?>