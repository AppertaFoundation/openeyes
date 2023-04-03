<?php

class ReferenceLevelIdResolverTest extends OEDbTestCase
{
    use MocksSession;
    use \WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $user = User::model()->findAll(new CDbCriteria(['order' => 'rand()']))[0];
        $this->mockCurrentContext();
        $this->mockCurrentUser($user);
    }

    public function testDefaultConstructor()
    {
        $resolver = new ReferenceLevelIdResolver();
        $firm = Yii::app()->session->getSelectedFirm();

        if ($firm->serviceSubspecialtyAssignment) {
            $specialty = Yii::app()->session->getSelectedFirm()->getSubspecialty()->specialty->id ?? null;
            $subspecialty = Yii::app()->session->getSelectedFirm()->getSubspecialty()->id ?? null;
        } else {
            $specialty = null;
            $subspecialty = null;
        }

        self::assertEquals(Institution::model()->getCurrent()->id, $resolver->institution->id);
        self::assertEquals(Yii::app()->session->getSelectedSite()->id, $resolver->site->id);
        self::assertEquals(Yii::app()->session->getSelectedFirm()->id, $resolver->firm->id);
        self::assertEquals($specialty, $resolver->specialty->id ?? null);
        self::assertEquals($subspecialty, $resolver->subspecialty->id ?? null);
        self::assertEquals(Yii::app()->user->id, $resolver->user->id);
    }

    public function testResolveInstitutionId()
    {
        $institution = Institution::model()->findAll(new CDbCriteria(['order' => 'rand()']))[0];
        $resolver = new ReferenceLevelIdResolver($institution);
        $actual = $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_INSTITUTION]);
        self::assertEquals($institution->id, $actual);

        $this->mockCurrentInstitution($institution);
        $resolver = new ReferenceLevelIdResolver();
        $actual = $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_INSTITUTION]);
        self::assertEquals($institution->id, $actual);
    }

    public function testResolveSiteId()
    {
        $site = Site::model()->findAll(new CDbCriteria(['order' => 'rand()']))[0];
        $resolver = new ReferenceLevelIdResolver(
            null,
            $site
        );
        $actual = $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_SITE]);
        self::assertEquals($site->id, $actual);
    }

    public function testResolveSpecialtyId()
    {
        $specialty = Specialty::model()->findAll(new CDbCriteria(['order' => 'rand()']))[0];
        $resolver = new ReferenceLevelIdResolver(
            null,
            null,
            $specialty
        );
        $actual = $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_SPECIALTY]);
        self::assertEquals($specialty->id, $actual);
    }

    public function testResolveSubspecialtyId()
    {
        $subspecialty = Subspecialty::model()->findAll(new CDbCriteria(['order' => 'rand()']))[0];
        $resolver = new ReferenceLevelIdResolver(
            null,
            null,
            null,
            $subspecialty
        );
        $actual = $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_SUBSPECIALTY]);
        self::assertEquals($subspecialty->id, $actual);
    }

    public function testResolveFirmId()
    {
        $firm = Firm::model()->findAll(new CDbCriteria(['order' => 'rand()']))[0];
        $resolver = new ReferenceLevelIdResolver(
            null,
            null,
            null,
            null,
            $firm
        );
        $actual = $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_FIRM]);
        self::assertEquals($firm->id, $actual);
    }

    public function testResolveUserId()
    {
        $user = User::model()->findAll(new CDbCriteria(['order' => 'rand()']))[0];
        $resolver = new ReferenceLevelIdResolver(
            null,
            null,
            null,
            null,
            null,
            $user
        );
        $actual = $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_USER]);
        self::assertEquals($user->id, $actual);

        $this->mockCurrentUser($user);
        $resolver = new ReferenceLevelIdResolver();
        $actual = $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_USER]);
        self::assertEquals($user->id, $actual);
        self::assertEquals(Yii::app()->user->id, $actual);
    }

    public function testResolveContextIds()
    {
        $site = Site::model()->findAll(new CDbCriteria(['order' => 'rand()']))[0];
        $firm = Firm::model()->findAll(
            new CDbCriteria(
                ['condition' => 'service_subspecialty_assignment_id IS NOT NULL', 'order' => 'rand()']
            )
        )[0] ?? null;
        $subspecialty = $firm->getSubspecialty();
        $specialty = $subspecialty->specialty ?? null;
        $resolver = new ReferenceLevelIdResolver(
            null,
            $site,
            null,
            null,
            $firm
        );
        self::assertEquals($site->id, $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_SITE]));
        self::assertEquals($specialty->id ?? null, $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_SPECIALTY]));
        self::assertEquals($subspecialty->id ?? null, $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_SUBSPECIALTY]));
        self::assertEquals($firm->id ?? null, $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_FIRM]));

        $this->mockCurrentContext(null, $site);
        $resolver = new ReferenceLevelIdResolver();
        self::assertEquals($site->id, $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_SITE]));

        $this->mockCurrentContext($firm);
        $resolver = new ReferenceLevelIdResolver();
        self::assertEquals($specialty->id ?? null, $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_SPECIALTY]));
        self::assertEquals($subspecialty->id ?? null, $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_SUBSPECIALTY]));
        self::assertEquals($firm->id ?? null, $resolver->resolveId(ReferenceData::LEVEL_REFS[ReferenceData::LEVEL_FIRM]));
    }

    public function getLevels()
    {
        $levels = array();
        foreach (ReferenceData::LEVEL_REFS as $level => $ref) {
            $levels[$ref] = array(
                'level' => $level,
                'ref' => $ref
            );
        }
        return $levels;
    }

    /**
     * @dataProvider getLevels
     * @param integer $level
     * @param string $ref
     * @return void
     */
    public function testGetResolver(int $level, string $ref)
    {
        $resolver = new ReferenceLevelIdResolver();
        if ($level === ReferenceData::LEVEL_INSTALLATION) {
            self::assertNull($resolver->getResolver($ref));
        } else {
            self::assertNotNull($resolver->getResolver($ref));
            self::assertTrue(method_exists($resolver, $resolver->getResolver($ref)));
        }
    }
}
