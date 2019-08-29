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

    private $split_print_page = 1;
    private $split_print_total_pages = 1;

    const MAX_FPTEN_LINES = 24;
    const LHS_LINE_FILLER_TEXT = 'x';
    const RHS_LINE_FILLER_TEXT = 'GP COPY';

    /**
     * Initialise the widget. This will set the total items based on the item array passed to the widget on creation,
     * print mode and default cost code.
     */
    public function init()
    {
        $settings = new SettingMetadata();
        $this->total_items = count($this->items);
        $this->print_mode = $settings->getSetting('prescription_form_format');
        $this->default_cost_code = $settings->getSetting('default_prescription_code_code');
    }

    /**
     * Render the prescription form widget.
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

    /**
     * Get the default cost code being used by the widget.
     * @return string Default cost code as specified in settings.
     */
    public function getDefaultCostCode()
    {
        return $this->default_cost_code;
    }

    /**
     * Get the total items currently stored within the widget.
     * @return int Total items being processed through the widget.
     */
    public function getTotalItems()
    {
        return $this->total_items;
    }

    /**
     * Add pages to the widget.
     * @param int $num_pages Number of pages to add.
     */
    public function addPages($num_pages = 1)
    {
        $this->page_count += $num_pages;
    }

    /**
     * Add split-print pages to the widget.
     * @param int $num_pages Number of pages to add.
     */
    public function addSplitPage($num_pages = 1)
    {
        // Only add another split page if there are more pages to print.
        if ($this->split_print_total_pages > 1 && $this->split_print_page + $num_pages <= $this->split_print_total_pages) {
            $this->split_print_page += $num_pages;
        }
    }

    /**
     * Get the total number of pages to be rendered by the widget.
     * @return int Total pages
     */
    public function getTotalPages()
    {
        return $this->page_count;
    }

    /**
     * Get the print mode currently being used by the widget.
     * @return string Print mode.
     */
    public function getPrintMode()
    {
        return $this->print_mode;
    }

    /**
     * @param $item OphDrPrescription_Item The item to be printed.
     * @return bool True if the item is printable to FP10/WP10; otherwise false.
     */
    public function isPrintable($item)
    {
        return str_replace('{form_type}', $this->print_mode, $item->dispense_condition->name) === 'Print to ' . $this->print_mode;
    }

    /**
     * Get the current item attribute being rendered.
     * @return string Current item attribute.
     */
    public function getCurrentItemAttr()
    {
        return $this->current_item_attr;
    }

    /**
     * Get the current item being rendered by the widget.
     * @return OphDrPrescription_Item|null
     */
    public function getCurrentItem()
    {
        if (isset($this->current_item_index)) {
            return $this->items[$this->current_item_index];
        }
        return null;
    }

    /**
     * Set the current attribute to render in the widget.
     * @param string|null $attr Attribute to render. If null, the value is unset.
     * @param int|null $taper_index Position of the taper to render. If null, the attribute is rendered as part of the item and not as part of a taper.
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

    /**
     * Set the current attribute using a raw attribute string.
     * @param $attr_str string Raw attribute string of the form 'item_$attr' for an item attribute or 'taper$i_$attr' for a taper attribute.
     */
    public function setCurrentAttrStr($attr_str)
    {
        $this->current_item_attr = $attr_str;
    }

    /**
     * Enable split sprinting.
     */
    public function enableSplitPrint()
    {
        $this->split_print = true;
    }

    /**
     * Disable split printing.
     */
    public function disableSplitPrint()
    {
        $this->split_print = false;
    }

    /**
     * Determine whether the widget is currently split printing.
     * @return bool True if split printing is enabled; otherwise false.
     */
    public function isSplitPrinting()
    {
        return $this->split_print;
    }

    /**
     * Gets the page number of a split-printed page.
     * @return int The page number for the split printed item.
     */
    public function getSplitPageNumber()
    {
        return $this->split_print_page;
    }

    /**
     * Get the total pages to be split-printed.
     * @return int The total number of pages to be split-printed.
     */
    public function getTotalSplitPages()
    {
        return $this->split_print_total_pages;
    }

    /**
     * Reset the split page count for the widget. This is used for each new item to be rendered by the widget.
     * @param $total_lines int The number of lines the item will require. This is used to determine the total split-print pages for the item.
     */
    public function resetSplitPageCount($total_lines)
    {
        $this->split_print_page = 1;
        $this->split_print_total_pages = (int)ceil($total_lines / self::MAX_FPTEN_LINES);
    }
}
