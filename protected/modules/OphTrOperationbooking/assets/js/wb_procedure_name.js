/*
Handle Whiteboard Procedure name
DOM structure:
- js hook: "js-handle-procedure-name"
- JSON for procedure short and long name
Example:
<div class="oe-wb-widget data-single-extra js-handle-procedure-name" data-procedure='{"shortName":"{{ Short name }}","fullName":"{{ Eye Side}} - {{ Long name }}"}'>
	<h3>Procedure</h3>
	<div class="wb-data">
		{{ Eye side }}
		<div class="extra-data">{{ Procedure long name }}</div>
	</div> <!-- wb-data -->
</div>
*/
function addEventToWhiteBoardButton(fullName) {
    $('.overflow-icon-btn').on('click', function () {
        const popup = document.createElement('div');
        popup.className = "oe-popup-wrap clear";
        popup.innerHTML = `<div class="wb-data-overflow-popup">${fullName}</div>`;
        document.body.appendChild(popup);
        popup.addEventListener("mousedown", () => popup.remove());
    });
}

ready(() => {
    /*
    Find DOM elements with JS hook: "js-handle-procedure-name"
    Turn nodeList into Array
    */
    const widgets = Array.from(document.querySelectorAll('.js-handle-procedure-name'));

    /**
     * Show popup of full Procedure name
     * @param {Element} widget - div: "oe-wb-widget"
     * @param {String} fullName - procedure full name
     */
    const fullProcedurePopup = (widget, fullName) => {
        if ($(widget).find('.overflow-icon-btn').length === 0) {
            // add icon button "..."
            const div = document.createElement('div');
            div.className = "overflow-icon-btn";
            widget.appendChild(div);
            // clicking on "..." popups up the overlay showing the full procedure name
            addEventToWhiteBoardButton(fullName);
        }
    };

    // procedure widgets should have a JSON: data-procedures{"short":"{shortname}", "long":"{longname}"}
    // check each procedure widget for overflow:
    widgets.forEach(widget => {
        // DOM elements to work with
        const dataDiv = widget.querySelector('.wb-data');
        const procedureDiv = widget.querySelector('.extra-data');

        // check we have the JSON to do this:
        if (widget.dataset.procedure === undefined) {
            console.error("No JSON provided for Procedure names");
            return;
        }

        // grap the short and long names from JSON in DOM
        const procedure = JSON.parse(widget.dataset.procedure);

        // work out available height for Procedure name (allow for widget header)
        const widgetH = widget.clientHeight - (widget.querySelector('h3').clientHeight);
        /*
        step through and find the best solution to display the Procedure name
        */

        if (dataDiv.scrollHeight > widgetH) {
            /*
            1) reduce the font-size for full/long name
            */
            procedureDiv.classList.replace('extra-data', 'extra-small-data');
            if (dataDiv.scrollHeight > widgetH) {
                /*
                2) switch to short name and try that, big size again
                ... add popup with the full/long name
                */
                procedureDiv.classList.replace('extra-small-data', 'extra-data');
                procedureDiv.textContent = procedure.shortName;
                fullProcedurePopup(widget, procedure.fullName);

                if (dataDiv.scrollHeight > widgetH) {
                    /*
                    3) finally make shortname a bit smaller
                    */
                    procedureDiv.classList.replace('extra-data', 'extra-small-data');
                }
            }
        }
    });

});