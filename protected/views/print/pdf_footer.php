<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
	<script>
	function subst() {
		var documents = {{DOCUMENTS}};
		var document_no = 0;
		var custom_tags = {{CUSTOM_TAGS}};

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

		for (var i in custom_tags) {
			var y = document.getElementsByClassName('wkhtmltopdf-footer-left');

			for (var j=0; j<y.length; j++) {
				y[j].innerHTML = y[j].innerHTML.replace('{{' + i + '}}', '<div>' + custom_tags[i] + '</div>');
			}
		}
	}
	</script>
	<body style="margin: 0; padding: 0; margin-bottom: 68.6em;" onload="subst()">
		<div class="wkhtmltopdf-footer-left" style="width: 45%; float: left; margin: 0; font-size: 6pt;">
			{{FOOTER_LEFT}}
		</div>
		<div class="wkhtmltopdf-footer-middle" style="width: 10%; float: left; margin: 0 auto; text-align: center; font-size: 6pt;">
			{{FOOTER_MIDDLE}}
		</div>
		<div class="wkhtmltopdf-footer-right" style="width: 45%; float: right; margin: 0; text-align: right; font-size: 6pt;">
			{{FOOTER_RIGHT}}
		</div>
	</body>
</html>
