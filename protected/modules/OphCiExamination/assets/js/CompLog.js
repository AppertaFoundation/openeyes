function CompLogConnectionError(message) {
	this.name = 'CompLogConnectionError';
	this.message = message;
	this.stack = (new Error()).stack;
}
CompLogConnectionError.prototype = new Error;

function CompLogConnection() {
	let self = this;
	this.dialog = null;
	this.latestHl7Data = null;
	this.isConnectedToCompLog = false;
	this.attemptDelay = 1000; //should be constant
	this.maxAttempts = 30; // should be constant
	this.initialiseCompLogDialog();
	this.checkConnectionToWS()
		.fail(() => {
			console.log('opening iframe');
			this.openInIframe("oeLauncher:complog");
			sleep(100);
		})
		.always(() => {
			for (let i = 1; i <= self.maxAttempts; i++) {
				console.log(`start of for loop ${i}`);
				let tryToConnect = new Promise((resolve, reject) => setTimeout(() => {
					if (self.isConnectedToCompLog) {
						console.log(`Whoops! Looks like a connection has already been established ${i}`);
						reject(new Error("Whoops! Looks like a connection has already been established"));
					}
					else {
						$.ajax({
							type: 'GET',
							url: "http://localhost:" + OE_COMPLog_port + "/info",
							dataType: 'json',
							contentType: 'application/json; charset=utf-8',
							crossDomain: true,
							success: () => {
								if (!self.isConnectedToCompLog) { //check again in case a different response has established the connection already
									console.log(`Yayaya we were first to find a connection! ${i}`);
									self.isConnectedToCompLog = true;
									resolve(`Yayaya we found a connection! ${i}`);
								}
								else {
									console.log(`another ajax call got a response first :( ${i}`);
									reject(new Error(`another ajax call got a response first :( ${i}`));
								}
							},
							error: () => {
								console.log(`no connection success yet ${i}`);
								reject(new CompLogConnectionError(`no connectoin succeess yet ${i}`));
							}
						});
					}
				}, self.attemptDelay * i));
				tryToConnect
					.then(() => {sleep(3000);}) // without sleep() function, an error box appears after the free trial dialog box. I don't know why sleep(3000) fixes this - seems like a CompLog problem
					.then(this.COMPLogDischargePatient) //try see if setTimeOut works instead of sleep.
					.then(this.COMPLogPresetTest)
					.then(this.pollForTestResults.bind(this))
					.then(() => {
						this.dialog.setTitle('COMPLog test in progress');
						this.dialog.on('ok', () => {
							this.importCompLogResults();
							this.destroy();
						});
						$('.ok').show();
					})
					.catch((error) => {
						if(i >= self.maxAttempts && error.name === 'CompLogConnectionError'){
							// insert some code that adds a message to the dialog that says 'could not connect to COMPLog'
							console.log('took too long to connect with CompLog');
						}
					});
			}
		});
}

CompLogConnection.prototype.initialiseCompLogDialog = function(){
	this.dialog = new OpenEyes.UI.Dialog.Confirm({
		title: 'COMPLog',
		okButton: 'Pull COMPLog Results',
		templateSelector: '#dialog-complog-template'});
	this.dialog.on('cancel', this.destroy.bind(this));
	this.dialog.open();
};

CompLogConnection.prototype.checkConnectionToWS = function() {
    return $.ajax({
			type: 'GET',
			url: "http://localhost:"+OE_COMPLog_port+"/info",
			dataType: 'json',
			contentType: 'application/json; charset=utf-8',
			crossDomain: true,
    });
};

CompLogConnection.prototype.COMPLogPresetTest = function() {
    let requestData = {"Message": "MSH|^~\\&|COMPLOG|COMPLOG||COMPLOG|20130510105428.912+0300||ZPT^ZTP^ZPT_ZTP|MSG100|P|2.4\nEVN|ZTP|20050110045502|||||\nPID|||"+OE_patient_hosnum+"||"+OE_patient_firstname+"^"+OE_patient_lastname+"||"+OE_patient_dob+"|"+OE_patient_gender+"-||2106-3|"+OE_patient_address+"|GL||||S||PATID12345001^2^M10|"+OE_patient_id+"|9-87654^NC\nZTP|COMPlogThresholding"};
    $.ajax({
        url: "http://localhost:"+OE_COMPLog_port+"/hl7",
        data: JSON.stringify(requestData),
        dataType: "json",
        contentType: "application/json; charset=UTF-8",
        type: "POST",
        crossDomain: true,
        async: false,
        success: function (data) {
        },
    });
};

CompLogConnection.prototype.updateLatestPolledHl7 = function(newLatest){
	console.log('updateLatestPolledHl7');
	this.latestHl7Data = newLatest;
};

CompLogConnection.prototype.COMPLogDischargePatient = function() {
	console.log('discahrge patient');
    let requestData = {"Message": "MSH|^~\&|ADT1|COMPLOG|COMPLOG|COMPLOG|198808181126|SECURITY|ADT^A03|MSG00001|P|2.4\nEVN|A01-|198808181123\nPID|||"+OE_patient_hosnum+"||"+OE_patient_firstname+"^"+OE_patient_lastname+"||"+OE_patient_dob+"|"+OE_patient_gender+"-||2106-3|"+OE_patient_address+"|GL||||S||PATID12345001^2^M10|"+OE_patient_id+"|9-87654^NC"};
    $.ajax({
        url: "http://localhost:"+OE_COMPLog_port+"/hl7",
        data: JSON.stringify(requestData),
        dataType: "json",
        contentType: "application/json; charset=UTF-8",
        type: "POST",
        crossDomain: true,
        async: false,
        success: function (data) {
        },
    });
};

CompLogConnection.prototype.convertHl7ToArray = function(hl7Data){
	let hl7 = hl7Data.Message;
	let parse = function (str) {
		var segments = str.split('\n');
		return _.map(segments, function (segment) {
			var fields = segment.split('|');
			return _.map(fields, function (field) {
				return _.includes(field,'^') ? field.split('^') : field;
			});
		});
	};

	let measurements = parse(hl7);
	let results = [];
	let today = new Date().toJSON().slice(0,10).replace(/-/g,'');

	for(i=0;i<measurements[0].length;i++){
		if(measurements[0][i] == "\rZR1") {
			if(measurements[0][i+2].substring(0,8) == today){
				measurement = {side: "", method: "", logmar: "", snellen: "", base: ""};
				measurement.side = measurements[0][i+4].toLowerCase();
				measurement.method = measurements[0][i+5].replace("Usual ","").replace("Lenses", "lens").replace("Best Corrected", "Glasses");
				measurement.base = measurements[0][i+7];
				measurement.logmar = measurements[0][i+8].substring(0, measurements[0][i+8].length - 1).replace("(","").replace(")","");
				measurement.snellen = measurements[0][i+11].replace(".0","").replace("(","").replace(")","");
				results.push(measurement);
			}
		}
	}
	return results;
};

CompLogConnection.prototype.pollForTestResults = function(){
	if(this.dialog) { //checks that the CompLog dialog is actually still open before continuing the polling process
		this.getHl7TestResults()
			.done((data) => {this.updateLatestPolledHl7.call(this, data);})
			.done(() => {setTimeout(this.pollForTestResults.bind(this), 1000);});
	}
};

CompLogConnection.prototype.getHl7TestResults = function(){
	let requestData =  {"Message": "MSH|^~\\&|COMPLOG|COMPLOG||COMPLOG|20130510105428.912+0300||QRY^ZTR|MSG100|P|2.4\nQRD|201311111016|R|I|Q1000|||10^RD|100437363|RES|ALL||\nPID|||"+OE_patient_hosnum+"||"+OE_patient_firstname+"^"+OE_patient_lastname+"||"+OE_patient_dob+"|"+OE_patient_gender+"-||2106-3|"+OE_patient_address+"|GL||||S||PATID12345001^2^M10|"+OE_patient_id+"|9-87654^NC"};
	return $.ajax({
		url: "http://localhost:" + OE_COMPLog_port + "/hl7",
		data: JSON.stringify(requestData),
		dataType: "json",
		contentType: "application/json; charset=UTF-8",
		type: "POST",
		crossDomain: true,
	});
};

CompLogConnection.prototype.importCompLogResults = function() {
	console.log(`impotCompLogResults()`);
	this.getHl7TestResults()
		.done( data => {this.updateLatestPolledHl7.call(this, data);} )
		.always( () => {this.saveResultsToOE.call(this, (this.convertHl7ToArray(this.latestHl7Data)))} );
};

function OphCiExamination_VisualAcuity_getClosestValue(mvalue) {
    var lastdiff = 10000;
    var previousvalue = {};
    $('ul[data-id="reading_val"]').each(function() {
        $(this).find('li').each(function()
        {
            diff = Math.abs($(this).data("id") - mvalue);
            if(diff < lastdiff)
            {
                lastdiff = diff;
                previousvalue.id = $(this).data("id");
                previousvalue.label = $(this).data("label");
                previousvalue.tooltip = $(this).data("tooltip");
            }
        });
    });
    return previousvalue;
}

function OphCiExamination_VisualAcuity_getMethodData(methodName) {
    let method_data = {};
    $('ul[data-id="method"]').each(function() {
        $(this).find('li').each(function(){
            if($(this).data("label") == methodName){
                method_data.id = $(this).data("id");
                method_data.label = $(this).data("label");
                return method_data;
            }
        });
    });
    return method_data;
}

CompLogConnection.prototype.saveResultsToOE = function(resultsArray) {
	console.log(`saveResultsToOE(${resultsArray})`);
    unit = $("#visualacuity_unit_change option:selected").html();
    resultsArray.forEach(function(element) {
        let selected_data = {};
        closestValue = OphCiExamination_VisualAcuity_getClosestValue(element.base);

        selected_data.reading_value = closestValue.id;
        selected_data.reading_display = closestValue.label;
        selected_data.tooltip =  closestValue.tooltip;

        method_data = OphCiExamination_VisualAcuity_getMethodData(element.method);

        selected_data.method_id = method_data.id;
        selected_data.method_display = method_data.label;

        OphCiExamination_VisualAcuity_addReading(element.side, selected_data);
    });
    this.COMPLogDischargePatient();
};

function sleep(milliseconds) {
	console.log(`sleep(${milliseconds})`);
    let start = new Date().getTime();
    for (let i = 0; i < 1e7; i++) {
        if ((new Date().getTime() - start) > milliseconds){
            break;
        }
    }
}

CompLogConnection.prototype.openInIframe = function(url){
	console.log(`openInIframe(${url})`);
	$('body').append('<iframe width="0" height="0" vspace="0" hspace="0" id="complog_iframe" src="'+url+'"></iframe>');
};

CompLogConnection.prototype.destroy = function(){
	console.log(this);
	this.dialog.destroy();
	this.dialog = null;
	$("#complog_iframe").remove();
};

function addMessageToFadeContent(msg)
{
    $(".fadeContent").append(msg+"<br>");
}

$(document).on("click", "#et_complog", function(event){
    event.preventDefault();
    let compLogConnection = new CompLogConnection();
});
