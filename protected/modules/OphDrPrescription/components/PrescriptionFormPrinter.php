<?php

    /**
     * Class PrescriptionFormPrinter
     */

class PrescriptionFormPrinter extends CWidget
{
    /**
     * @var $items OphDrPrescription_Item[]
     */
    public $items;
    public $patient;
    public $site;
    public $firm;
    public $user;

    public $current_item_index = 0;
    public $current_taper_index = 0;
    private $current_item_attr; // If a single item is greater than 30 lines, this will capture the attribute that overflows.
    private $split_print = false;
    private $total_pages;

    private $total_items;
    private $default_cost_code;
    private $print_mode;
    private $page_count = 1;

    const MAX_FPTEN_LINES = 22;
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

        $total_lines_used = 0;
        $this->total_pages = 0;
        $this->page_count = 1;
        $previous_item = null;
        $blank_lines = null;
        foreach ($this->items as $index => $item) {
            if ($this->isPrintable($item)) {
                $lines_used = $total_lines_used % self::MAX_FPTEN_LINES;
                // Wrap to a new page.
                if (isset($this->items[$index + 1])) {
                    if ($item->fpTenLinesUsed() + 1 > self::MAX_FPTEN_LINES) {
                        $extra_item_lines = $this->items[$index + 1]->fpTenLinesUsed() + 1;
                        $blank_lines = self::MAX_FPTEN_LINES - (($item->fpTenLinesUsed() + 1) % self::MAX_FPTEN_LINES);
                        if ($extra_item_lines <= $blank_lines) {
                            // No blank lines following the current item, meaning another item will be rendered on the same page.
                            $blank_lines = 0;
                        }
                        $total_lines_used += $blank_lines + (int)floor($item->fpTenLinesUsed() / self::MAX_FPTEN_LINES);
                    } elseif ($item->fpTenLinesUsed() + 1 > self::MAX_FPTEN_LINES - $lines_used) {
                        //echo $total_lines_used;
                        $total_lines_used += self::MAX_FPTEN_LINES - $lines_used;
                    }
                }
                $total_lines_used += ($item->fpTenLinesUsed() + 1);
            }
        }
        $this->total_pages = (int)ceil($total_lines_used / self::MAX_FPTEN_LINES);
    }

    /**
     * Render the prescription form widget.
     * @throws CException
     */
    public function run()
    {
        $this->total_items = count($this->items);
        $this->page_count = 1;
        for ($page = 0; $page < $this->total_pages; $page++) {
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
        if ($this->page_count + $num_pages <= $this->total_pages) {
            $this->page_count += $num_pages;
        }
    }

    /**
     * Get the total number of pages to be rendered by the widget.
     * @return int Total pages
     */
    public function getTotalPages()
    {
        return $this->total_pages;
    }

    /**
     * Get current page number.
     * @return int Current page number
     */
    public function getPageNumber()
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

    public function getLineLength()
    {
        if ($this->print_mode === 'FP10') {
            return OphDrPrescription_Item::MAX_FPTEN_LINE_CHARS;
        }
        return OphDrPrescription_Item::MAX_WPTEN_LINE_CHARS;
    }
}
