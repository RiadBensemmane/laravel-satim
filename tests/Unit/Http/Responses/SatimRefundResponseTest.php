<?php

use LaravelSatim\Tests\TestCase;
use LaravelSatim\Http\Responses\SatimRefundResponse;

uses(TestCase::class);

it('can be instantiated with all parameters', function () {
    $response = new SatimRefundResponse(
        orderStatus: '4',
        actionCode: '0',
        actionCodeDescription: 'Refund successful',
        errorCode: '0',
        errorMessage: null,
        params: ['refundId' => 'REF123']
    );

    expect($response)->toBeInstanceOf(SatimRefundResponse::class)
        ->and($response->orderStatus)->toBe('4')
        ->and($response->actionCode)->toBe('0')
        ->and($response->actionCodeDescription)->toBe('Refund successful')
        ->and($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBeNull()
        ->and($response->params)->toBe(['refundId' => 'REF123']);
});

it('can be instantiated with minimal parameters', function () {
    $response = new SatimRefundResponse();

    expect($response)->toBeInstanceOf(SatimRefundResponse::class)
        ->and($response->orderStatus)->toBeNull()
        ->and($response->actionCode)->toBeNull()
        ->and($response->actionCodeDescription)->toBeNull()
        ->and($response->errorCode)->toBeNull()
        ->and($response->errorMessage)->toBeNull()
        ->and($response->params)->toBe([]);
});

it('creates response from successful API response array', function () {
    $apiResponse = [
        'errorCode' => '0',
        'errorMessage' => null
    ];

    $response = SatimRefundResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimRefundResponse::class)
        ->and($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBeNull()
        ->and($response->orderStatus)->toBeNull()
        ->and($response->actionCode)->toBeNull()
        ->and($response->actionCodeDescription)->toBeNull()
        ->and($response->params)->toBe([]);
});

it('creates response from error API response array', function () {
    $apiResponse = [
        'errorCode' => '3',
        'errorMessage' => 'Refund failed'
    ];

    $response = SatimRefundResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimRefundResponse::class)
        ->and($response->errorCode)->toBe('3')
        ->and($response->errorMessage)->toBe('Refund failed')
        ->and($response->orderStatus)->toBeNull()
        ->and($response->actionCode)->toBeNull()
        ->and($response->actionCodeDescription)->toBeNull()
        ->and($response->params)->toBe([]);
});

it('handles empty API response array', function () {
    $apiResponse = [];

    $response = SatimRefundResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimRefundResponse::class)
        ->and($response->errorCode)->toBeNull()
        ->and($response->errorMessage)->toBeNull()
        ->and($response->orderStatus)->toBeNull()
        ->and($response->actionCode)->toBeNull()
        ->and($response->actionCodeDescription)->toBeNull()
        ->and($response->params)->toBe([]);
});

it('implements SatimResponseInterface', function () {
    $response = new SatimRefundResponse();

    expect($response)->toBeInstanceOf(\LaravelSatim\Contracts\SatimResponseInterface::class);
});

it('extends AbstractSatimResponse', function () {
    $response = new SatimRefundResponse();

    expect($response)->toBeInstanceOf(\LaravelSatim\Http\Responses\AbstractSatimResponse::class);
});

it('inherits methods from AbstractSatimResponse', function () {
    $response = new SatimRefundResponse(
        orderStatus: '4',
        actionCode: '0',
        errorCode: '0',
        params: ['respCode' => '00']
    );

    expect($response->paymentRefunded())->toBeTrue()
        ->and($response->successful())->toBeFalse()
        ->and($response->fail())->toBeFalse();
});

it('can detect refund success scenarios', function () {
    // Test successful refund
    $successfulRefundResponse = new SatimRefundResponse(
        orderStatus: '4',
        actionCode: '0',
        errorCode: '0'
    );

    expect($successfulRefundResponse->paymentRefunded())->toBeTrue();

    // Test refund with error
    $failedRefundResponse = new SatimRefundResponse(
        orderStatus: '6',
        actionCode: '203',
        errorCode: '3'
    );

    expect($failedRefundResponse->paymentRefunded())->toBeFalse();
});

it('can detect error scenarios from inherited methods', function () {
    // Test card temporarily blocked during refund
    $blockedResponse = new SatimRefundResponse(
        orderStatus: '6',
        actionCode: '203',
        errorCode: '3',
        params: ['respCode' => '37']
    );

    expect($blockedResponse->cardTemporarilyBlocked())->toBeTrue();

    // Test insufficient balance during refund
    $insufficientResponse = new SatimRefundResponse(
        orderStatus: '6',
        actionCode: '116',
        errorCode: '3',
        params: ['respCode' => '51']
    );

    expect($insufficientResponse->cardBalanceInsufficient())->toBeTrue();
});

it('provides error and success messages from inherited methods', function () {
    $response = new SatimRefundResponse(
        actionCodeDescription: 'Refund processed successfully',
        params: ['respCode_desc' => 'Refund approved']
    );

    expect($response->errorMessage())->toBe('Refund approved')
        ->and($response->successMessage())->toBe('Refund approved');
});

it('provides error codes from inherited methods', function () {
    $responseWithRespCode = new SatimRefundResponse(
        errorCode: '3',
        params: ['respCode' => '37']
    );

    expect($responseWithRespCode->errorCode())->toBe('37');

    $responseWithErrorCode = new SatimRefundResponse(
        errorCode: '3',
        params: []
    );

    expect($responseWithErrorCode->errorCode())->toBe('3');
});

it('handles various refund error scenarios', function () {
    $testCases = [
        [
            'description' => 'Successful refund',
            'data' => [
                'orderStatus' => '4',
                'errorCode' => '0',
                'actionCode' => '0'
            ],
            'expectedRefunded' => true,
            'expectedSuccessful' => false,
            'expectedFail' => false
        ],
        [
            'description' => 'Refund failed - invalid order',
            'data' => [
                'orderStatus' => '6',
                'errorCode' => '3',
                'actionCode' => '100'
            ],
            'expectedRefunded' => false,
            'expectedSuccessful' => false,
            'expectedFail' => true
        ],
        [
            'description' => 'Refund cancelled',
            'data' => [
                'orderStatus' => '5',
                'errorCode' => '3',
                'actionCode' => '10'
            ],
            'expectedRefunded' => false,
            'expectedSuccessful' => false,
            'expectedFail' => true
        ]
    ];

    foreach ($testCases as $testCase) {
        $response = new SatimRefundResponse(
            orderStatus: $testCase['data']['orderStatus'],
            errorCode: $testCase['data']['errorCode'],
            actionCode: $testCase['data']['actionCode']
        );

        expect($response->paymentRefunded())
            ->toBe($testCase['expectedRefunded'])
            ->and($response->successful())
            ->toBe($testCase['expectedSuccessful'])
            ->and($response->fail())
            ->toBe($testCase['expectedFail']);
    }
});

it('validates type safety for constructor parameters', function () {
    $response = new SatimRefundResponse(
        orderStatus: '4',
        actionCode: '0',
        actionCodeDescription: 'Success',
        errorCode: '0',
        errorMessage: 'No error',
        params: ['key' => 'value']
    );

    expect($response->orderStatus)->toBeString()
        ->and($response->actionCode)->toBeString()
        ->and($response->actionCodeDescription)->toBeString()
        ->and($response->errorCode)->toBeString()
        ->and($response->errorMessage)->toBeString()
        ->and($response->params)->toBeArray();
});

it('validates null values are handled correctly', function () {
    $response = new SatimRefundResponse(
        orderStatus: null,
        actionCode: null,
        actionCodeDescription: null,
        errorCode: null,
        errorMessage: null,
        params: []
    );

    expect($response->orderStatus)->toBeNull()
        ->and($response->actionCode)->toBeNull()
        ->and($response->actionCodeDescription)->toBeNull()
        ->and($response->errorCode)->toBeNull()
        ->and($response->errorMessage)->toBeNull()
        ->and($response->params)->toBeArray()
        ->and($response->params)->toBeEmpty();
});

it('fromResponse returns SatimRefundResponse instance', function () {
    $apiResponse = ['errorCode' => '0'];

    $response = SatimRefundResponse::fromResponse($apiResponse);

    expect($response)->toBeInstanceOf(SatimRefundResponse::class)
        ->and($response)->toBeInstanceOf(\LaravelSatim\Http\Responses\AbstractSatimResponse::class);
});

it('handles complex API response structures', function () {
    $complexApiResponse = [
        'errorCode' => '0',
        'errorMessage' => null,
        'additionalData' => [
            'refundId' => 'REF123456',
            'timestamp' => '2025-06-25T10:30:00Z'
        ],
        'status' => 'completed'
    ];

    $response = SatimRefundResponse::fromResponse($complexApiResponse);

    expect($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBeNull()
        ->and($response->orderStatus)->toBeNull()
        ->and($response->actionCode)->toBeNull();
});

it('validates that fromResponse uses SatimResponseAccessor correctly', function () {
    $apiResponse = [
        'errorCode' => 0,
        'errorMessage' => '',
    ];

    $response = SatimRefundResponse::fromResponse($apiResponse);

    expect($response->errorCode)->toBe('0')
        ->and($response->errorMessage)->toBe('');
});

it('can be used in collections and serialization contexts', function () {
    $responses = [
        SatimRefundResponse::fromResponse(['errorCode' => '0']),
        SatimRefundResponse::fromResponse(['errorCode' => '3', 'errorMessage' => 'Error'])
    ];

    expect($responses)->toHaveCount(2)
        ->and($responses[0])->toBeInstanceOf(SatimRefundResponse::class)
        ->and($responses[1])->toBeInstanceOf(SatimRefundResponse::class)
        ->and($responses[0]->errorCode)->toBe('0')
        ->and($responses[1]->errorCode)->toBe('3');
});
