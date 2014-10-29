<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<script>
	function subst() {
		var documents = {{DOCUMENTS}};
		var document_no = 0;

		var vars={};
		var x=window.location.search.substring(1).split('&');
		for (var i in x) {var z=x[i].split('=',2);vars[z[0]] = unescape(z[1]);}
		vars['topage'] = vars['topage'] / documents;
		while (vars['page'] > vars['topage']) {
			vars['page'] -= vars['topage'];
			document_no += 1;
		}

		var docrefs = {{DOCREFS}};
		var y = document.getElementsByClassName('docref');
		for (var j=0; j<y.length; j++) {
			y[j].innerHTML = docrefs[document_no];
		}

		var barcodes = {{BARCODES}};
		var y = document.getElementsByClassName('barcode');
		for (var j=0; j<y.length; j++) {
			y[j].innerHTML = barcodes[document_no];
		}

		var patient_names = {{PATIENT_NAMES}};
		var patient_hosnums = {{PATIENT_HOSNUMS}};
		var patient_nhsnums = {{PATIENT_NHSNUMS}};
		var y = document.getElementsByClassName('patient_name');
		for (var j=0; j<y.length; j++) {
			y[j].innerHTML = patient_names[document_no] + "<br/>";
		}

		var y = document.getElementsByClassName('patient_hosnum');
		for (var j=0; j<y.length; j++) {
			y[j].innerHTML = "Hosp No: " + patient_hosnums[document_no];
		}

		var y = document.getElementsByClassName('patient_nhsnum');
		for (var j=0; j<y.length; j++) {
			y[j].innerHTML = ", NHS No: " + patient_nhsnums[document_no];
		}

		var x=['frompage','topage','page','webpage','section','subsection','subsubsection'];
		for (var i in x) {
			var y = document.getElementsByClassName(x[i]);
			for (var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];
		}
	}
	</script>
	<body style="margin: 0; padding: 0; margin-bottom: 61.6em;" onload="subst()">
		<div style="width: 46%; float: left; font-size: 6pt;">
			{{FOOTER_LEFT}}
		</div>
		<div style="width: 35%; float: left; margin-left: auto; margin-right: auto; font-size: 6pt;">
			{{FOOTER_MIDDLE}}
		</div>
		<div style="width: 19%; float: right; font-size: 6pt;">
			{{FOOTER_RIGHT}}
		</div>
	</body>
</html>
