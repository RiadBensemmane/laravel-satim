<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Enums\SatimCurrency;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name SatimConfirmResponse
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
class SatimConfirmResponse extends AbstractSatimResponse implements SatimResponseInterface
{
    public function __construct(
        public ?string $expiration = null,
        public ?string $cardholderName = null,
        public ?float $depositAmount = null,
        public ?SatimCurrency $currency = null,
        public ?string $pan = null,
        public ?string $approvalCode = null,
        public ?int $authCode = null,
        public ?string $orderNumber = null,
        public ?float $amount = null,
        public ?string $svfeResponse = null,
        public ?string $orderStatus = null,
        public ?string $actionCode = null,
        public ?string $actionCodeDescription = null,
        public ?string $errorCode = null,
        public ?string $errorMessage = null,
        public ?string $ip = null,
        public array $params = []
    ) {
        parent::__construct(
            orderStatus: $orderStatus,
            actionCode: $actionCode,
            actionCodeDescription: $actionCodeDescription,
            errorCode: $errorCode,
            errorMessage: $errorMessage,
            params: $params
        );
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public static function fromResponse(array $response): SatimConfirmResponse
    {
        return new SatimConfirmResponse(
            expiration: $response['expiration'] ?? null,
            cardholderName: $response['cardholderName'] ?? null,
            depositAmount: isset($response['depositAmount']) ? (float) ($response['depositAmount'] / 100) : null,
            currency: isset($response['currency']) ? SatimCurrency::tryFrom($response['currency']) : null,
            pan: $response['Pan'] ?? null,
            approvalCode: $response['approvalCode'] ?? null,
            authCode: isset($response['authCode']) ? (int) $response['authCode'] : null,
            orderNumber: $response['OrderNumber'] ?? null,
            amount: isset($response['Amount']) ? (float) ($response['Amount'] / 100) : null,
            svfeResponse: $response['SvfeResponse'] ?? null,
            orderStatus: isset($response['OrderStatus']) ? (string) $response['OrderStatus'] : null,
            actionCode: isset($response['actionCode']) ? (string) $response['actionCode'] : null,
            actionCodeDescription: $response['actionCodeDescription'] ?? null,
            errorCode: $response['ErrorCode'] ?? null,
            errorMessage: $response['ErrorMessage'] ?? null,
            ip: $response['Ip'] ?? null,
            params: [
                'respCode_desc' => $response['params']['respCode_desc'] ?? null,
                'udf1' => $response['params']['udf1'] ?? null,
                'respCode' => $response['params']['respCode'] ?? null,
            ]
        );
    }
}
