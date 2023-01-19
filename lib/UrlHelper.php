<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

namespace WHMCS\Module\Server\XoviNow;

use WHMCS\Module\Server\XoviNow\Dto\License;

final class UrlHelper
{
    private const ACTIVATION_URL = 'https://suite.xovinow.com/licensing/activate?code=';
    private const DASHBOARD_URL = 'https://suite.xovinow.com/';

    public static function getActivationUrl(License $license): string
    {
        return self::ACTIVATION_URL . rawurlencode($license->getKeyIdentifiers()->getActivationCode());
    }

    public static function getDashboardUrl(): string
    {
        return self::DASHBOARD_URL;
    }
}
