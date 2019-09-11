var analytics_dataCenter = (function(){
  // ajax url
  var ajaxURL = null;
  function setAjaxURL(val){
    ajaxURL = val;
  }
  function getAjaxURL(){
    return ajaxURL;
  }


  // for specialty ajax call
  // to get service data and clinical data
  var responseData = null;
  function setResponseData(val){
    if(val['data']){
      responseData = val['data'];
      if(responseData['clinical_data']){
        setClinicalData(responseData['clinical_data']);
      }
      if(responseData['service_data']){
        setServiceData(responseData['service_data']);
      }
      if(responseData['current_user']){
        setCurrentUser(responseData['current_user']);
      }
      if(responseData['user_list']){
        setSidebarUser(responseData['user_list']);
      }
    }
  }
  function getResponseData(){
    return responseData;
  }
  // clinical data
  var clinicalData = null;
  function setClinicalData(val){
    clinicalData = val;
  }
  function getClinicalData(){
    return clinicalData;
  }
  // service data
  var serviceData = null;
  function setServiceData(val){
    serviceData = val;
  }
  function getServiceData(){
    return serviceData;
  }
  // custom data
  var customData = null;
  function setCustomData(val){
    customData = val;
  }
  function getCustomData(){
    return customData;
  }
  // sidebar user data
  var sidebar_user = null;
  function setSidebarUser(val){
    sidebar_user = val;
  }
  function getSidebarUser(){
    return sidebar_user;
  }
  // current user data
  var current_user = null;
  function setCurrentUser(val){
    current_user = val;
  }
  function getCurrentUser(){
    return current_user;
  }
  return {
    specialtyData: {
      setResponseData: setResponseData,
      getResponseData: getResponseData,
    },
    clinical: {
      setClinicalData: setClinicalData,
      getClinicalData: getClinicalData,
    },
    service: {
      setServiceData: setServiceData,
      getServiceData: getServiceData,
    },
    custom: {
      setCustomData: setCustomData,
      getCustomData: getCustomData,
    },
    user: {
      setSidebarUser: setSidebarUser,
      getSidebarUser: getSidebarUser,
      setCurrentUser: setCurrentUser,
      getCurrentUser: getCurrentUser,
    },
    ajax: {
      setAjaxURL: setAjaxURL,
      getAjaxURL: getAjaxURL,
    }
  };
})();