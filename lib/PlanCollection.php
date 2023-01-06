<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Xovi;

use RuntimeException;
use WHMCS\Module\Server\Xovi\Plans\AgencyPlan;
use WHMCS\Module\Server\Xovi\Plans\ProfessionalPlan;
use WHMCS\Module\Server\Xovi\Plans\StarterPlan;

final class PlanCollection
{
    /**
     * @var Plan[]
     */
    private array $plans = [];

    public function __construct()
    {
        $starterPlan = new StarterPlan();
        $professionalPlan = new ProfessionalPlan();
        $agencyPlan = new AgencyPlan();

        $this->plans[$starterPlan->getId()] = $starterPlan;
        $this->plans[$professionalPlan->getId()] = $professionalPlan;
        $this->plans[$agencyPlan->getId()] = $agencyPlan;
    }

    /**
     * @return Plan[]
     */
    public function getAll(): array
    {
        return $this->plans;
    }

    public function getPlanById(string $id): Plan
    {
        if (!isset($this->plans[$id])) {
            throw new RuntimeException("Plan with id '{$id}' not found");
        }

        return $this->plans[$id];
    }
}
