<?php

// Copyright 2023. Plesk International GmbH. All rights reserved.

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require __DIR__ . '/vendor/autoload.php';

use WHMCS\Module\Server\Xovi\KaApi;
use WHMCS\Module\Server\Xovi\Logger;
use WHMCS\Module\Server\Xovi\ServerOptions;
use WHMCS\Module\Server\Xovi\ServiceProperties;
use WHMCS\Module\Server\Xovi\PlanCollection;
use WHMCS\Module\Server\Xovi\Plans\StarterPlan;
use WHMCS\Module\Server\Xovi\ProductOptions;
use WHMCS\Module\Server\Xovi\Translator;
use WHMCS\Module\Server\Xovi\UrlHelper;

function xovi_getKaApiClient(array $params): KaApi
{
    return new KaApi(
        $params[ServerOptions::SERVER_SCHEME],
        $params[ServerOptions::SERVER_HOST],
        (int)$params[ServerOptions::SERVER_PORT],
        $params[ServerOptions::SERVER_USERNAME],
        $params[ServerOptions::SERVER_PASSWORD]
    );
}

function xovi_MetaData(): array
{
    return [
        'DisplayName' => 'XOVI',
        'APIVersion' => '1.1',
        'RequiresServer' => true,
        'ServiceSingleSignOnLabel' => false,
    ];
}

function xovi_ConfigOptions(): array
{
    global $CONFIG;

    $plans = new PlanCollection();
    $planOptions = [];

    foreach ($plans->getAll() as $plan) {
        $planOptions[$plan->getId()] = $plan->getName();
    }

    $defaultPlan = new StarterPlan();
    $translator = Translator::getInstance($CONFIG);

    return [
        ProductOptions::PLAN_ID => [
            'FriendlyName' => $translator->translate('xovi_label_plan'),
            'Type' => 'dropdown',
            'Size' => '25',
            'Options' => $planOptions,
            'Default' => $defaultPlan->getId(),
            'SimpleMode' => true,
        ],
    ];
}

function xovi_ClientArea(array $params): string
{
    global $CONFIG;

    $kaApi = xovi_getKaApiClient($params);
    $keyId = $params['model']->serviceProperties->get(ServiceProperties::KEY_ID);
    $translator = Translator::getInstance($CONFIG);

    try {
        $license = $kaApi->retrieveLicense($keyId);
        $activationUrl = UrlHelper::getActivationUrl($license);
        $dashboardUrl = UrlHelper::getDashboardUrl();

        if ($license->getActivationInfo()->isActivated()) {
            return '<div class="tab-content"><div class="row"><div class="col-sm-3 text-left">' . $translator->translate('xovi_button_license_activated') . '</div></div></div><br/>';
        }

        $html = '';

        if (!$license->isTerminated() && !$license->isSuspended()) {
            $html .= '<div class="tab-content"><a class="btn btn-block btn-info" href="' . $activationUrl . '" target="_blank">' . $translator->translate('xovi_button_activate_license') . '</a></div><br/>';
        }

        $html .= '<div class="tab-content"><a class="btn btn-block btn-default" href="' . $dashboardUrl . '" target="_blank">' . $translator->translate('xovi_button_dashboard') . '</a></div><br/>';

        return $html;
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function xovi_CreateAccount(array $params): string
{
    try {
        $plans = new PlanCollection();
        $plan = $plans->getPlanById($params[ProductOptions::PLAN_ID]);
        $kaApi = xovi_getKaApiClient($params);
        $license = $kaApi->createLicense($plan);

        $params['model']->serviceProperties->save([
            ServiceProperties::KEY_ID => $license->getKeyIdentifiers()->getKeyId(),
            ServiceProperties::ACTIVATION_CODE => $license->getKeyIdentifiers()->getActivationCode(),
            ServiceProperties::ACTIVATION_URL => UrlHelper::getActivationUrl($license),
        ]);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function xovi_SuspendAccount(array $params): string
{
    try {
        $keyId = $params['model']->serviceProperties->get(ServiceProperties::KEY_ID);
        $kaApi = xovi_getKaApiClient($params);

        $kaApi->suspendLicense($keyId);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function xovi_UnsuspendAccount(array $params): string
{
    try {
        $keyId = $params['model']->serviceProperties->get(ServiceProperties::KEY_ID);
        $kaApi = xovi_getKaApiClient($params);

        $kaApi->resumeLicense($keyId);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function xovi_TerminateAccount(array $params): string
{
    try {
        $keyId = $params['model']->serviceProperties->get(ServiceProperties::KEY_ID);
        $kaApi = xovi_getKaApiClient($params);

        $kaApi->terminateLicense($keyId);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function xovi_ChangePackage(array $params): string
{
    try {
        $keyId = $params['model']->serviceProperties->get(ServiceProperties::KEY_ID);
        $plans = new PlanCollection();
        $plan = $plans->getPlanById($params[ProductOptions::PLAN_ID]);
        $kaApi = xovi_getKaApiClient($params);

        $kaApi->modifyLicense($keyId, $plan);

        return 'success';
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return $exception->getMessage();
    }
}

function xovi_TestConnection(array $params): array
{
    try {
        $kaApi = xovi_getKaApiClient($params);

        $kaApi->testConnection();

        return [
            'success' => true,
            'error' => '',
        ];
    } catch (Throwable $exception) {
        Logger::error(__FUNCTION__, $params, $exception);

        return [
            'success' => false,
            'error' => $exception->getMessage(),
        ];
    }
}
