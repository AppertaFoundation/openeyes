
$(document).ready(function() {
    const $wrapper = document.getElementById('wrapper-auto-generate-events-selector');

    if ($wrapper) {
        const suffix = $wrapper.dataset.suffix;
        const $generate_prescription = document.getElementById(`auto_generate_prescription_after_${suffix}`);

        if ($generate_prescription) {
            const $generate_prescription_label = $generate_prescription.closest('label');

            $generate_prescription_label.addEventListener('change', () => {
                const $generate_gp = document.querySelector(`input[type="checkbox"]#auto_generate_gp_letter_after_${suffix}`);
                const $dropdown = document.getElementById(`auto_generate_prescription_after_${suffix}_set_id`);
                if ($generate_gp) {
                    $generate_gp.click();

                    if (!$generate_gp.checked) {
                        $dropdown.value = "";
                    }
                    $dropdown.style.display = $generate_gp.checked ? 'inline-block' : 'none';
                } else {
                    console.warn(`Element with id "auto_generate_gp_letter_after_${suffix}" not found`);
                }
            });
        }
    }

});
