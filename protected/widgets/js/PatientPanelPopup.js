var PatientPanel = PatientPanel || {};
PatientPanel.patientPopups = {
  init: function () {
    if ($('#oe-patient-details').length == 0) return;

    // patient popups
    var quicklook = new OpenEyes.UI.NavBtnPopup('quicklook', $('#js-quicklook-btn'), $('#patient-summary-quicklook'));
    var demographics = new OpenEyes.UI.NavBtnPopup('demographics', $('#js-demographics-btn'), $('#patient-popup-demographics'));
    var management = new OpenEyes.UI.NavBtnPopup('management', $('#js-management-btn'), $('#patient-popup-management'));
    var risks = new OpenEyes.UI.NavBtnPopup('risks', $('#js-allergies-risks-btn'), $('#patient-popup-allergies-risks'));

    var all = [quicklook, demographics, management, risks];

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