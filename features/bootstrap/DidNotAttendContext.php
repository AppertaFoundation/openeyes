<?php
/**
 * Created by PhpStorm.
 * User: zhe
 * Date: 30/01/19
 * Time: 11:51 AM
 */

use \SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

class DidNotAttendContext extends PageObjectContext
{
    /**
     * @Then /^I add not attend comments of "([^"]*)"$/
     */
    public function iAddNotAttendCommentsOf($comments)
    {
        /**
         *
         * @var DidNotAttend $DidNotAttend
         */
        $DidNotAttend = $this->getPage ( 'DidNotAttend' );
        $DidNotAttend->notAttendComments ( $comments );
    }
}