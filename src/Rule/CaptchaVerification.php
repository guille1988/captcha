<?php

namespace Felipetti\Captcha\Rule;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Client\ConnectionException;
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

        $rawResponse = Http::asForm()
            ->post($this->data->getUrl(), $this->buildData($value));

        if(! $rawResponse->successful()){
            $fail($this->data->getErrorMessage('invalid-request'));

            return;
        }

        $response = $rawResponse->json();

        if($this->hasFailed($response)){
            $fail($this->data->getErrorMessage($this->getErrorCode($response)));

            return;
        }

        if($this->scoreSurpassed($response)){
            $fail($this->data->getErrorMessage('threshold-surpassed'));
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
