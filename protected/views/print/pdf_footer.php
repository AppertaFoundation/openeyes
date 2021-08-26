<style>
    #footer {
        font-size: 5.2pt;
        /*Padding below has been commented as part of OE-11253, if it causes issues, try setting padding-bottom to a suitable value.*/
        /*padding: 0;*/
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
    docrefs[document_no] = docrefs[document_no];
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
    var patient_dobs = {{PATIENT_DOBS}};
    var y = document.getElementsByClassName('patient_name');
    for (var j=0; j<y.length; j++) {
        y[j].innerHTML = patient_names[document_no] + "<br/>";
    }

    var y = document.getElementsByClassName('patient_hosnum');
    for (var j=0; j<y.length; j++) {
        y[j].innerHTML = "{{Hos No}}: " + patient_hosnums[document_no];
    }

    var y = document.getElementsByClassName('patient_nhsnum');
    for (var j=0; j<y.length; j++) {
        y[j].innerHTML = ", {{NHS No}}: " + patient_nhsnums[document_no];
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
<div class="puppeteer-footer-left" style="width: 45%; float: left; margin: 0; text-align: left;">
    {{FOOTER_LEFT}}
</div>
<div class="puppeteer-footer-middle">
    {{FOOTER_MIDDLE}}
</div>
<div class="puppeteer-footer-right" style="width: 45%; float: right; margin: 0; text-align: right;">
    {{FOOTER_RIGHT}}
</div>
