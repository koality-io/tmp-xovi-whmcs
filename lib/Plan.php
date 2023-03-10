<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\XoviNow;

interface Plan
{
    public function getId(): string;

    public function getName(): string;

    public function getPlanApiConst(): string;
}
