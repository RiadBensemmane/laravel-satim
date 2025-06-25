<?php

use LaravelSatim\Tests\TestCase;
use LaravelSatim\Http\Requests\SatimConfirmRequest;
use LaravelSatim\Enums\SatimLanguage;
use LaravelSatim\Exceptions\SatimInvalidArgumentException;

uses(TestCase::class);

it('can create a valid confirm request', function () {
    $request = SatimConfirmRequest::make(
        orderId: 'ORDER123'
    );

    expect($request)->toBeInstanceOf(SatimConfirmRequest::class)
        ->and($request->orderId)->toBe('ORDER123')
        ->and($request->language)->toBeNull();
});

it('can create a confirm request with language', function () {
    $request = SatimConfirmRequest::make(
        orderId: 'ORDER123',
        language: SatimLanguage::EN
    );

    expect($request)->toBeInstanceOf(SatimConfirmRequest::class)
        ->and($request->orderId)->toBe('ORDER123')
        ->and($request->language)->toBe(SatimLanguage::EN);
});

it('converts to array correctly', function () {
    $request = SatimConfirmRequest::make(
        orderId: 'ORDER123',
        language: SatimLanguage::FR
    );

    $array = $request->toArray();

    expect($array)->toHaveKeys([
        'userName', 'password', 'orderId', 'language'
    ])
        ->and($array['orderId'])->toBe('ORDER123')
        ->and($array['language'])->toBe(SatimLanguage::FR)
        ->and($array['userName'])->toBe('test_username')
        ->and($array['password'])->toBe('test_password');
});

it('converts to request format correctly', function () {
    $request = SatimConfirmRequest::make(
        orderId: 'ORDER123',
        language: SatimLanguage::AR
    );

    $requestData = $request->toRequest();

    expect($requestData)->toHaveKeys([
        'userName', 'password', 'orderId', 'language'
    ])
        ->and($requestData['orderId'])->toBe('ORDER123')
        ->and($requestData['language'])->toBe(SatimLanguage::AR->value);
});

it('converts to request format with null language correctly', function () {
    $request = SatimConfirmRequest::make(
        orderId: 'ORDER123'
    );

    $requestData = $request->toRequest();

    expect($requestData)->toHaveKeys([
        'userName', 'password', 'orderId', 'language'
    ])
        ->and($requestData['orderId'])->toBe('ORDER123')
        ->and($requestData['language'])->toBeNull();
});

it('validates orderId is required', function () {
    SatimConfirmRequest::make(orderId: '');
})->throws(SatimInvalidArgumentException::class);

it('validates orderId max length', function () {
    expect(fn() => SatimConfirmRequest::make(
        orderId: str_repeat('a', 21)
    ))->toThrow(SatimInvalidArgumentException::class);
});

it('accepts valid orderId at max length', function () {
    $validOrderId = str_repeat('a', 20);

    $request = SatimConfirmRequest::make(orderId: $validOrderId);

    expect($request->orderId)->toBe($validOrderId);
});

it('accepts all valid SatimLanguage enum values', function () {
    $languages = [
        SatimLanguage::EN,
        SatimLanguage::FR,
        SatimLanguage::AR
    ];

    foreach ($languages as $language) {
        $request = SatimConfirmRequest::make(
            orderId: 'ORDER123',
            language: $language
        );

        expect($request->language)->toBe($language);
    }
});


it('implements SatimRequestInterface', function () {
    $request = SatimConfirmRequest::make(orderId: 'ORDER123');

    expect($request)->toBeInstanceOf(\LaravelSatim\Contracts\SatimRequestInterface::class);
});

it('extends AbstractSatimRequest', function () {
    $request = SatimConfirmRequest::make(orderId: 'ORDER123');

    expect($request)->toBeInstanceOf(\LaravelSatim\Http\Requests\AbstractSatimRequest::class);
});

it('has correct validation error messages', function () {
    try {
        SatimConfirmRequest::make(orderId: '');
        expect(false)->toBeTrue('Should have thrown exception');
    } catch (SatimInvalidArgumentException $e) {
        expect($e->getMessage())->toContain('order id');
    }
});
