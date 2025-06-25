<?php

use LaravelSatim\Tests\TestCase;
use LaravelSatim\Http\Responses\SatimRegisterResponse;

uses(TestCase::class);

it('can be instantiated with all parameters', function () {
    $response = new SatimRegisterResponse(
        orderId: 'ORDER123',
        formUrl: 'https://satim.dz/payment/form/123',
        errorCode: '0',
        errorMessage: null
    );

    expect($response)->toBeInstanceOf(SatimRegisterResponse::class)
        ->and($response->orderId)->toBe('ORDER123')
        ->and($response->formUrl)->toBe('https://satim.dz/payment/form/123')
        ->and($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBeNull();
});

it('can be instantiated with minimal parameters', function () {
    $response = new SatimRegisterResponse();

    expect($response)->toBeInstanceOf(SatimRegisterResponse::class)
        ->and($response->orderId)->toBeNull()
        ->and($response->formUrl)->toBeNull()
        ->and($response->errorCode)->toBeNull()
        ->and($response->errorMessage)->toBeNull();
});

it('creates response from successful API response array', function () {
    $apiResponse = [
        'orderId' => 'ORDER123',
        'formUrl' => 'https://satim.dz/payment/form/123',
        'errorCode' => '0',
        'errorMessage' => null
    ];

    $response = SatimRegisterResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimRegisterResponse::class)
        ->and($response->orderId)->toBe('ORDER123')
        ->and($response->formUrl)->toBe('https://satim.dz/payment/form/123')
        ->and($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBeNull();
});

it('creates response from error API response array', function () {
    $apiResponse = [
        'orderId' => null,
        'formUrl' => null,
        'errorCode' => '1',
        'errorMessage' => 'Invalid merchant credentials'
    ];

    $response = SatimRegisterResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimRegisterResponse::class)
        ->and($response->orderId)->toBeNull()
        ->and($response->formUrl)->toBeNull()
        ->and($response->errorCode)->toBe('1')
        ->and($response->errorMessage)->toBe('Invalid merchant credentials');
});

it('handles missing values in API response', function () {
    $apiResponse = [
        'orderId' => 'ORDER123'
    ];

    $response = SatimRegisterResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimRegisterResponse::class)
        ->and($response->orderId)->toBe('ORDER123')
        ->and($response->formUrl)->toBeNull()
        ->and($response->errorCode)->toBeNull()
        ->and($response->errorMessage)->toBeNull();
});

it('handles empty API response array', function () {
    $apiResponse = [];

    $response = SatimRegisterResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimRegisterResponse::class)
        ->and($response->orderId)->toBeNull()
        ->and($response->formUrl)->toBeNull()
        ->and($response->errorCode)->toBeNull()
        ->and($response->errorMessage)->toBeNull();
});

it('implements SatimResponseInterface', function () {
    $response = new SatimRegisterResponse();

    expect($response)->toBeInstanceOf(\LaravelSatim\Contracts\SatimResponseInterface::class);
});

it('extends AbstractSatimResponse', function () {
    $response = new SatimRegisterResponse();

    expect($response)->toBeInstanceOf(\LaravelSatim\Http\Responses\AbstractSatimResponse::class);
});

it('inherits methods from AbstractSatimResponse', function () {
    $response = new SatimRegisterResponse(
        errorCode: '0'
    );

    expect($response->paymentRegistered())->toBeTrue();
});

it('can detect payment registration success', function () {
    $successResponse = new SatimRegisterResponse(
        orderId: 'ORDER123',
        formUrl: 'https://satim.dz/payment/form/123',
        errorCode: '0',
        errorMessage: null
    );

    expect($successResponse->paymentRegistered())->toBeTrue();
});

it('can detect payment registration failure', function () {
    $failureResponse = new SatimRegisterResponse(
        orderId: null,
        formUrl: null,
        errorCode: '1',
        errorMessage: 'Invalid merchant credentials'
    );

    expect($failureResponse->paymentRegistered())->toBeFalse();
});

it('fromResponse returns SatimRegisterResponse instance', function () {
    $apiResponse = ['orderId' => 'ORDER123'];

    $response = SatimRegisterResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimRegisterResponse::class)
        ->and($response)->toBeInstanceOf(\LaravelSatim\Http\Responses\AbstractSatimResponse::class);
});

it('handles various error codes correctly', function () {
    $errorCodes = [
        ['code' => '0', 'message' => null, 'shouldBeRegistered' => true],
        ['code' => '1', 'message' => 'Invalid merchant credentials', 'shouldBeRegistered' => false],
        ['code' => '2', 'message' => 'Invalid order amount', 'shouldBeRegistered' => false],
        ['code' => '3', 'message' => 'Invalid order number', 'shouldBeRegistered' => false],
        ['code' => '4', 'message' => 'Order already exists', 'shouldBeRegistered' => false],
        ['code' => '5', 'message' => 'System error', 'shouldBeRegistered' => false]
    ];

    foreach ($errorCodes as $errorData) {
        $apiResponse = [
            'orderId' => $errorData['shouldBeRegistered'] ? 'ORDER123' : null,
            'formUrl' => $errorData['shouldBeRegistered'] ? 'https://satim.dz/payment/form/123' : null,
            'errorCode' => $errorData['code'],
            'errorMessage' => $errorData['message']
        ];

        $response = SatimRegisterResponse::fromResponse($apiResponse);

        expect($response->errorCode)->toBe($errorData['code'])
            ->and($response->errorMessage)->toBe($errorData['message'])
            ->and($response->paymentRegistered())->toBe($errorData['shouldBeRegistered']);
    }
});

it('validates property types correctly', function () {
    $response = new SatimRegisterResponse(
        orderId: 'ORDER123',
        formUrl: 'https://satim.dz/payment/form/123',
        errorCode: '0',
        errorMessage: null
    );

    expect($response->orderId)->toBeString()
        ->and($response->formUrl)->toBeString()
        ->and($response->errorCode)->toBeString()
        ->and($response->errorMessage)->toBeNull();
});

it('validates nullable properties', function () {
    $response = new SatimRegisterResponse();

    expect($response->orderId)->toBeNull()
        ->and($response->formUrl)->toBeNull()
        ->and($response->errorCode)->toBeNull()
        ->and($response->errorMessage)->toBeNull();
});

it('handles string conversions in fromResponse', function () {
    $apiResponse = [
        'orderId' => 123,  // Should be converted to string
        'formUrl' => 'https://satim.dz/payment/form/123',
        'errorCode' => 0,  // Should be converted to string
        'errorMessage' => 'Test message'
    ];

    $response = SatimRegisterResponse::fromResponse($apiResponse);

    expect($response->orderId)->toBe('123')
        ->and($response->formUrl)->toBe('https://satim.dz/payment/form/123')
        ->and($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBe('Test message');
});

it('preserves inherited properties from AbstractSatimResponse', function () {
    $response = new SatimRegisterResponse(
        errorCode: '3'
    );

    // Should inherit these properties from AbstractSatimResponse
    expect($response->orderStatus)->toBeNull()
        ->and($response->actionCode)->toBeNull()
        ->and($response->actionCodeDescription)->toBeNull()
        ->and($response->params)->toBe([]);
});

it('validates successful registration response structure', function () {
    $successfulResponse = [
        'orderId' => 'ORDER_12345',
        'formUrl' => 'https://satim.dz/merchant/payment/form?orderId=ORDER_12345&token=abc123',
        'errorCode' => '0',
        'errorMessage' => null
    ];

    $response = SatimRegisterResponse::fromResponse($successfulResponse);

    expect($response->orderId)->toBeString()
        ->and($response->orderId)->toMatch('/^ORDER_/')
        ->and($response->formUrl)->toBeString()
        ->and($response->formUrl)->toStartWith('https://')
        ->and($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBeNull()
        ->and($response->paymentRegistered())->toBeTrue();
});

it('validates error response structure', function () {
    $errorResponse = [
        'orderId' => null,
        'formUrl' => null,
        'errorCode' => '1',
        'errorMessage' => 'Authentication failed'
    ];

    $response = SatimRegisterResponse::fromResponse($errorResponse);

    expect($response->orderId)->toBeNull()
        ->and($response->formUrl)->toBeNull()
        ->and($response->errorCode)->toBe('1')
        ->and($response->errorMessage)->toBeString()
        ->and($response->paymentRegistered())->toBeFalse();
});
