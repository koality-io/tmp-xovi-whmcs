<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\Xovi\Plans;

use WHMCS\Module\Server\Xovi\Plan;

final class StarterPlan implements Plan
{
    public function getId(): string
    {
        return 'starter';
    }

    public function getName(): string
    {
        return 'XOVI NOW Starter';
    }

    public function getPlanApiConst(): string
    {
        return 'XO-NOW-PP-STA-1M';
    }
}
