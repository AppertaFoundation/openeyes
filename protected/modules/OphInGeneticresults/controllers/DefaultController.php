<?php

class DefaultController extends BaseEventTypeController
{
    /**
     * @return array
     */
    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('Create', 'Update', 'View', 'Print'),
                'roles' => array('OprnEditGeneticResults'),
            ),
            array('allow',
                'actions' => array('View', 'Print'),
                'roles' => array('OprnViewGeneticResults'),
            ),
        );
    }

    /**
     * @param BaseEventTypeElement $element
     * @return bool
     */
    public function isRequiredInUI(BaseEventTypeElement $element)
    {
        return true;
    }
}
