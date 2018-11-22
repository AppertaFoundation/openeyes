function toggleSection(section, reference) {

    //make the collapse content to be shown or hidden
    var toggle_switch = $(section);
    $(reference).toggle(function () {
        if ($(reference).css('display') === 'none') {
            //change the button label to be 'Show'
            toggle_switch.html($(section).attr('data-show-label'));
        } else {
            //change the button label to be 'Hide'
            toggle_switch.html($(section).attr('data-hide-label'));
        }
    });
}