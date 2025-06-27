<?php

declare(strict_types=1);

use LaravelSatim\Contracts\SatimResponseInterface;
use LaravelSatim\Http\Responses\AbstractSatimResponse;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Tests\TestCase;

uses(TestCase::class);

it('should extends and implements satim response classes', function () {
    expect(SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 100000,
            'currency' => '012',
            'approvalCode' => '402130',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre paiement a été accepté',
                'udf1' => '2018105301346',
                'respCode' => '00',
            ],
            'actionCode' => 0,
            'actionCodeDescription' => 'Votre paiement a été accepté',
            'ErrorCode' => '0',
            'ErrorMessage' => 'Success',
            'OrderStatus' => 2,
            'OrderNumber' => '1538298192',
            'Pan' => '628058**1011',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '00',
        ])
    ))
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->toBeInstanceOf(AbstractSatimResponse::class)
        ->toBeInstanceOf(SatimResponseInterface::class)
    ;
});

it('should confirm a valid credit card', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 100000,
            'currency' => '012',
            'approvalCode' => '402130',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre paiement a été accepté',
                'udf1' => '2018105301346',
                'respCode' => '00',
            ],
            'actionCode' => 0,
            'actionCodeDescription' => 'Votre paiement a été accepté',
            'ErrorCode' => '0',
            'ErrorMessage' => 'Success',
            'OrderStatus' => 2,
            'OrderNumber' => '1538298192',
            'Pan' => '628058**1011',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '00',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeTrue()
        ->and($response->fail())->toBeFalse()
        ->and($response->paymentAccepted())->toBeTrue()
        ->and($response->paymentRejected())->toBeFalse()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(1000)
        ->and($response->orderNumber)->toBeString('1538298192')
        ->and($response->orderStatus)->toBeString('2')
        ->and($response->actionCode)->toBeString('0')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('00')
        ->and($response->successMessage())->not()->toBeNull()
    ;
});

it('should confirm a temporarily blocked card', function () {
    $response = SatimConfirmResponse::fromResponse(
        confirmEndpoint([
            'expiration' => '202701',
            'cardholderName' => '**********',
            'depositAmount' => 0,
            'currency' => '012',
            'authCode' => 2,
            'params' => [
                'respCode_desc' => 'Votre transaction a été rejetée, veuillez contacter votre banque.Code erreur :37',
                'udf1' => '2018105301346',
                'respCode' => '37',
            ],
            'actionCode' => 203,
            'actionCodeDescription' => 'processing.error.203',
            'ErrorCode' => '3',
            'ErrorMessage' => 'Order is not confirmed due to order’s state',
            'OrderStatus' => 6,
            'OrderNumber' => '1538298193',
            'Pan' => '628058**6712',
            'Amount' => 100000,
            'Ip' => '127.0.0.1',
            'SvfeResponse' => '37',
        ])
    );

    expect($response)
        ->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeTrue()
        ->and($response->paymentAccepted())->toBeFalse()
        ->and($response->cardTemporarilyBlocked())->toBeTrue()
        ->and($response->amount)->toEqual(1000)
        ->and($response->depositAmount)->toEqual(0)
        ->and($response->orderNumber)->toBeString('1538298193')
        ->and($response->orderStatus)->toBeString('6')
        ->and($response->actionCode)->toBeString('203')
        ->and($response->params)->toBeArray()->toHaveKeys(['respCode_desc', 'udf1', 'respCode'])
        ->and($response->params['respCode'])->toBe('37')
        ->and($response->errorMessage())->not()->toBeNull()
    ;
});
