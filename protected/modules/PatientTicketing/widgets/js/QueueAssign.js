$(document).ready(function(){
	if(window.location.pathname == '/patient/summary/'+OE_patient_id){
      setTimeout(function(){
        $('#add-event').trigger('click');
      },1000);
    }
});