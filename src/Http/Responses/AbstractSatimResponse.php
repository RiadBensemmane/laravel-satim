<?php

declare(strict_types=1);

namespace LaravelSatim\Http\Responses;

/**
 * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @project laravel-satim
 *
 * @name AbstractSatimResponse
 *
 * @license MIT
 * @copyright (c) 2025 Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
 *
 * @created 21/06/2025
 *
 * @version 1.0.0
 */
abstract class AbstractSatimResponse
{
    public function __construct(
        public ?string $orderStatus = null,
        public ?string $actionCode = null,
        public ?string $actionCodeDescription = null,
        public ?string $errorCode = null,
        public ?string $errorMessage = null,
        public array $params = []
    ) {}

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function registeredPayment(): bool
    {
        return $this->errorMessage === null && $this->errorCode === '0';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function alreadyConfirmed(): bool
    {
        return ($this->params['respCode'] ?? null) === '00' && $this->errorCode === '2' && $this->orderStatus === '2' && $this->actionCode === '0';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function acceptedPayment(): bool
    {
        return ($this->params['respCode'] ?? null) === '00' && $this->errorCode === '0' && $this->orderStatus === '2';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function rejectedPayment(): bool
    {
        return ($this->params['respCode'] ?? null) === '00' && $this->errorCode === '0' && $this->orderStatus === '3';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function refunded(): bool
    {
        return $this->orderStatus === '4';
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function errorMessage(): ?string
    {
        return $this->errorMessage ?: null;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function errorCode(): ?string
    {
        return $this->errorCode ?: null;
    }

    /**
     * @author Abderrahim CHETIBI <chetibi.abderrahim@gmail.com>
     *
     * @created 21/06/2025
     */
    public function successMessage(): ?string
    {
        return $this->params['respCode_desc'] ?? ($this->actionCodeDescription ?: null);
    }
}
