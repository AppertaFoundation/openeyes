function removeCorrespondingListItems(listItems, name=null, planId=null) {
    for(let i = 0; i < listItems.length; i++) {
        if (planId) {
            let currentPlanId = $(listItems[i]).find('.remove-circle').data('plan-id');
            if (currentPlanId === planId) {
                $(listItems[i]).remove();
            }
        } else if (name) {
            let currentPlanName = $(listItems[i]).clone().children().remove().end().text().trim();
            if (currentPlanName === name) {
                $(listItems[i]).remove();
            }
        }
    }
}

$(document).ready(function() {
    let $currentDialog;

    $('.problems-plans-sortable').sortable();

    $(document).on('click', '.problems-plans ul li .remove-circle', function () {
        let planId = $(this).data('plan-id');

        // no id => it's not stored in the db => remove the li from ul
        if (!planId) {
            let newPlanName = $(this).closest('li').clone().children().remove().end().text().trim();
            let li = $('.problems-plans ul li');
            removeCorrespondingListItems(li, newPlanName);
            return;
        }

        $currentDialog = new OpenEyes.UI.Dialog({
            content: '<button class="button hint green" data-plan-id="'+planId+'">YES</button>' +
                     '<button class="button hint red">NO</button>',
            title: "Are you sure you want to remove this plan?",
        });
        $currentDialog.open();
    });


    $(document).on('click', '.oe-popup button', function () {
        let buttonType = $(this).text();
        let planId = $(this).data('plan-id');
        let $li = $('.problems-plans ul li .remove-circle[data-plan-id="'+planId+'"]').closest('li').first();

        if (buttonType === "YES") {
            $.ajax({
                'url': '/patient/deactivatePlansProblems?plan_id=' + planId,
                'success': function () {
                    let li = $('.problems-plans ul li');
                    removeCorrespondingListItems(li, null, planId);
                }
            });
        }
        $currentDialog.close();
    });

    $('.js-add-pp-btn').click(function () {
        // let $ul =  $(this).closest('.problems-plans').find('ul');
        let $ul =  $('.problems-plans').find('ul');
        // let $input = $(this).closest('.problems-plans').find('.create-problem-plan');
        let $input = $(this).closest('.problems-plans').find('.create-problem-plan');

        let $li = '<li>' +
            '<span class="drag-handle"><i class="oe-i menu medium pro-theme"></i></span>' + $input.val() +
            '<div class="remove"><i class="oe-i remove-circle small pro-theme pad"></i></div>' +
            '</li>';

        $input.val('');
        $ul.append($li);
    });



    $('.js-save-btn').click(function () {
        let closeButtons = $(this).closest('.problems-plans').find('ul li .remove-circle');
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
                let allPlans = JSON.parse($data);

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
            },
            'fail': function (msg) {
                alert("Could not save the plans. Return message: " + msg);
            }
        });
    });

    $(window).unload(function(){
        // TODO: NOT WORKING (page refreshes before ajax finished executing) -> use save button
        // let closeButtons = $('.problems-plans ul li .remove-circle');
        // let planIds = [];
        // for(let i = 0; i < closeButtons.length; i++) {
        //     planIds.push($(closeButtons[i]).data('planId'));
        // }
        //
        // $.ajax({
        //     'async': false, // TODO maybe??
        //     'url': '/patient/updatePlansProblems?plan_ids='+JSON.stringify(planIds),
        // });
    });
});
