/**
 * Created by petergallagher on 18/03/15.
 */
var OpenEyes = OpenEyes || {};
OpenEyes.CO = OpenEyes.CO || {};

OpenEyes.CO.SiteSecretary = (function () {
    "use strict";
    var $editForm,
        $editFormTable,
        $saveRow,
        $lastEdit,
        $blankEdit,
        $targetButton,
        $targetRow,
        $loaderImage,
        saveButton = '<button class="addButton small">{{text}}</button>',
        deleteButton = '<button type="submit" form="deleteSecretaryForm" name="id" class="small" value=""><i class="oe-i trash-blue "></i></button>',
        addUrl = "/OphCoCorrespondence/admin/addSiteSecretary",
        deleteUrl = "/OphCoCorrespondence/admin/deleteSiteSecretary",
        postData = {},
        errorTmpl = '<div class="alert-box alert with-icon">' +
            '<p>Please fix the following input errors:</p>' +
            '<ul>{{#errors}}<li>{{{.}}}</li>{{/errors}}</ul>' +
            '</div>',
        $blankRow,
        $blankRowBtn;

    function addSuccess(data, status, xhr) {
        if (!data.success) {
            $editForm.parent().prepend(Mustache.render(errorTmpl, data));
        } else {
            if ($targetButton.text() === 'Add') {
                $targetButton.parent().prepend(Mustache.render(saveButton, {text: "Save"}));
                $targetButton.replaceWith(deleteButton);
                $editFormTable.children('tbody').append($blankEdit.clone());
                $targetRow.find('input[name$=\\[id\\]]').val(data.siteSecretaries[0].id);
                $targetRow.find('button[form="deleteSecretaryForm"]').val(data.siteSecretaries[0].id);
            }
        }
    }

    function deleteSuccess(data, status, xhr) {
        $targetRow.remove();
    }

    function beforeSend(jqXHR, settings) {
        $('.alert-box.alert.with-icon').remove();
        $targetButton.addClass('inactive')
            .parent()
            .append($loaderImage.show());
    }

    function afterSend(jqXHR, status) {
        $targetRow.find('button.inactive').removeClass('inactive');
        $targetRow.find('img.loader').remove();
    }

    function formEvent(e) {
        e.preventDefault();
        var url,
            successFunction;

        postData = {};
        $targetButton = $(e.target);
        $targetRow = $targetButton.parents(".secretaryFormRow");
        postData.YII_CSRF_TOKEN = $editForm.find(':input[name="YII_CSRF_TOKEN"]').val();

        if ($targetButton.hasClass('addButton')) {
            $targetRow.find(':input').serializeArray().map(function (x) {
                postData[x.name] = x.value;
            });
            url = addUrl;
            successFunction = addSuccess;
        } else {
            url = deleteUrl;
            successFunction = deleteSuccess;
            postData.id = $targetButton.val();
        }

        $.ajax(url, {
            type: "POST",
            data: postData,
            dataType: 'json',
            success: successFunction,
            beforeSend: beforeSend,
            complete: afterSend,
            error: function (jqXHR, textStatus, errorThrown) {
                new OpenEyes.UI.Dialog.Alert({
                    content: "An error occured, plese try again."
                }).open();
            }
        });
    }

    function showBlankRow(){
        $blankRow.removeClass('hidden');
        $blankRowBtn.closest('tr').addClass('hidden');
    }

    return {
        init: function () {
            $editForm = $('#editSecretaryForm');
            $editFormTable = $editForm.children('table');
            $saveRow = $editFormTable.find('tr:last');
            $lastEdit = $saveRow.prev();
            $lastEdit.find('button[form="deleteSecretaryForm"]').replaceWith(Mustache.render(saveButton, {text: "Add"}));
            $blankEdit = $lastEdit.clone();
            $loaderImage = $saveRow.find('.loader').clone();
            $blankRow = $('.js-addNewRow');
            $blankRowBtn = $('.js-showBlankRow');
            $blankRow.addClass('hidden');

            //We'll handle these with fancy JS actions now so remove them
            $saveRow.remove();
            $('button[form="deleteSecretaryForm"]').parent().prepend(Mustache.render(saveButton, {text: "Save"}));
            $editForm.on('click', 'button[form="deleteSecretaryForm"], .addButton', formEvent);
            $editForm.on('click', '.js-showBlankRow', showBlankRow);
        }
    };
})();

$(function () {
    OpenEyes.CO.SiteSecretary.init();
});
