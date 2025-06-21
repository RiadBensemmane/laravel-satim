<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Requests;

use Illuminate\Support\Facades\Validator;
use LaravelSatim\Contracts\SatimRequestInterface;
use LaravelSatim\Exceptions\SatimInvalidArgumentException;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 * @project laravel-satim
 * @package LaravelSatim\Http\Requests
 * @name SatimRefundRequest
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 * @version 1.0.0
 */
final class SatimRefundRequest extends AbstractSatimRequest implements SatimRequestInterface
{
    /**
     * @param string $orderId
     * @param float $amount
     * @throws SatimInvalidArgumentException
     */
    public function __construct(
        public string $orderId,
        public float $amount
    ) {
        $this->validate();
    }

    /**
     * @param string $orderId
     * @param float $amount
     * @return SatimRefundRequest
     * @throws SatimInvalidArgumentException
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     * @created 21/06/2025
     */
    public static function make(
        string $orderId,
        float $amount
    ): SatimRefundRequest {
        return new SatimRefundRequest(
            orderId: $orderId,
            amount: $amount
        );
    }

    /**
     * @return array
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     * @created 21/06/2025
     */
    public function toArray(): array
    {
        return [
            'userName' => $this->userName(),
            'password' => $this->password(),
            'orderId' => $this->orderId,
            'amount' => $this->amount
        ];
    }

    /**
     * @return array
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     * @created 21/06/2025
     */
    public function toRequest(): array
    {
        return array_merge($this->toArray(), [
            'amount' => (int)($this->amount * 100)
        ]);
    }

    /**
     * @return void
     * @throws SatimInvalidArgumentException
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     * @created 21/06/2025
     */
    public function validate(): void
    {
        $validator = Validator::make($this->toArray(), [
            'userName' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'max:30'],
            'orderId' => ['required', 'string', 'max:20'],
            'amount' => ['required', 'decimal:2', 'min:50']
        ]);

        if ($validator->fails()) {
            throw new SatimInvalidArgumentException($validator->errors()->first());
        }
    }
}
