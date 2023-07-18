<?php
/**
 * (C) Apperta Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2023, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

namespace OEModule\OphCoCorrespondence\tests\unit\components;

use Element_OphCoCorrespondence_Esign;
use Episode;
use HasDatabaseAssertions;
use LetterMacro;
use MocksSession;

use function PHPUnit\Framework\assertCount;

/**
 * Class CorrespondenceCreatorTest
 *
 * @group sample-data
 * @group correspondence-creator
 */
class CorrespondenceCreatorTest extends \OEDbTestCase
{
    use \FakesSettingMetadata;
    use HasDatabaseAssertions;
    use MocksSession;
    use \WithTransactions;

    private $pin_required_setting_name = 'require_pin_for_correspondence';


    public function setUp(): void
    {
        parent::setUp();

        // low level models check the controller state during creation, so mock here
        $this->mockAppControllerAction();

        \Yii::app()->session['selected_institution_id'] = 1;
        \Yii::import('application.modules.OphCoCorrespondence.components.*');
    }

    public function tearDown(): void
    {
        unset(\Yii::app()->session['selected_institution_id']);
        unset(\Yii::app()->controller);

        parent::tearDown();
    }

    /** @test */
    public function correspondence_creator_will_autosign_when_setting_allows()
    {
        $this->fakeSettingMetadata($this->pin_required_setting_name, 'no');
        $this->mockCurrentContext();
        $user = \User::factory()->withContact()->create();
        $this->mockCurrentUser($user);

        $episode = Episode::factory()->forFirm(\Yii::app()->session->getSelectedFirm())->create();
        $macro = LetterMacro::factory()->forPatientRecipient()->create();

        $creator = new \CorrespondenceCreator($episode, $macro);
        $success = $creator->save();

        $this->assertTrue($success);
        $eventEsigns = Element_OphCoCorrespondence_Esign::model()->findAll('event_id = ?', [$creator->event->id]);
        $this->assertCount(1, $eventEsigns);

        $this->assertCount(1, $eventEsigns[0]->signatures, 'signature should be attached to created correspondence event');
        $this->assertTrue($eventEsigns[0]->isSigned(), 'signature should be signed');
    }

    /** @test */
    public function correspondence_creator_does_not_autosign_when_pin_required()
    {
        $this->fakeSettingMetadata($this->pin_required_setting_name, 'yes');
        $this->mockCurrentContext();
        $user = \User::factory()->withContact()->create();
        $this->mockCurrentUser($user);

        $episode = Episode::factory()->forFirm(\Yii::app()->session->getSelectedFirm())->create();
        $macro = LetterMacro::factory()->forPatientRecipient()->create();

        $creator = new \CorrespondenceCreator($episode, $macro);
        $success = $creator->save();

        $this->assertTrue($success);
        $eventEsigns = Element_OphCoCorrespondence_Esign::model()->findAll('event_id = ?', [$creator->event->id]);
        $this->assertCount(1, $eventEsigns);

        $this->assertFalse($eventEsigns[0]->isSigned(), 'esign should not have a signed signature');
    }

    protected function mockAppControllerAction()
    {
        $controller = $this->getMockBuilder('BaseController')
            ->disableOriginalConstructor()
            ->onlyMethods(['getAction'])
            ->getMock();

        $controller->method('getAction')
            ->willReturn(new class () {
                public $id = 'create';

                public function getId()
                {
                    return 'create';
                }
            });

        \Yii::app()->controller = $controller;
    }
}
