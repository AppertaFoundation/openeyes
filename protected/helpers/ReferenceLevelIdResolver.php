<?php

/**
 * Use this class to resolve IDs for reference level-based queries.
 */
class ReferenceLevelIdResolver
{
    public ?Institution $institution = null;
    public ?Site $site = null;
    public ?Specialty $specialty = null;
    public ?Subspecialty $subspecialty = null;
    public ?Firm $firm = null;
    public ?User $user = null;

    private const RESOLVERS = [
        'institution_id' => 'resolveInstitutionId',
        'site_id' => 'resolveSiteId',
        'specialty_id' => 'resolveSpecialtyId',
        'subspecialty_id' => 'resolveSubspecialtyId',
        'firm_id' => 'resolveFirmId',
        'user_id' => 'resolveUserId',
    ];

    public function __construct(
        ?Institution $institution = null,
        ?Site $site = null,
        ?Specialty $specialty = null,
        ?Subspecialty $subspecialty = null,
        ?Firm $firm = null,
        ?User $user = null
    ) {
        $this->institution = $institution ?? Institution::model()->getCurrent();
        $this->site = $site ?? Yii::app()->session->getSelectedSite();
        $this->specialty = $specialty;
        $this->subspecialty = $subspecialty;
        $this->firm = $firm ?? Yii::app()->session->getSelectedFirm();
        $this->user = $user ?? User::model()->findByPk(Yii::app()->user->id);
    }

    public function resolveId(string $level_id)
    {
        $resolver = $this->getResolver($level_id);
        if ($resolver) {
            return $this->$resolver();
        }
    }

    public function getResolver($level_id)
    {
        return self::RESOLVERS[$level_id] ?? null;
    }

    public function resolveInstitutionId()
    {
        return $this->institution->id;
    }

    public function resolveSiteId()
    {
        return $this->site->id;
    }

    public function resolveSubspecialtyId()
    {
        if ($this->subspecialty) {
            return $this->subspecialty->id;
        }
        return $this->firm
            ? $this->firm->getSubspecialtyId()
            : Yii::app()->session->getSelectedFirm()->getSubspecialtyId();
    }

    public function resolveSpecialtyId()
    {
        if ($this->specialty) {
            return $this->specialty->id;
        }
        if ($this->subspecialty) {
            return $this->subspecialty->specialty->id;
        }
        return $this->firm
            ? $this->firm->getSubspecialty()->specialty->id
            : Yii::app()->session->getSelectedFirm()->getSubspecialty()->specialty->id;
    }

    public function resolveFirmId()
    {
        return $this->firm->id;
    }

    public function resolveUserId()
    {
        return $this->user->id;
    }
}
