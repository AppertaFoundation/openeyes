<style>
    #footer {
        font-size: 5.2pt;
        padding: 0;
        margin-right: {{MARGIN_RIGHT}};
        margin-left: {{MARGIN_LEFT}};
        width: calc(100% - {{MARGIN_LEFT}} - {{MARGIN_RIGHT}};
    }

    #footer > div {
        width: 10%;
        float: left;
        margin: 0 auto;
        text-align: center;
    }
</style>
<script>
function subst() {
    var documents = {{DOCUMENTS}};
    var document_no = 0;
    var custom_tags = {{CUSTOM_TAGS}};

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
    var patient_primary_identifiers = {{PATIENT_PRIMARY_IDENTIFIERS}};
    var patient_secondary_identifiers = {{PATIENT_SECONDARY_IDENTIFIERS}};
    var patient_dobs = {{PATIENT_DOBS}};
    var y = document.getElementsByClassName('patient_name');
    for (var j=0; j<y.length; j++) {
        y[j].innerHTML = patient_names[document_no] + "<br/>";
    }

    var y = document.getElementsByClassName('patient_hosnum');
    for (var j=0; j<y.length; j++) {
            y[j].innerHTML = "{{Hos No}}: " + patient_primary_identifiers[document_no];
    }

    var y = document.getElementsByClassName('patient_nhsnum');
    for (var j=0; j<y.length; j++) {
            y[j].innerHTML = ", {{NHS No}}: " + patient_secondary_identifiers[document_no];
    }

    var y = document.getElementsByClassName('patient_dob');
    for (var j=0; j<y.length; j++) {
        y[j].innerHTML = "<br/>DOB: " + patient_dobs[document_no];
    }

    var x=['frompage','topage','page','webpage','section','subsection','subsubsection'];
    for (var i in x) {
        var y = document.getElementsByClassName(x[i]);
        for (var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];
    }

    for (var i in custom_tags) {
        var y = document.getElementsByClassName('puppeteer-footer-left');

        for (var j=0; j<y.length; j++) {
            y[j].innerHTML = y[j].innerHTML.replace('{{' + i + '}}', '<div>' + custom_tags[i] + '</div>');
        }
    }
}
</script>
<div class="puppeteer-footer-left" style="width: 45%; float: left; margin: 0; font-size: 8pt;">
    {{FOOTER_LEFT}}
</div>
<div class="puppeteer-footer-middle" style="float: left; margin: 0 auto; text-align: center; font-size: 8pt;">
    {{FOOTER_MIDDLE}}
</div>
<div class="puppeteer-footer-right" style="width: 45%; float: right; margin: 0; text-align: right; font-size: 8pt;">
    {{FOOTER_RIGHT}}
</div>
