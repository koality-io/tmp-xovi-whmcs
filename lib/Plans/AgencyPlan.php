<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\XoviNow\Plans;

use WHMCS\Module\Server\XoviNow\Plan;

final class AgencyPlan implements Plan
{
    public function getId(): string
    {
        return 'agency';
    }

    public function getName(): string
    {
        return 'XOVI NOW Agency';
    }

    public function getPlanApiConst(): string
    {
        return 'XO-NOW-PP-AGE-1M';
    }
}
