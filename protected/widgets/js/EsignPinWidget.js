const EsignPinWidget = {
    getSignature : function(action,row_id) {
        let pin_input = document.getElementById("input_"+row_id);
        let signature_div = document.getElementById("div_"+row_id);
        let signature_pin = pin_input.value;

        let params = {signature_pin,YII_CSRF_TOKEN:YII_CSRF_TOKEN};
        const searchParams = Object.keys(params).map((key) => {
            return encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
        }).join('&');

        fetch(baseUrl + "/" + moduleName + "/default/"+action,{
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8'
            },
            body: searchParams,
            method: 'POST',
        })
            .then(response => response.json())
            .then(data => {
                if(data) {
                    if( data.code !== 0){
                        signature_div.innerHTML = '<span class="error">'+data.error+'</span>';
                    } else {
                        signature_div.innerHTML = '<img src="data:image/png;base64, '
                            +(data.singature_image_base64)
                        +'">';
                    }
                }
            });

    }
};
