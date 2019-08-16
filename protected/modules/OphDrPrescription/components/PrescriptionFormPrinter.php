<?php

    /**
     * Class PrescriptionFormPrinter
     * @property $items
     * @property $patient
     * @property $site
     * @property $firm
     * @property $user
     * @property $print_mode string
     * @property $page_count

     * @property $split_print
     * @property $current_item_index
     * @property $current_taper_index
     * @property $current_item_attr

     * @property $current_item_copy
     * @property $current_taper_copy
     * @property $current_attr_copy

     * @property $end_of_page
     * @property $split_print_end
     * @property $total_items
     */
    class PrescriptionFormPrinter extends CWidget
    {
        public $items;
        public $patient;
        public $site;
        public $firm;
        public $user;
        public $print_mode;
        public $page_count = 1;

        public $split_print = false;
        public $current_item_index = 0;
        public $current_taper_index = 0;
        public $current_item_attr; // If a single item is greater than 30 lines, this will capture the attribute that overflows.

        public $current_item_copy = 0;
        public $current_taper_copy = 0;
        public $current_attr_copy; // If a single item is greater than 30 lines, this will capture the attribute that overflows.

        public $end_of_page = false;
        public $split_print_end = false;
        public $total_items;
        public $default_cost_code;

        public function init() {
            $settings = new SettingMetadata();
            $this->print_mode = $settings->getSetting('prescription_form_format');
            $this->default_cost_code = $settings->getSetting('default_prescription_code_code');
        }

        /**
         * @throws CException
         */
        public function run() {
            $this->total_items = count($this->items);
            for ($page = 0; $page < $this->page_count; $page++) {
                $this->render('form_print_container', array(
                    'form_css_class' => $this->print_mode === 'FP10' ? 'fpten' : 'wpten',
                    'page_number' => $page
                ));
            }
        }
    }