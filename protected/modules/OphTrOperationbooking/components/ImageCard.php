<?php

    /**
     * Class ImageCard
     * @property $element Element_OphTrOperationnote_Cataract
     * @property $eye Eye
     */
class ImageCard extends WBCard
{
    public $doodles = array();
    public $eye;
    protected $css_class = 'data-image';
    protected $baseViewFile = 'data/image_data';
    public function init()
    {
        $this->data = array(
            'idSuffix' => 'CataractAxis',
            'onReadyCommandArray' => array(
                array('addDoodle', $this->doodles),
                array('deselectDoodles', array()),
            ),
            'side' => $this->eye->shortName,
            'mode' => 'view',
            'width' => 333.25,
            'height' => 183,
            'attribute' => 'eyedraw',
        );
    }
}