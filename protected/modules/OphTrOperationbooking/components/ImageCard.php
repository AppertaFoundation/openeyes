<?php

    /**
     * Class ImageCard
     * @property $element Element_OphTrOperationnote_Cataract
     * @property $eye Eye
     */
class ImageCard extends WBCard
{
    public $element;
    public $eye;
    protected $css_class = 'data-image';
    protected $baseViewFile = 'data/image_data';
    public function init()
    {
        $this->data = array(
            'idSuffix' => 'Cataract',
            'side' => $this->eye->shortName,
            'mode' => 'edit',
            'width' => 400,
            'height' => 200,
            'model' => $this->element,
            'attribute' => 'eyedraw',
        );
    }
}