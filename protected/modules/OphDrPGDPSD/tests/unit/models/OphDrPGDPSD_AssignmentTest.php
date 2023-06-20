<?php
/**
* @covers OphDrPGDPSD_PGDPSD
* @covers OphDrPGDPSD_PGDPSDMeds
* @covers OphDrPGDPSD_AssignedUser
* @covers OphDrPGDPSD_AssignedTeam
* @covers OphDrPGDPSD_Assignment
* @covers OphDrPGDPSD_AssignmentMeds
* @covers OphDrPGDPSD_AssignmentComment
*
* @group pgdpsd
 */
class OphDrPGDPSD_AssignmentTest extends \ActiveRecordTestCase
{
    protected $fixtures = array(
        'assignments' => OphDrPGDPSD_Assignment::class,
        'ophdrpgdpsd_pgdpsd' => OphDrPGDPSD_PGDPSD::class,
        'drugs' => 'Medication',
        'routes' => 'MedicationRoute',
        'worklist_patients' => 'WorklistPatient',
    );

    public function getModel()
    {
        return OphDrPGDPSD_Assignment::model();
    }

    public function getPGDPSD()
    {
        return array(
            array(
                'pgdpsd_fixtureId' => 'pgdpsd1',
                'temp_user_ids' => array(1, 2, 3),
                'temp_meds' => array(
                    'drug1',
                    'drug2',
                ),
                'temp_team_ids' => array(),
                'can_save' => true,
            ),
            array(
                'pgdpsd_fixtureId' => 'pgdpsd2',
                'temp_user_ids' => array(
                ),
                'temp_meds' => array(
                    'drug1',
                    'drug2',
                ),
                'temp_team_ids' => array(),
                'can_save' => false,
            ),
            array(
                'pgdpsd_fixtureId' => 'pgdpsd3',
                'temp_user_ids' => array(1, 2, 3),
                'temp_meds' => array(),
                'temp_team_ids' => array(),
                'can_save' => false,
            ),
        );
    }
    /**
     * @dataProvider getPGDPSD
     * @throws CException
     */
    public function testCreatePGDPSD($pgdpsd_fixtureId, $temp_user_ids, $temp_meds, $temp_team_ids, $can_save)
    {
        $pgdpsd = $this->ophdrpgdpsd_pgdpsd($pgdpsd_fixtureId);
        $pgdpsd->temp_user_ids = $temp_user_ids;
        foreach ($temp_meds as $med) {
            $attrs = $this->drugs($med)->attributes;
            $temp_med = array(
                'medication_id' => $attrs['id'],
                'dose' => $attrs['default_dose'],
                'dose_unit_term' => $attrs['default_dose_unit_term'],
                'route_id' => $attrs['default_route_id'],
            );
            $pgdpsd->temp_meds_info[] = $temp_med;
        }
        $pgdpsd->temp_team_ids = $temp_team_ids;
        $res = $pgdpsd->save();
        $this->assertEquals($can_save, $res);
    }

    public function getAssignmentDetails()
    {
        return array(
            array(
                'assignment_fixture' => 'assignment1',
                'psd_fixture' => 'pgdpsd1',
                'assigned_meds' => array(),
                'wp_fixture' => 'worklist_patient1',
                'comment' => 'test comment',
                'can_save' => true,
            ),
            array(
                'assignment_fixture' => 'assignment2',
                'psd_fixture' => null,
                'assigned_meds' => array(
                    'drug1',
                    'drug2',
                ),
                'wp_fixture' => 'worklist_patient2',
                'comment' => 'test comment 2',
                'can_save' => true,
            ),
            array(
                'assignment_fixture' => 'assignment3',
                'psd_fixture' => null,
                'assigned_meds' => array(
                    'drug1',
                    'drug5',
                ),
                'wp_fixture' => 'worklist_patient3',
                'comment' => null,
                'can_save' => true,
            ),
            array(
                'assignment_fixture' => 'assignment4',
                'psd_fixture' => null,
                'assigned_meds' => array(
                    'drug2',
                    'drug5',
                ),
                'wp_fixture' => null,
                'comment' => 'custom non order',
                'can_save' => true,
            ),
        );
    }

    /**
     * @dataProvider getAssignmentDetails
     * @throws CException
     */
    public function testCreateAssignment($assignment_fixture, $psd_fixture, $assigned_meds, $wp_fixture, $comment, $can_save)
    {
        $assignment = $this->assignments($assignment_fixture);
        $meds = array();
        if ($psd_fixture) {
            $psd = $this->ophdrpgdpsd_pgdpsd($psd_fixture);
            $meds = array_map(function ($med) {
                return array(
                    'medication_id' => $med->medication_id,
                    'dose' => $med->dose,
                    'dose_unit_term' => $med->dose_unit_term,
                    'route_id' => $med->route_id,
                );
            }, $psd->assigned_meds);
        } else {
            foreach ($assigned_meds as $med_fixture) {
                $med = $this->drugs($med_fixture);
                $meds[] = array(
                    'medication_id' => $med->id,
                    'dose' => $med->default_dose,
                    'dose_unit_term' => $med->default_dose_unit_term,
                    'route_id' => $med->default_route_id,
                );
            }
        }
        $assignment->cacheMeds($meds);
        $assignment->saveComment($comment);
        $res = $assignment->save();
        $this->assertEquals($res, $can_save);
    }
}
