function updateProblemsPlansList(allPlans) {
    let $ul =  $('.problems-plans').find('ul');
    $ul.empty();

    for (let i = 0; i < allPlans.length; i++) {
        let $li =
            '<li>' +
            '<span class="drag-handle"><i class="oe-i menu medium pro-theme"></i></span>' + allPlans[i].name +
            '<div class="metadata">' +
            '<i class="oe-i info small pro-theme js-has-tooltip" data-tooltip-content="' + allPlans[i].title + '"></i>' +
            '</div>' +
            '<div class="remove"><i class="oe-i remove-circle small pro-theme pad" data-plan-id="' + allPlans[i].id + '"></i></div>' +
            '</li>';
        $ul.append($li);
    }
}

function savePlans(closeButtons) {
    let planIds = new Map();
    let newPlans = new Map();
    for (let i = 0; i < closeButtons.length; i++) {
        let planId = $(closeButtons[i]).data('planId');
        if (planId) {
            planIds.set(i+1, planId);
        } else {
            let newPlan = $(closeButtons[i]).closest('li').clone().children().remove().end().text();
            newPlans.set(i+1, newPlan);
        }
    }

    $.ajax({
        'url': '/patient/updatePlansProblems?plan_ids=' + JSON.stringify([...planIds]) +
            '&new_plans=' + JSON.stringify([...newPlans]) +
            '&patient_id=' + $('#oe-patient-details').data('patient-id'),
        'success': function ($data) {
            updateProblemsPlansList(JSON.parse($data));
        },
        'fail': function (msg) {
            alert("Could not save the plans. Return message: " + msg);
        }
    });
}

$(document).ready(function() {
    let $currentDialog;

    $('.problems-plans-sortable').sortable({
        update: function() {
            // after plans are sorted, save their display_order
            savePlans($(this).find('li .remove-circle'));
        }
    });

    // remove the plan
    $(document).on('click', '.problems-plans ul li .remove-circle', function () {
        // create popup dialog to ask user if he is sure he wants to remove the plan
        $currentDialog = new OpenEyes.UI.Dialog({
            content: '<button class="button hint green" data-plan-id="'+ $(this).data('plan-id')+'">YES</button>' +
                     '<button class="button hint red">NO</button>',
            title: "Are you sure you want to remove this plan?",
        });
        $currentDialog.open();
    });


    // handle popup dialog buttons
    $(document).on('click', '.oe-popup button', function () {
        if ($(this).text() === "YES") {
            $.ajax({
                'url': '/patient/deactivatePlansProblems?' +
                    'plan_id=' + $(this).data('plan-id') +
                    '&patient_id=' + $('#oe-patient-details').data('patient-id'),
                'success': function ($data) {
                    updateProblemsPlansList(JSON.parse($data));
                },
            });
        }
        $currentDialog.close();
    });


    // add a new plan and save them all
    $('.js-add-pp-btn').click(function () {
        let $ul =  $('.problems-plans').find('ul');
        let $input = $(this).closest('.problems-plans').find('.create-problem-plan');

        if (!$input.val().length) {
            return;
        }

        let $li = '<li>' +
            '<span class="drag-handle"><i class="oe-i menu medium pro-theme"></i></span>' + $input.val() +
            '<div class="remove"><i class="oe-i remove-circle small pro-theme pad"></i></div>' +
            '</li>';

        $input.val('');
        $ul.append($li);

        savePlans($(this).closest('.problems-plans').find('ul li .remove-circle'));
    });
});
