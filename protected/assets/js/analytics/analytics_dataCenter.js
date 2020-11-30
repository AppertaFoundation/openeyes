const analytics_dataCenter = (function () {
    // ajax url
    let ajaxURL = null;

    function setAjaxURL(val) {
        ajaxURL = val;
    }

    function getAjaxURL() {
        return ajaxURL;
    }

    // for saving the search criteria in cataract page
    let ca_search_form = {};

    function setCataractSearchForm(key, val) {
        ca_search_form[key] = val;
    }

    function getCataractSearchForm() {
        return ca_search_form;
    }

    function clearCataractSearchForm() {
        ca_search_form = {};
    }

    // clinical data
    let clinicalData = null;

    function setClinicalData(val) {
        clinicalData = val;
    }

    function getClinicalData() {
        return clinicalData;
    }

    // service data
    let serviceData = null;

    function setServiceData(val) {
        serviceData = val;
    }

    function getServiceData() {
        return serviceData;
    }

    // sidebar user data
    let sidebar_user = null;

    function setSidebarUser(val) {
        sidebar_user = val;
    }

    function getSidebarUser() {
        return sidebar_user;
    }

    // current user data
    let current_user = null;

    function setCurrentUser(val) {
        current_user = val;
    }

    function getCurrentUser() {
        return current_user;
    }

    // for specialty ajax call
    // to get service data and clinical data
    let responseData = null;

    function setResponseData(val) {
        if (val['data']) {
            responseData = val['data'];
            if (responseData['clinical_data']) {
                setClinicalData(responseData['clinical_data']);
            }
            if (responseData['service_data']) {
                setServiceData(responseData['service_data']);
            }
            if (responseData['current_user']) {
                setCurrentUser(responseData['current_user']);
            }
            if (responseData['user_list']) {
                setSidebarUser(responseData['user_list']);
            }
        }
    }

    function getResponseData() {
        return responseData;
    }

    // custom data
    let customData = null;

    function setCustomData(val) {
        customData = val;
    }

    function getCustomData() {
        return customData;
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
        },
        cataract: {
            setCataractSearchForm: setCataractSearchForm,
            getCataractSearchForm: getCataractSearchForm,
            clearCataractSearchForm: clearCataractSearchForm,
        }
    };
})();
