<?php

    /**
     * Class WBCard
     * @property $title string
     * @property $colour string
     * @property $editable bool
     * @property $data string|array
     * @property $highlight_colour string
     * @property $event_id int
     */
class WBCard extends CWidget
{
    public $title;
    public $colour;
    public $editable = false;
    public $data;
    public $highlight_colour;
    public $event_id;
    protected $css_class;
    protected $data_view;
    protected $baseViewFile = 'wbcard';

    public function init()
    {
        if (is_string($this->data)) {
            // Single card with no extra or small data.
            $this->css_class = 'data-single';
            $this->data_view = 'data/single_data';
        } elseif (is_array($this->data)) {
            if (isset($this->data['content'])) {
                // Single card with extra and/or small data
                $this->css_class = $this->data['extra_data'] ? 'data-single-extra' : 'data-single';
                $this->data_view = 'data/single_data';
            } elseif (isset($this->data['ed_data'])) {
                // Card with image/eyedraw graphic.
                $this->css_class = 'data_image';
                $this->data_view = 'data/image_data';
            } elseif (isset($this->data[0])) {
                if (count($this->data) === 2 && is_array($this->data[0])) {
                    // Double card
                    $this->css_class = 'data-double-extra';
                    $this->data_view = 'data/double_data';
                } else {
                    // List card
                    $this->css_class = 'data-list';
                    $this->data_view = 'data/list_data';
                }
            }
        }
    }

    public function run()
    {
        $this->render($this->baseViewFile, array(
            'data_view' => $this->data_view,
            'css_class' => $this->css_class
        ));
    }
}