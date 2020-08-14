<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
class ModuleAPITest extends CDbTestCase
{
    protected $orig_modules;
    protected $test_event_type;

    public $fixtures = array(
            'event_types' => 'EventType',
            'event_groups' => 'EventGroup',
        'users' => 'User',
    );

    public function setUp()
    {
        parent::setUp();
        $this->orig_modules = Yii::app()->getModules();
        Yii::app()->setModules(array('TestModule' => array('class' => 'ModuleAPITestNS\TestModule')));
        Yii::setPathOfAlias('ModuleAPITestNS', __DIR__.'/ModuleAPITest');
        Yii::setPathOfAlias('application.modules.TestModule.components', __DIR__.'/ModuleAPITest/components');
        // create temporary event type for testing
        $event_type = new EventType();
        $event_type->name = 'Test Module';
        $event_type->class_name = 'TestModule';
        $event_type->event_group_id = 1;
        $event_type->last_modified_user_id = 1;
        $event_type->noVersion()->save();
        $this->test_event_type = $event_type;
    }
    public function tearDown()
    {
        parent::tearDown();
        Yii::app()->setModules($this->orig_modules);
        Yii::setPathOfAlias('ModuleAPITestNS', null);
        Yii::setPathOfAlias('application.modules.TestModule.components', null);
        if ($this->test_event_type) {
            $this->test_event_type->noVersion()->delete();
        }
    }

    /**
     * @covers ModuleAPI
     */
    public function testget()
    {
        $res = Yii::app()->moduleAPI->get('TestModule');
        $this->assertEquals('ModuleAPITestNS\components\TestModule_API', get_class($res));
    }
}
