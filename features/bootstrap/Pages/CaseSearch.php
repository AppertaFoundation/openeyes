<?php

use Behat\Behat\Exception\BehaviorException;

class CaseSearch extends OpenEyesPage
{
    protected $path = "/OECaseSearch/caseSearch/index";
    protected $elements = array(
        'addParam' => array(
            'xpath' => "//*[@id='js-add-param']"
        ),
        'lastParam' => array(
            'xpath' => "//*[@id='param-list']//*[@class='parameter'][last()]"
        ),
        'searchBtn' => array(
            'xpath' => "//*[@class='js-search-btn']"
        ),
        'searchResults' => array(
            'xpath' => "//*[@id='case-search-results']"
        ),
        'ageOperation' => array(
            'xpath' => "//*[@class='js-age-operation']"
        ),
        'ageMinVal' => array(
            'xpath' => "//*[@class='js-age-min']"
        ),
        'ageMaxVal' => array(
            'xpath' => "//*[@class='js-age-max']"
        ),
        'ageVal' => array(
            'xpath' => "//*[@class='js-age-value']"
        ),
    );

    /**
     * @param $lowerAge int|null
     * @param $upperAge int|null
     *
     * @throws \Behat\Mink\Exception\ElementNotFoundException
     */
    public function addAgeParam($lowerAge, $upperAge)
    {
        $this->getElement('addParam')->selectOption('Patient Age');
        $this->waitForElementDisplayBlock('Patient Age', 500);

        $ageParam = $this->getElement('lastParam');
        $operation = $ageParam->find('xpath', $this->getElement('ageOperation')->getXpath());
        $ageVal = $ageParam->find('xpath', $this->getElement('ageVal')->getXpath());

        if ($lowerAge && $upperAge) {
            $operation->selectOption('Between');
            $ageParam->find('xpath', $this->getElement('ageMinVal')->getXpath())->setValue($lowerAge);
            $ageParam->find('xpath', $this->getElement('ageMaxVal')->getXpath())->setValue($upperAge);
        } else if ($lowerAge && !$upperAge) {
            $operation->selectOption('Older than');
            $ageVal->setValue($lowerAge);
        } else if (!$lowerAge && $upperAge) {
            $operation->selectOption('Younger than');
            $ageVal->setValue($upperAge);
        }
    }

    public function search()
    {
        $this->getElement('searchBtn')->click();
    }

    public function resultsExist()
    {
        if (!$this->has('xpath', $this->elements['searchResults']['xpath'])) {
            throw new \Behat\Mink\Exception\ElementNotFoundException(
                $this->getSession(),
                null,
                'xpath',
                $this->elements['searchResults']['xpath']
            );
        }
    }
}

