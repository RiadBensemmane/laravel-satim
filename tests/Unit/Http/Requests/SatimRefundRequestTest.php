<?php

use LaravelSatim\Tests\TestCase;
use LaravelSatim\Http\Requests\SatimRefundRequest;
use LaravelSatim\Exceptions\SatimInvalidArgumentException;
use LaravelSatim\Satim;

uses(TestCase::class);

it('can create a valid refund request', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 100.50
    );

    expect($request)->toBeInstanceOf(SatimRefundRequest::class)
        ->and($request->orderId)->toBe('ORDER123')
        ->and($request->amount)->toBe(100.50);
});

it('converts to array correctly', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 250.50
    );

    $array = $request->toArray();

    expect($array)->toHaveKeys([
        'userName', 'password', 'orderId', 'amount'
    ])
        ->and($array['orderId'])->toBe('ORDER123')
        ->and($array['amount'])->toBe(250.50)
        ->and($array['userName'])->toBe('test_username')
        ->and($array['password'])->toBe('test_password');
});

it('converts to request format correctly', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 100.50
    );

    $requestData = $request->toRequest();

    expect($requestData)->toHaveKeys([
        'userName', 'password', 'orderId', 'amount'
    ])
        ->and($requestData['orderId'])->toBe('ORDER123')
        ->and($requestData['amount'])->toBe(10050) // amount in cents
        ->and($requestData['userName'])->toBe('test_username')
        ->and($requestData['password'])->toBe('test_password');
});

it('converts amount to cents correctly in request format', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 999.99
    );

    $requestData = $request->toRequest();
    expect($requestData['amount'])->toBe(99999);
});

it('validates orderId is required', function () {
    SatimRefundRequest::make(orderId: '', amount: 100.0);
})->throws(SatimInvalidArgumentException::class);

it('validates orderId max length', function () {
    expect(fn() => SatimRefundRequest::make(
        orderId: str_repeat('a', 21),
        amount: 100.0
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('accepts valid orderId at max length', function () {
    $validOrderId = str_repeat('a', 20);
    
    $request = SatimRefundRequest::make(
        orderId: $validOrderId,
        amount: 100.0
    );
    
    expect($request->orderId)->toBe($validOrderId);
});

it('validates amount is required', function () {
    expect(fn() => SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 0.0
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('validates minimum amount', function () {
    expect(fn() => SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 49.99
    ))->toThrow(SatimInvalidArgumentException::class, 'The amount field must be at least 50.');
});

it('accepts minimum valid amount', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 50.00
    );
    
    expect($request->amount)->toBe(50.00);
});

it('it throws when decimal precision is not validated', function () {
    SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 100.123
    );
})->throws(SatimInvalidArgumentException::class);

it('implements SatimRequestInterface', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 100.0
    );
    
    expect($request)->toBeInstanceOf(\LaravelSatim\Contracts\SatimRequestInterface::class);
});

it('extends AbstractSatimRequest', function () {
    $request = SatimRefundRequest::make(
        orderId: 'ORDER123',
        amount: 100.0
    );
    
    expect($request)->toBeInstanceOf(\LaravelSatim\Http\Requests\AbstractSatimRequest::class);
});

it('has correct validation error messages for orderId', function () {
    try {
        SatimRefundRequest::make(orderId: '', amount: 100.0);
        expect(false)->toBeTrue('Should have thrown exception');
    } catch (SatimInvalidArgumentException $e) {
        expect($e->getMessage())->toContain('order id');
    }
});

it('has correct validation error messages for amount', function () {
    try {
        SatimRefundRequest::make(orderId: 'ORDER123', amount: 10.0);
        expect(false)->toBeTrue('Should have thrown exception');
    } catch (SatimInvalidArgumentException $e) {
        expect($e->getMessage())->toContain('amount');
    }
});

it('handles float amounts with proper precision', function () {
    $amounts = [50.00, 100.50, 999.99, 1234.56];
    
    foreach ($amounts as $amount) {
        $request = SatimRefundRequest::make(
            orderId: 'ORDER123',
            amount: $amount
        );
        
        expect($request->amount)->toBe($amount);
    }
});