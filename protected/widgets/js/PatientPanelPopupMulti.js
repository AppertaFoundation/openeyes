var PatientPanel = PatientPanel || {};
let allPopupBtns={};
PatientPanel.patientPopups = {
    init: function (parentElement, patientId) {
        if(!parentElement){
            parentElement = $(document);
        }

        if ((parentElement[0].id !== 'oe-patient-details') && $(parentElement).find('#oe-patient-details').length === 0){
            return;
        }

        // patient popups
        var quicklook = new OpenEyes.UI.NavBtnPopup('quicklook',
            parentElement.find('[data-patient-id='+patientId+'].js-oe-patient').children('.js-quicklook-btn'),
            parentElement.find('.patient-summary-quicklook[data-patient-id =' + patientId + ']'),
            { closeBtn: parentElement.find('.patient-summary-quicklook[data-patient-id ='+patientId+'] > .close-icon-btn') }
        );
        var demographics = new OpenEyes.UI.NavBtnPopup('demographics',
            parentElement.find('[data-patient-id='+patientId+'].js-oe-patient').children('.js-demographics-btn'),
            parentElement.find('.patient-popup-demographics[data-patient-id =' + patientId + ']'),
            { closeBtn: parentElement.find('.patient-popup-demographics[data-patient-id =' + patientId + '] > .close-icon-btn') }
        );
        var management = new OpenEyes.UI.NavBtnPopup('management',
            parentElement.find('[data-patient-id='+patientId+'].js-oe-patient').children('.js-management-btn'),
            parentElement.find('.patient-popup-management[data-patient-id =' + patientId + ']'),
            { closeBtn: parentElement.find('.patient-popup-management[data-patient-id =' + patientId + '] > .close-icon-btn') }
        );
        var risks = new OpenEyes.UI.NavBtnPopup('risks',
            parentElement.find('[data-patient-id='+patientId+'].js-oe-patient').children('.js-allergies-risks-btn'),
            parentElement.find('.patient-popup-allergies-risks[data-patient-id =' + patientId + ']'),
            { closeBtn: parentElement.find('.patient-popup-allergies-risks[data-patient-id =' + patientId + '] > .close-icon-btn') }
        );

        allPopupBtns[patientId] = [quicklook, demographics, management, risks];

        if (parentElement.find('.js-trials-btn')) {
            var trials = new OpenEyes.UI.NavBtnPopup('trials',
                parentElement.find('[data-patient-id='+patientId+'].js-oe-patient').children('.js-trials-btn'),
                parentElement.find('.patient-popup-trials[data-patient-id ='+patientId+']')
            );
            allPopupBtns[patientId].push(trials);
        }

        if (parentElement.find('.js-worklist-btn')) {
            var worklist = new OpenEyes.UI.NavBtnPopup('worklist',
                parentElement.find('[data-patient-id='+patientId+'].js-oe-patient').children('.js-worklist-btn'),
                parentElement.find('.patient-popup-worklist[data-patient-id ='+patientId+']')
            );
            allPopupBtns[patientId].push(worklist);
        }

        for (let pBtns in allPopupBtns[patientId]) {
            var popup = allPopupBtns[patientId][pBtns];
            popup.inGroup(this); // register group with PopupBtn
            popup.latchable = true;
            popup.useMouseEvents = true;
        }
        //five buttons inside patient panel
        this.popupBtns = allPopupBtns[patientId];
        //all buttons in the patients list
        this.allBtns = allPopupBtns;
    },

    closeAll: function () {
        for (var pId in this.allBtns) {
            this.popupBtns = this.allBtns[pId];
            for (var i in this.popupBtns) {
                var popup = this.popupBtns[i];
                popup.hide();  // close all patient popups
                if(popup.latchable) {
                    popup.unlatch();
                }
            }
        }
    },

    lockAll: function() {
        for (var pId in this.allBtns) {
            var popupBtns = this.allBtns[pId];
            for (var i in popupBtns) {
                var popup = popupBtns[i];
                popup.isLatched = true;
            }
        }
    },

    unlockAll: function() {
        for (var pId in this.allBtns) {
            var popupBtns = this.allBtns[pId];
            for (var i in popupBtns) {
                var popup = popupBtns[i];
                popup.isLatched = false;
            }
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
                $(content).css({ top: (topButton - popupHeight - iconHeight * 0.5) + 'px' });
                // 0.5 here is for solving minor anchor positioning issue when floating panel appears above the icon
            }else{
                // adjust the distance between icon and patient popups in case they are too close to each other.
                $(content).css({ top: (topButton + iconHeight * 0.75) + 'px' });
            }
        }
    },

    /**
     * This function adjusts the left of the popup to make it appear at the same horizontal position as the button.
     * @param button The ref to the button (image) that opens the popup on mouseover.
     * @param content The ref to the popup element.
     */
    adjustLeft: function(button, content) {
        // Not apply this change when the popup mode is not float
        if(popupMode === 'float') {
            var leftButton = $(button).offset().left;
            $(content).css({left: (leftButton) + 'px'});
        }
    }
};