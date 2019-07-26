/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {
    function PlansProblemsController() {
        this.initialiseTriggers();
    };

    PlansProblemsController.prototype.initialiseTriggers = function () {
        let controller = this;

        $(document).ready(function() {
            let $currentDialog;

            $('.problems-plans-sortable').sortable({
                update: function() {
                    // after plans are sorted, save their display_order
                    controller.savePlans($(this).find('li .remove-circle'));
                }
            });

            // remove the plan
            $('.problems-plans').on('click', 'ul li .remove-circle', function () {
                // create popup dialog to ask user if he is sure he wants to remove the plan
                $currentDialog = new OpenEyes.UI.Dialog({
                    content: '<button class="button hint green" data-plan-id="'+ $(this).data('plan-id')+'">YES</button>&nbsp;&nbsp;' +
                             '<button class="button hint red">NO</button>',
                    title: "Are you sure you want to remove this plan?",
                });
                $currentDialog.open();
            });


            // handle popup dialog buttons
            $(document).on('click', '.oe-popup button', function () {
                if ($(this).text() === "YES") {
                    $.ajax({
                        'url': '/patient/deactivatePlansProblems',
                        'type': 'GET',
                        'data': {"plan_id": $(this).data('plan-id'), "patient_id":$('#oe-patient-details').data('patient-id')},
                        'dataType': 'json',
                        'success': function ($data) {
                            controller.updateProblemsPlansList($data);
                        },
                        'error': function (msg) {
                            alert("Could not save the plans. Return message: " + msg);
                        }
                    });
                }
                $currentDialog.close();
            });


            // add a new plan and save them all
            $('.js-add-pp-btn').click(function () {
                let $input = $(this).closest('.problems-plans').find('.create-problem-plan');

                if (!$input.val().length) {
                    return;
                }

                controller.savePlans($(this).closest('.problems-plans').find('ul li .remove-circle'), $input);
            });
        });
    };

    PlansProblemsController.prototype.savePlans = function(closeButtons, $input = false) {
        let controller = this;
        let planIds = [];
        let newPlan;
        for (let i = 1; i <= closeButtons.length; i++) {
            let planId = $(closeButtons[i]).data('planId');
            if (planId) {
                planIds[i] = planId;
            }
        }

        if($input){
            newPlan = $input.val();
        }

        $.ajax({
            'url': '/patient/updatePlansProblems',
            'type': 'POST',
            'data': {"YII_CSRF_TOKEN": YII_CSRF_TOKEN, "plan_ids": planIds, "new_plan":newPlan, "patient_id":$('#oe-patient-details').data('patient-id')},
            'dataType': 'json',
            'success': function ($data) {
                controller.updateProblemsPlansList($data);
                $input.val('');
            },
            'error': function (msg) {
                alert("Could not save the plans. Return message: " + msg.responseText);
            }
        });
    };

    PlansProblemsController.prototype.updateProblemsPlansList = function(allPlans) {
        let $ul =  $('.problems-plans').find('ul');
        let template = `<li>
                <span class="drag-handle" style="position: absolute;"><i class="oe-i menu medium pro-theme"></i></span>
                <span style="display:inline-block;margin-left:30px;">{{name}}</span>
                <div class="metadata">
                    <i class="oe-i info small pro-theme js-has-tooltip" data-tooltip-content="{{title}}<br /> Created: {{create_at}}"></i>
                </div>
                <div class="remove"><i class="oe-i remove-circle small pro-theme pad" data-plan-id="{{id}}"></i></div>
            </li>`
        $ul.empty();

        for (let i = 0; i < allPlans.length; i++) {
            let $li = Mustache.render(template, {name:allPlans[i].name, title:allPlans[i].title, create_at:allPlans[i].create_at, id:allPlans[i].id});
            $ul.append($li);
        }
    }

    exports.PlansProblemsController = PlansProblemsController;
})(OpenEyes.UI);