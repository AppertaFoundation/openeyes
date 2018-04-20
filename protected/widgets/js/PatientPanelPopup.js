var PatientPanel = PatientPanel || {};
PatientPanel.patientPopups = {
  init:function(){
    if( $('#oe-patient-details').length == 0 ) return;

    // patient popups
    var quicklook 		= new OpenEyes.UI.NavBtnPopup( 'quicklook', $('#js-quicklook-btn'), $('#patient-summary-quicklook') );
    var demographics 	= new OpenEyes.UI.NavBtnPopup( 'demographics', $('#js-demographics-btn'), $('#patient-popup-demographics') );
    var demographics2 	= new OpenEyes.UI.NavBtnPopup( 'management', $('#js-management-btn'), $('#patient-popup-management') );
    var risks 			= new OpenEyes.UI.NavBtnPopup( 'risks', $('#js-allergies-risks-btn'), $('#patient-popup-allergies-risks') );


    var all = [ quicklook, demographics, demographics2, risks ];

    for( pBtns in all ) {
      all[pBtns].inGroup( this ); // register group with PopupBtn
    }

    this.popupBtns = all;

  },

  closeAll:function(){
    for( pBtns in this.popupBtns ){
      this.popupBtns[pBtns].hide();  // close all patient popups
    }
  }

};