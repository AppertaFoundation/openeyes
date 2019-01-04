var PatientPanel = PatientPanel || {};
PatientPanel.patientPopups = {
  init: function (parentElement) {
    if(!parentElement){
      parentElement = $(document);
    }

    if ((parentElement[0].id !== 'oe-patient-details') && $(parentElement).find('#oe-patient-details').length === 0){
      console.log('patient popup parent not found');
      return;
    }

    // patient popups
    var quicklook = new OpenEyes.UI.NavBtnPopup('quicklook',
        parentElement.find('.js-quicklook-btn'),
        parentElement.find('.patient-summary-quicklook')
    );
    var demographics = new OpenEyes.UI.NavBtnPopup('demographics',
        parentElement.find('.js-demographics-btn'),
        parentElement.find('.patient-popup-demographics')
    );
    var management = new OpenEyes.UI.NavBtnPopup('management',
        parentElement.find('.js-management-btn'),
        parentElement.find('.patient-popup-management')
    );
    var risks = new OpenEyes.UI.NavBtnPopup('risks',
        parentElement.find('.js-allergies-risks-btn'),
        parentElement.find('.patient-popup-allergies-risks')
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
    for (i in this.popupBtns) {
      var popup = this.popupBtns[i];
      popup.isLatched = false;
    }
  }
};