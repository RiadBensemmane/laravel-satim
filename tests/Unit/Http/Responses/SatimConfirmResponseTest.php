<?php

use LaravelSatim\Tests\TestCase;
use LaravelSatim\Http\Responses\SatimConfirmResponse;
use LaravelSatim\Enums\SatimCurrency;

uses(TestCase::class);

it('can be instantiated with all parameters', function () {
    $response = new SatimConfirmResponse(
        expiration: '2025/12',
        cardholderName: 'John Doe',
        depositAmount: 100.50,
        currency: SatimCurrency::DZD,
        pan: '****1234',
        approvalCode: 'APPROVAL123',
        authCode: 123456,
        orderNumber: 'ORD123',
        amount: 250.75,
        svfeResponse: 'SVFE_RESPONSE',
        orderStatus: '2',
        actionCode: '0',
        actionCodeDescription: 'Success',
        errorCode: '0',
        errorMessage: null,
        ip: '192.168.1.1',
        params: ['udf1' => 'test']
    );

    expect($response)->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->expiration)->toBe('2025/12')
        ->and($response->cardholderName)->toBe('John Doe')
        ->and($response->depositAmount)->toBe(100.50)
        ->and($response->currency)->toBe(SatimCurrency::DZD)
        ->and($response->pan)->toBe('****1234')
        ->and($response->approvalCode)->toBe('APPROVAL123')
        ->and($response->authCode)->toBe(123456)
        ->and($response->orderNumber)->toBe('ORD123')
        ->and($response->amount)->toBe(250.75)
        ->and($response->svfeResponse)->toBe('SVFE_RESPONSE')
        ->and($response->orderStatus)->toBe('2')
        ->and($response->actionCode)->toBe('0')
        ->and($response->actionCodeDescription)->toBe('Success')
        ->and($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBeNull()
        ->and($response->ip)->toBe('192.168.1.1')
        ->and($response->params)->toBe(['udf1' => 'test']);
});

it('can be instantiated with minimal parameters', function () {
    $response = new SatimConfirmResponse();

    expect($response)->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->expiration)->toBeNull()
        ->and($response->cardholderName)->toBeNull()
        ->and($response->depositAmount)->toBeNull()
        ->and($response->currency)->toBeNull()
        ->and($response->pan)->toBeNull()
        ->and($response->approvalCode)->toBeNull()
        ->and($response->authCode)->toBeNull()
        ->and($response->orderNumber)->toBeNull()
        ->and($response->amount)->toBeNull()
        ->and($response->svfeResponse)->toBeNull()
        ->and($response->orderStatus)->toBeNull()
        ->and($response->actionCode)->toBeNull()
        ->and($response->actionCodeDescription)->toBeNull()
        ->and($response->errorCode)->toBeNull()
        ->and($response->errorMessage)->toBeNull()
        ->and($response->ip)->toBeNull()
        ->and($response->params)->toBe([]);
});

it('creates response from successful API response array', function () {
    $apiResponse = [
        'expiration' => '2025/12',
        'cardholderName' => 'John Doe',
        'depositAmount' => 10050,
        'currency' => '012',
        'Pan' => '****1234',
        'approvalCode' => 'APPROVAL123',
        'authCode' => 123456,
        'OrderNumber' => 'ORD123',
        'Amount' => 25075,
        'SvfeResponse' => 'SVFE_RESPONSE',
        'OrderStatus' => '2',
        'actionCode' => '0',
        'actionCodeDescription' => 'Success',
        'ErrorCode' => '0',
        'ErrorMessage' => null,
        'Ip' => '192.168.1.1',
        'params' => [
            'udf1' => 'test_udf1',
            'respCode' => '00',
            'respCode_desc' => 'Transaction approved'
        ]
    ];

    $response = SatimConfirmResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->expiration)->toBe('2025/12')
        ->and($response->cardholderName)->toBe('John Doe')
        ->and($response->depositAmount)->toBe(100.50)
        ->and($response->currency)->toBe(SatimCurrency::DZD)
        ->and($response->pan)->toBe('****1234')
        ->and($response->approvalCode)->toBe('APPROVAL123')
        ->and($response->authCode)->toBe(123456)
        ->and($response->orderNumber)->toBe('ORD123')
        ->and($response->amount)->toBe(250.75)
        ->and($response->svfeResponse)->toBe('SVFE_RESPONSE')
        ->and($response->orderStatus)->toBe('2')
        ->and($response->actionCode)->toBe('0')
        ->and($response->actionCodeDescription)->toBe('Success')
        ->and($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBeNull()
        ->and($response->ip)->toBe('192.168.1.1')
        ->and($response->params)->toBe([
            'udf1' => 'test_udf1',
            'respCode' => '00',
            'respCode_desc' => 'Transaction approved'
        ]);
});

it('creates response from error API response array', function () {
    $apiResponse = [
        'OrderStatus' => '6',
        'actionCode' => '203',
        'actionCodeDescription' => 'Card temporarily blocked',
        'ErrorCode' => '3',
        'ErrorMessage' => 'Payment failed',
        'params' => [
            'respCode' => '37',
            'respCode_desc' => 'Card temporarily blocked'
        ]
    ];

    $response = SatimConfirmResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->orderStatus)->toBe('6')
        ->and($response->actionCode)->toBe('203')
        ->and($response->actionCodeDescription)->toBe('Card temporarily blocked')
        ->and($response->errorCode)->toBe('3')
        ->and($response->errorMessage)->toBe('Payment failed')
        ->and($response->params)->toBe([
            'udf1' => null,
            'respCode' => '37',
            'respCode_desc' => 'Card temporarily blocked'
        ]);
});

it('handles missing values in API response', function () {
    $apiResponse = [
        'OrderNumber' => 'ORD123',
        'Amount' => 10000
    ];

    $response = SatimConfirmResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response->orderNumber)->toBe('ORD123')
        ->and($response->amount)->toBe(100.0)
        ->and($response->expiration)->toBeNull()
        ->and($response->cardholderName)->toBeNull()
        ->and($response->currency)->toBeNull()
        ->and($response->params)->toBe([
            'udf1' => null,
            'respCode' => null,
            'respCode_desc' => null
        ]);
});

it('converts amounts from cents correctly', function () {
    $testCases = [
        ['cents' => 0, 'expected' => 0.0],
        ['cents' => 100, 'expected' => 1.0],
        ['cents' => 10050, 'expected' => 100.50],
        ['cents' => 99999, 'expected' => 999.99],
        ['cents' => 123456, 'expected' => 1234.56]
    ];

    foreach ($testCases as $testCase) {
        $apiResponse = [
            'Amount' => $testCase['cents'],
            'depositAmount' => $testCase['cents']
        ];

        $response = SatimConfirmResponse::fromResponse($apiResponse);

        expect($response->amount)->toBe($testCase['expected'])
            ->and($response->depositAmount)->toBe($testCase['expected']);
    }
});

it('handles currency enum conversion correctly', function () {
    $apiResponse = [
        'currency' => '012'
    ];

    $response = SatimConfirmResponse::fromResponse($apiResponse);

    expect($response->currency)->toBe(SatimCurrency::DZD);
});

it('handles invalid currency', function () {
    $apiResponse = [
        'currency' => '999'
    ];

    $response = SatimConfirmResponse::fromResponse($apiResponse);

    expect($response->currency)->toBeNull();
});

it('implements SatimResponseInterface', function () {
    $response = new SatimConfirmResponse();

    expect($response)->toBeInstanceOf(\LaravelSatim\Contracts\SatimResponseInterface::class);
});

it('extends AbstractSatimResponse', function () {
    $response = new SatimConfirmResponse();

    expect($response)->toBeInstanceOf(\LaravelSatim\Http\Responses\AbstractSatimResponse::class);
});

it('inherits methods from AbstractSatimResponse', function () {
    $response = new SatimConfirmResponse(
        orderStatus: '2',
        actionCode: '0',
        errorCode: '0',
        params: ['respCode' => '00']
    );

    expect($response->successful())->toBeTrue()
        ->and($response->fail())->toBeFalse()
        ->and($response->paymentAccepted())->toBeTrue();
});

it('can detect card validation statuses from inherited methods', function () {
    // Test card temporarily blocked
    $blockedResponse = new SatimConfirmResponse(
        orderStatus: '6',
        actionCode: '203',
        errorCode: '3',
        params: ['respCode' => '37']
    );

    expect($blockedResponse->cardTemporarilyBlocked())->toBeTrue();

    // Test card valid
    $validResponse = new SatimConfirmResponse(
        orderStatus: '2',
        actionCode: '0',
        errorCode: '0',
        params: ['respCode' => '00']
    );

    expect($validResponse->cardValid())->toBeTrue();

    // Test insufficient balance
    $insufficientResponse = new SatimConfirmResponse(
        orderStatus: '6',
        actionCode: '116',
        errorCode: '3',
        params: ['respCode' => '51']
    );

    expect($insufficientResponse->cardBalanceInsufficient())->toBeTrue();
});

it('can detect payment statuses from inherited methods', function () {
    // Test payment confirmed
    $confirmedResponse = new SatimConfirmResponse(
        orderStatus: '2',
        actionCode: '0',
        errorCode: '2',
        params: ['respCode' => '00']
    );

    expect($confirmedResponse->paymentConfirmed())->toBeTrue();

    // Test payment refunded
    $refundedResponse = new SatimConfirmResponse(
        orderStatus: '4'
    );

    expect($refundedResponse->paymentRefunded())->toBeTrue();

    // Test payment cancelled
    $cancelledResponse = new SatimConfirmResponse(
        errorCode: '3',
        actionCode: '10'
    );

    expect($cancelledResponse->paymentCancelled())->toBeTrue();
});

it('provides error and success messages from inherited methods', function () {
    $response = new SatimConfirmResponse(
        actionCodeDescription: 'Transaction successful',
        params: ['respCode_desc' => 'Payment approved']
    );

    expect($response->errorMessage())->toBe('Payment approved')
        ->and($response->successMessage())->toBe('Payment approved');
});

it('provides error codes from inherited methods', function () {
    $responseWithRespCode = new SatimConfirmResponse(
        errorCode: '3',
        params: ['respCode' => '37']
    );

    expect($responseWithRespCode->errorCode())->toBe('37');

    $responseWithErrorCode = new SatimConfirmResponse(
        errorCode: '3',
        params: []
    );

    expect($responseWithErrorCode->errorCode())->toBe('3');
});

it('handles empty params array correctly', function () {
    $apiResponse = [
        'OrderNumber' => 'ORD123',
        'params' => []
    ];

    $response = SatimConfirmResponse::fromResponse($apiResponse);

    expect($response->params)->toBe([
        'udf1' => null,
        'respCode' => null,
        'respCode_desc' => null
    ]);
});

it('handles missing params key correctly', function () {
    $apiResponse = [
        'OrderNumber' => 'ORD123'
    ];

    $response = SatimConfirmResponse::fromResponse($apiResponse);

    expect($response->params)->toBe([
        'udf1' => null,
        'respCode' => null,
        'respCode_desc' => null
    ]);
});

it('fromResponse returns SatimConfirmResponse instance', function () {
    $apiResponse = ['OrderNumber' => 'ORD123'];

    $response = SatimConfirmResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimConfirmResponse::class)
        ->and($response)->toBeInstanceOf(\LaravelSatim\Http\Responses\AbstractSatimResponse::class);
});
