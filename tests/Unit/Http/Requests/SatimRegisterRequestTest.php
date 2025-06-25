<?php

use LaravelSatim\Tests\TestCase;
use LaravelSatim\Http\Requests\SatimRegisterRequest;
use LaravelSatim\Exceptions\SatimInvalidArgumentException;
use LaravelSatim\Enums\SatimCurrency;
use LaravelSatim\Enums\SatimLanguage;

uses(TestCase::class);

it('can create a valid register request', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.50,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );

    expect($request)->toBeInstanceOf(SatimRegisterRequest::class)
        ->and($request->orderNumber)->toBe('ORD123')
        ->and($request->amount)->toBe(100.50)
        ->and($request->returnUrl)->toBe('https://example.com/return')
        ->and($request->udf1)->toBe('udf1');
});

it('can create a register request with all optional parameters', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 250.75,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf2: 'udf2',
        udf3: 'udf3',
        udf4: 'udf4',
        udf5: 'udf5',
        failUrl: 'https://example.com/fail',
        description: 'Test payment',
        currency: SatimCurrency::DZD,
        language: SatimLanguage::EN
    );

    expect($request->orderNumber)->toBe('ORD123')
        ->and($request->amount)->toBe(250.75)
        ->and($request->returnUrl)->toBe('https://example.com/return')
        ->and($request->udf1)->toBe('udf1')
        ->and($request->udf2)->toBe('udf2')
        ->and($request->udf3)->toBe('udf3')
        ->and($request->udf4)->toBe('udf4')
        ->and($request->udf5)->toBe('udf5')
        ->and($request->failUrl)->toBe('https://example.com/fail')
        ->and($request->description)->toBe('Test payment')
        ->and($request->currency)->toBe(SatimCurrency::DZD)
        ->and($request->language)->toBe(SatimLanguage::EN);
});

it('converts to array correctly', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 250.50,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        description: 'Test payment'
    );

    $array = $request->toArray();

    expect($array)->toHaveKeys([
        'userName', 'password', 'orderNumber', 'amount', 'currency',
        'returnUrl', 'failUrl', 'description', 'language', 'jsonParams'
    ])
        ->and($array['orderNumber'])->toBe('ORD123')
        ->and($array['amount'])->toBe(250.50)
        ->and($array['returnUrl'])->toBe('https://example.com/return')
        ->and($array['description'])->toBe('Test payment')
        ->and($array['userName'])->toBe('test_username')
        ->and($array['password'])->toBe('test_password')
        ->and($array['jsonParams'])->toHaveKeys([
            'force_terminal_id', 'udf1', 'udf2', 'udf3', 'udf4', 'udf5'
        ])
        ->and($array['jsonParams']['force_terminal_id'])->toBe('test_terminal')
        ->and($array['jsonParams']['udf1'])->toBe('udf1');
});

it('converts to request format correctly', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.50,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        currency: SatimCurrency::DZD,
        language: SatimLanguage::FR
    );

    $requestData = $request->toRequest();

    expect($requestData)->toHaveKeys([
        'userName', 'password', 'orderNumber', 'amount', 'currency',
        'returnUrl', 'failUrl', 'description', 'language', 'jsonParams'
    ])
        ->and($requestData['orderNumber'])->toBe('ORD123')
        ->and($requestData['amount'])->toBe(10050)
        ->and($requestData['currency'])->toBe('012')
        ->and($requestData['language'])->toBe('FR')
        ->and($requestData['userName'])->toBe('test_username')
        ->and($requestData['password'])->toBe('test_password')
        ->and($requestData['jsonParams'])->toBeString();
});

it('converts amount to cents correctly in request format', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 999.99,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );

    $requestData = $request->toRequest();
    expect($requestData['amount'])->toBe(99999);
});

it('encodes jsonParams correctly in request format', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.00,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf2: 'udf2'
    );

    $requestData = $request->toRequest();
    $jsonParams = json_decode($requestData['jsonParams'], true);

    expect($jsonParams)->toHaveKeys(['force_terminal_id', 'udf1', 'udf2'])
        ->and($jsonParams['force_terminal_id'])->toBe('test_terminal')
        ->and($jsonParams['udf1'])->toBe('udf1')
        ->and($jsonParams['udf2'])->toBe('udf2');
});

it('validates orderNumber is required', function () {
    SatimRegisterRequest::make(
        orderNumber: '',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );
})->throws(SatimInvalidArgumentException::class);

it('validates orderNumber max length', function () {
    expect(fn() => SatimRegisterRequest::make(
        orderNumber: str_repeat('a', 11),
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('accepts valid orderNumber at max length', function () {
    $validOrderNumber = str_repeat('a', 10);

    $request = SatimRegisterRequest::make(
        orderNumber: $validOrderNumber,
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );

    expect($request->orderNumber)->toBe($validOrderNumber);
});

it('validates amount is required', function () {
    expect(fn() => SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 0.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('validates minimum amount', function () {
    expect(fn() => SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 49.99,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    ))->toThrow(SatimInvalidArgumentException::class, 'The amount field must be at least 50.');
});

it('accepts minimum valid amount', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 50.00,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );

    expect($request->amount)->toBe(50.00);
});

it('throws when decimal precision is not validated', function () {
    SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.123,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );
})->throws(SatimInvalidArgumentException::class);

it('validates returnUrl is required', function () {
    expect(fn() => SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: '',
        udf1: 'udf1'
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('validates returnUrl is a valid URL', function () {
    expect(fn() => SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'not-a-valid-url',
        udf1: 'udf1'
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('validates returnUrl max length', function () {
    $longUrl = 'https://example.com/' . str_repeat('a', 500);

    expect(fn() => SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: $longUrl,
        udf1: 'udf1'
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('validates failUrl is a valid URL when provided', function () {
    expect(fn() => SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        failUrl: 'not-a-valid-url'
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('validates failUrl max length', function () {
    $longUrl = 'https://example.com/' . str_repeat('a', 500);

    expect(fn() => SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        failUrl: $longUrl
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('validates description max length', function () {
    $longDescription = str_repeat('a', 513);

    expect(fn() => SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        description: $longDescription
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('validates udf1 is required', function () {
    expect(fn() => SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: ''
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('validates udf1 max length', function () {
    expect(fn() => SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: str_repeat('a', 21)
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('validates udf2 max length when provided', function () {
    expect(fn() => SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf2: str_repeat('a', 21)
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('accepts valid udf fields at max length', function () {
    $validUdf = str_repeat('a', 20);

    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: $validUdf,
        udf2: $validUdf,
        udf3: $validUdf,
        udf4: $validUdf,
        udf5: $validUdf
    );

    expect($request->udf1)->toBe($validUdf)
        ->and($request->udf2)->toBe($validUdf)
        ->and($request->udf3)->toBe($validUdf)
        ->and($request->udf4)->toBe($validUdf)
        ->and($request->udf5)->toBe($validUdf);
});

it('validates currency enum when provided', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        currency: SatimCurrency::DZD
    );

    expect($request->currency)->toBe(SatimCurrency::DZD);
});

it('validates language enum when provided', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        language: SatimLanguage::FR
    );

    expect($request->language)->toBe(SatimLanguage::FR);
});

it('implements SatimRequestInterface', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );

    expect($request)->toBeInstanceOf(\LaravelSatim\Contracts\SatimRequestInterface::class);
});

it('extends AbstractSatimRequest', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1'
    );

    expect($request)->toBeInstanceOf(\LaravelSatim\Http\Requests\AbstractSatimRequest::class);
});

it('handles float amounts with proper precision', function () {
    $amounts = [50.00, 100.50, 999.99, 1234.56];

    foreach ($amounts as $amount) {
        $request = SatimRegisterRequest::make(
            orderNumber: 'ORD123',
            amount: $amount,
            returnUrl: 'https://example.com/return',
            udf1: 'udf1'
        );

        expect($request->amount)->toBe($amount);
    }
});

it('filters out null values in jsonParams', function () {
    $request = SatimRegisterRequest::make(
        orderNumber: 'ORD123',
        amount: 100.0,
        returnUrl: 'https://example.com/return',
        udf1: 'udf1',
        udf2: 'udf2'
    );

    $requestData = $request->toRequest();
    $jsonParams = json_decode($requestData['jsonParams'], true);

    expect($jsonParams)->toHaveKeys(['force_terminal_id', 'udf1', 'udf2'])
        ->and($jsonParams)->not->toHaveKey('udf3')
        ->and($jsonParams)->not->toHaveKey('udf4')
        ->and($jsonParams)->not->toHaveKey('udf5');
});

it('has correct validation error messages for required fields', function () {
    try {
        SatimRegisterRequest::make(
            orderNumber: '',
            amount: 100.0,
            returnUrl: 'https://example.com/return',
            udf1: 'udf1'
        );
        expect(false)->toBeTrue('Should have thrown exception');
    } catch (SatimInvalidArgumentException $e) {
        expect($e->getMessage())->toContain('order number');
    }

    try {
        SatimRegisterRequest::make(
            orderNumber: 'ORD123',
            amount: 10.0,
            returnUrl: 'https://example.com/return',
            udf1: 'udf1'
        );
        expect(false)->toBeTrue('Should have thrown exception');
    } catch (SatimInvalidArgumentException $e) {
        expect($e->getMessage())->toContain('amount');
    }

    try {
        SatimRegisterRequest::make(
            orderNumber: 'ORD123',
            amount: 100.0,
            returnUrl: '',
            udf1: 'udf1'
        );
        expect(false)->toBeTrue('Should have thrown exception');
    } catch (SatimInvalidArgumentException $e) {
        expect($e->getMessage())->toContain('return url');
    }

    try {
        SatimRegisterRequest::make(
            orderNumber: 'ORD123',
            amount: 100.0,
            returnUrl: 'https://example.com/return',
            udf1: ''
        );
        expect(false)->toBeTrue('Should have thrown exception');
    } catch (SatimInvalidArgumentException $e) {
        expect($e->getMessage())->toContain('udf1');
    }
});
