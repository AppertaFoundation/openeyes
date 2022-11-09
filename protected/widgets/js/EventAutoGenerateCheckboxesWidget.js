
$(document).ready(function() {
    const $wrapper = document.getElementById('wrapper-auto-generate-events-selector');

    if ($wrapper) {
        const suffix = $wrapper.dataset.suffix;
        const $generate_prescription = document.getElementById(`auto_generate_prescription_after_${suffix}`);

        if ($generate_prescription) {
            const $generate_prescription_label = $generate_prescription.closest('label');

            const $dropdown = document.getElementById(`auto_generate_prescription_after_${suffix}_set_id`);

            /**
             * Binding 'change' event to the Generate prescription checkbox label
             */
            $generate_prescription_label.addEventListener('change', function(e) {
                // get generate gp letter checkbox element
                const generate_gp = document.querySelector(`input[type="checkbox"]#auto_generate_gp_letter_after_${suffix}`);
                // get generate prescription checkbox element
                const generate_px = this.querySelector('input[type="checkbox"]');

                // if there is no generate gp letter checkbox or generate prescription checkbox found
                // print warning message and exit the callback
                if (!generate_gp || !generate_px) {
                    console.warn(`Element with id "auto_generate_gp_letter_after_${suffix}" not found`);
                    return;
                }
                // sync generate gp checkbox attribute 'checked' and value with generate prescription checkbox
                generate_gp.checked = generate_px.checked;
                generate_gp.value = generate_px.value;
                // display the drug sets dropdown if the generate prescription checkbox is checked
                $dropdown.style.display = generate_px.checked ? 'inline-block' : 'none';
            });
        }
    }

});
