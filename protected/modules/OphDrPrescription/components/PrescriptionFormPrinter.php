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

    public $current_item_index = 0;
    public $current_taper_index = 0;
    private $current_item_attr; // If a single item is greater than 30 lines, this will capture the attribute that overflows.
    private $split_print = false;

    private $total_items;
    private $default_cost_code;
    private $print_mode;
    private $page_count = 1;

    const MAX_FPTEN_LINES = 21;
    const LHS_LINE_FILLER_TEXT = 'x';
    const RHS_LINE_FILLER_TEXT = 'GP COPY';

    public function init()
    {
        $settings = new SettingMetadata();
        $this->total_items = count($this->items);
        $this->print_mode = $settings->getSetting('prescription_form_format');
        $this->default_cost_code = $settings->getSetting('default_prescription_code_code');
    }

    /**
     * @throws CException
     */
    public function run()
    {
        $this->total_items = count($this->items);
        for ($page = 0; $page < $this->page_count; $page++) {
            $this->render('form_print_container', array(
                'form_css_class' => $this->print_mode === 'FP10' ? 'fpten' : 'wpten',
                'page_number' => $page,
            ));
        }
    }

    public function getDefaultCostCode()
    {
        return $this->default_cost_code;
    }

    public function getTotalItems()
    {
        return $this->total_items;
    }

    public function addPages($num_pages = 1)
    {
        $this->page_count += $num_pages;
    }

    public function getTotalPages()
    {
        return $this->page_count;
    }

    public function getPrintMode()
    {
        return $this->print_mode;
    }

    public function isPrintable($item)
    {
        return str_replace('{form_type}', $this->print_mode, $item->dispense_condition->name) === 'Print to ' . $this->print_mode;
    }

    public function getCurrentItemAttr()
    {
        return $this->current_item_attr;
    }

    /**
     * @param string|null $attr
     * @param int|null $taper_index
     */
    public function setCurrentAttr($attr = null, $taper_index = null)
    {
        if ($attr) {
            if ($taper_index !== null) {
                $this->current_item_attr = "taper{$taper_index}_$attr";
            } else {
                $this->current_item_attr = "item_$attr";
            }
        } else {
            $this->current_item_attr = null;
        }
    }

    public function setCurrentAttrStr($attr_str)
    {
        $this->current_item_attr = $attr_str;
    }

    public function enableSplitPrint()
    {
        $this->split_print = true;
    }

    public function disableSplitPrint()
    {
        $this->split_print = false;
    }

    public function isSplitPrinting()
    {
        return $this->split_print;
    }
}
