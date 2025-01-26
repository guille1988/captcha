<?php

namespace Felipetti\Captcha\Rule;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Felipetti\Captcha\ValueObject\Data;

class CaptchaVerification implements ValidationRule
{
    // Has the data required to perform all operations of the package.
    private Data $data;

    /**
     * Charges data attribute with an instance of data class, to make the package work.
     */
    public function __construct()
    {
        $this->data = new Data;
    }

    /**
     * This performs all the operations needed for the package to work.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     * @throws ConnectionException
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if($this->isNotString($value)) {
            $fail('The :attribute must be a string.');

            return;
        }

        $errorCode = match(true){
            ! ($rawResponse = $this->makeRequest($value))->successful() => 'invalid-request',
            $this->hasFailed($response = $rawResponse->json()) => $this->getErrorCode($response),
            $this->scoreSurpassed($response) => 'threshold-surpassed',
            default => null,
        };

        if(is_string($errorCode)) {
            $fail($this->data->getErrorMessage($errorCode));
        }
    }

    /**
     * Check if the value is not a string.
     *
     * @param mixed $value
     * @return bool
     */
    private function isNotString(mixed $value): bool
    {
        return ! is_string($value);
    }

    /**
     * @param string $value
     * @return PromiseInterface|Response
     * @throws ConnectionException
     */
    private function makeRequest(string $value): PromiseInterface|Response
    {
        return Http::asForm()->post($this->data->getUrl(), $this->buildData($value));
    }

    /**
     * Builds the data to make a POST request to Captcha.
     *
     * @param string $value
     * @return array
     */
    private function buildData(string $value): array
    {
        return [
            'secret' => $this->data->getSecretKey(),
            'remoteip' => request()->ip(),
            'response' => $value
        ];
    }

    /**
     * Check if Captcha response has failed
     *
     * @param array $response
     * @return bool
     */
    private function hasFailed(array $response): bool
    {
        return isset($response['success']) &&
            ! $response['success'];
    }

    /**
     * Gets the proper error code.
     *
     * @param array $response
     * @return string
     */
    private function getErrorCode(array $response): string
    {
        return ! empty($response['error-codes']) ?
            $response['error-codes'][0] :
            'default';
    }

    /**
     * If it is Captcha V3, it will check if the score is lower
     * than the threshold.
     *
     * @param array $response
     * @return bool
     */
    private function scoreSurpassed(array $response): bool
    {
        return isset($response['score']) &&
            $response['score'] < $this->data->getThreshold();
    }
}
