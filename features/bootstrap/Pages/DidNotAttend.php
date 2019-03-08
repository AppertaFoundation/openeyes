<?php
/**
 * Created by PhpStorm.
 * User: zhe
 * Date: 30/01/19
 * Time: 11:52 AM
 */

class DidNotAttend extends OpenEyesPage
{
    protected $elements = array(
        'CommentsBtn'=>array(
            'xpath'=>"//*[@id='OEModule_OphCiDidNotAttend_models_Comments_element']"
        ),

        'CommentFields'=>array(
            'xpath'=>"//*[@id='OEModule_OphCiDidNotAttend_models_Comments_comment']"
        ),
    );

    public function notAttendComments($comments){
        $this->getElement('CommentsBtn')->click();
        $this->getElement('CommentFields')->setValue($comments);
    }
}

