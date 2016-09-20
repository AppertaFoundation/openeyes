<?php

/**
 * Class UserController
 */
class UserController extends BaseAdminController
{
    /**
     * @var string
     */
    public $layout = 'admin';

    /**
     * @var int
     */
    public $itemsPerPage = 100;

    public function actionList()
    {
        $admin = new Admin(User::model(), $this);
        $admin->setListFields(array(
            'id',
            'username',
            'title',
            'first_name',
            'last_name',
            'is_doctor',
            'roles',
            'active',
        ));
        $admin->searchAll();
        $admin->getSearch()->addActiveFilter();
        $admin->getSearch()->setItemsPerPage($this->itemsPerPage);
        $admin->listModel();
    }
}