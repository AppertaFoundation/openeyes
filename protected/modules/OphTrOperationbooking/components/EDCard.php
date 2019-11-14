<?php

    /**
     * Class EDCard
     * @property $element Element_OphTrOperationnote_Cataract
     * @property $eye Eye
     */
class EDCard extends WBCard
{
    protected $type = 'Image';
    public $doodles = array();
    public $eye;
    protected $css_class = 'data-image';
    protected $baseViewFile = 'data/ed_data';
    public function init()
    {
        $this->data = array(
            'idSuffix' => 'CataractAxis',
            'onReadyCommandArray' => array(
                array('addDoodle', $this->doodles),
                array('deselectDoodles', array()),
                array('replaceWithBGImage', array('wb-data image-fill', true)),
            ),
            'side' => $this->eye->shortName,
            'mode' => 'view',
            'width' => 2008,
            'height' => 1102,
            'attribute' => 'eyedraw',
        );
    }
}
