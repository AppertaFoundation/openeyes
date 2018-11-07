
/* Module-specific javascript can be placed here */


$(document).ready(function () {
    $('#et_save').unbind('click').click(function () {
        if (!$(this).hasClass('inactive')) {
            disableButtons();
            return true;
        }
        return false;
    });

    $('table.procedures').on('click', 'i.trash', function () {
        $(this).closest('tr').remove();
    });

    $('#et_cancel').unbind('click').click(function () {
        if (!$(this).hasClass('inactive')) {
            disableButtons();

            if (m = window.location.href.match(/\/update\/[0-9]+/)) {
                window.location.href = window.location.href.replace('/update/', '/view/');
            } else {
                window.location.href = baseUrl + '/patient/episodes/' + et_patient_id;
            }
        }
        return false;
    });

    $('#et_deleteevent').unbind('click').click(function () {
        if (!$(this).hasClass('inactive')) {
            disableButtons();
            return true;
        }
        return false;
    });

    handleButton($('#et_print'), function (e) {
        printIFrameUrl(OE_print_url, null);
        enableButtons();
        e.preventDefault();
    });

    $('select.populate_textarea').unbind('change').change(function () {
        if ($(this).val() != '') {
            var cLass = $(this).parent().parent().parent().attr('class').match(/Element.*/);
            var el = $('#' + cLass + '_' + $(this).attr('id'));
            var currentText = el.text();
            var newText = $(this).children('option:selected').text();

            if (currentText.length == 0) {
                el.text(ucfirst(newText));
            } else {
                el.text(currentText + ', ' + newText);
            }
        }
    });

    // populate laser drop down when the site is changed
    $(this).delegate('#Element_OphTrLaser_Site_site_id', 'change', function (e) {
        populateLaserList($(this).val());
    });
    $(this).delegate('#Element_OphTrLaser_Site_laser_id', 'focus', function (e) {
        options = false;
        $('#Element_OphTrLaser_Site_laser_id option').each(function () {
            if ($(this).val().length) {
                options = true;
                return false;
            }
        });
        if (!options) {
            $('#laser_select_hint').slideDown('fast');
        }
    });
    if ($('#Element_OphTrLaser_Site_site_id').length) {
        initSiteSelector();
    }
});

function initSiteSelector() {
    // if site contains lasers
    if (typeof lasers_available !== 'undefined' && lasers_available) {
        var site_selector = $('#Element_OphTrLaser_Site_site_id');
        // set the site
        site_selector.val(site_id);
        // trigger change on the site, to populate the lasers selector
        site_selector.change();
    }
}

function ucfirst(str) {
    str += '';
    var f = str.charAt(0).toUpperCase();
    return f + str.substr(1);
}

function eDparameterListener(_drawing) {
    if (_drawing.selectedDoodle != null) {
        // handle event
    }
}

function populateLaserList(siteId) {
    // reset list
    $('#Element_OphTrLaser_Site_laser_id option').each(function () {
        if ($(this).val().length) {
            $(this).remove();
        }
    });
    // now populate
    if (siteId && lasersBySite[siteId]) {
        for (var i = 0; i < lasersBySite[siteId].length; i++) {
            $('#Element_OphTrLaser_Site_laser_id').append('<option value="' + lasersBySite[siteId][i]['id'] + '" >' + lasersBySite[siteId][i]['name'] + '</option>');
        }
        $('#laser_select_hint').slideUp('fast');
    }
}