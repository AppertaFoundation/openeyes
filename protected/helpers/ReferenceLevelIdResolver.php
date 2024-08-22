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
        $this->firm = $firm ?? Yii::app()->session->getSelectedFirm();
        $this->subspecialty = $subspecialty ?? $this->firm->getSubspecialty();
        $this->specialty = $specialty ?? $this->subspecialty->specialty ?? null;
        $this->user = $user ?? Yii::app()->session->getSelectedUser();
    }

    public function resolveId(string $level_id): ?int
    {
        $resolver = $this->getResolver($level_id);
        if ($resolver) {
            return $this->$resolver();
        }
        return null;
    }

    public function getResolver($level_id): ?string
    {
        return self::RESOLVERS[$level_id] ?? null;
    }

    public function resolveInstitutionId(): ?int
    {
        // Cannot use null coalesce if performing a type cast, so need to use a simple condition statement here.
        // This applies to all resolver functions.
        if ($this->institution) {
            return (int)$this->institution->id;
        }
        return null;
    }

    public function resolveSiteId(): ?int
    {
        if ($this->site) {
            return (int)$this->site->id;
        }
        return null;
    }

    public function resolveSubspecialtyId(): ?int
    {
        if ($this->subspecialty) {
            return (int)$this->subspecialty->id;
        }
        return null;
    }

    public function resolveSpecialtyId(): ?int
    {
        if ($this->specialty) {
            return (int)$this->specialty->id;
        }
        return null;
    }

    public function resolveFirmId(): ?int
    {
        if ($this->firm) {
            return (int)$this->firm->id;
        }
        return null;
    }

    public function resolveUserId(): ?int
    {
        if ($this->user) {
            return (int)$this->user->id;
        }
        return null;
    }
}
