var PatientPanel = PatientPanel || {};
PatientPanel.patientPopups = {
    init: function (parentElement) {
        if(!parentElement){
            parentElement = $(document);
        }

        if ((parentElement[0].id !== 'oe-patient-details') && $(parentElement).find('#oe-patient-details').length === 0){
            return;
        }

        // patient popups
        var quicklook = new OpenEyes.UI.NavBtnPopup('quicklook',
            parentElement.find('.js-quicklook-btn'),
            parentElement.find('.patient-summary-quicklook'),            
            { closeBtn: parentElement.find('.patient-summary-quicklook > .close-icon-btn') }
        );
        var demographics = new OpenEyes.UI.NavBtnPopup('demographics',
            parentElement.find('.js-demographics-btn'),
            parentElement.find('.patient-popup-demographics'),
            { closeBtn: parentElement.find('.patient-popup-demographics > .close-icon-btn') }
        );
        var management = new OpenEyes.UI.NavBtnPopup('management',
            parentElement.find('.js-management-btn'),
            parentElement.find('.patient-popup-management'),
            { closeBtn: parentElement.find('.patient-popup-management > .close-icon-btn') }
        );
        var risks = new OpenEyes.UI.NavBtnPopup('risks',
            parentElement.find('.js-allergies-risks-btn'),
            parentElement.find('.patient-popup-allergies-risks'),
            { closeBtn: parentElement.find('.patient-popup-allergies-risks > .close-icon-btn') }
        );

        var all = [quicklook, demographics, management, risks];

        if (parentElement.find('.js-trials-btn')) {
            var trials = new OpenEyes.UI.NavBtnPopup('trials',
                parentElement.find('.js-trials-btn'),
                parentElement.find('.patient-popup-trials')
            );
            all.push(trials);
        }

        for (pBtns in all) {
            var popup = all[pBtns];
            popup.inGroup(this); // register group with PopupBtn
            popup.latchable = true;
            popup.useMouseEvents = true;
        }
        this.popupBtns = all;
    },

    closeAll: function () {
        for (var i in this.popupBtns) {
            var popup = this.popupBtns[i];
            popup.hide();  // close all patient popups
            if(popup.latchable) {
                popup.unlatch();
            }
        }
    },

    lockAll: function() {
        for (var i in this.popupBtns) {
            var popup = this.popupBtns[i];
            popup.isLatched = true;
        }
    },

    unlockAll: function() {
        for (var i in this.popupBtns) {
            var popup = this.popupBtns[i];
            popup.isLatched = false;
        }
    },

    /**
     * This function checks the popup to see if it is going outside the viewport and adjusts the
     * top of the popup to make it appear above the button.
     * @param button The ref to the button (image) that opens the popup on mouseover.
     * @param content The ref to the popup element.
     */
    adjustTop: function(button, content) {
        // Not apply this change when the popup mode is not float
        if(popupMode === 'float'){
            // height of the icons (such as demographics, management, quicklook)
            var iconHeight = 35;

            var topButton = $(button).offset().top;
            var popupHeight = $(content).show().height();

            // this variable holds the total length from the top of the window to the button of the popup.
            var total = popupHeight + topButton;

            var windowHeight = $(window).height();

            if( (total + iconHeight)  > windowHeight) {
                // this property is removed when the mouse leaves the icon.
                $(content).css({ top: (topButton - popupHeight - iconHeight) + 'px' });
            }
        }
    },
};