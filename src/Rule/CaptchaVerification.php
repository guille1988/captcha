<?php

namespace Felipetti\Captcha\Rule;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Felipetti\Captcha\ValueObject\Data;

class CaptchaVerification implements ValidationRule
{
    // This has the data required to perform all operations of the package.
    private Data $data;

    /**
     * This method charges data attribute with an instance of data class, to make the package work.
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
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rawResponse = Http::asForm()->post($this->data->getUrl(), $this->buildData($value));

        if(! $rawResponse->successful()){
            $fail($this->data->getErrorMessage('invalid-request'));
        }

        $response = $rawResponse->json();

        if(! $response['success']){
            $errorCode = isset($response['error-codes']) ?
                $response['error-codes'][0] :
                'default';

            $fail($this->data->getErrorMessage($errorCode));
        }

        if($this->scoreSurpassed($response)){
            $fail($this->data->getErrorMessage('threshold-surpassed'));
        }
    }

    /**
     * This builds the data to make a POST request to CAPTCHA.
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
     * If it is CAPTCHA V3, it will check if the score is lower
     * than the threshold.
     *
     * @param array $response
     * @return bool
     */
    private function scoreSurpassed(array $response): bool
    {
        return isset($response['score']) && $response['score'] < $this->data->getThreshold();
    }
}
