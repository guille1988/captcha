<?php


namespace Felipetti\Captcha\Rule;


use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Felipetti\Captcha\Data\Data;


class CaptchaVerification implements Rule
{

    // This has the error message of bad API uri.
    private string $apiUriIsInvalid = 'API uri is invalid';

    // This is set afterwards, to hold the actual error message, if any.
    private string $actualErrorMessage;

    // This has the config file from data class.
    private array $config;


    /**
     * This method charges config attribute with an instance of data class, to make the package work.
     */
    public function __construct()
    {
        $this->config = app(Data::class)->getConfig();
    }


    /**
     * This select the corresponding response of the error, according to the config file.
     *
     * @param string $errorCode
     * @return string
     */
    public function getErrorMessageFromAllCodes(string $errorCode): string
    {
        return $this->config['error_codes'][$errorCode];
    }


    /**
     * This gets secret key or test secret key, according to the config file and if the application
     * is in production or not.
     *
     * @return string|NULL
     */
    public function getSecretKey(): ?string
    {
        $secretKey = $this->config['secret_key'];

        return empty($this->config['enable_test_key']) ?
            $secretKey :
            (app()->isProduction() ? $secretKey : $this->config['test_secret_key']);
    }


    /**
     * This discerns according to the error, the response message, sets it to the attribute.
     *
     * @param string $actualErrorMessage
     * @return void
     */
    public function setActualErrorMessage(string $actualErrorMessage): void
    {
        $this->actualErrorMessage = $actualErrorMessage;
    }


    /**
     * This has the API request and the conditions to pass verifications.
     *
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $secret = $this->getSecretKey();
        $url = $this->config['url'];
        $remoteip = request()->ip();
        $response = $value;

        $data = compact(['secret', 'response', 'remoteip']);

        $rawResponse = Http::asForm()->post($url, $data);

        if(is_null($rawResponse->json()))
        {
            $this->setActualErrorMessage($this->apiUriIsInvalid);
            return false;
        }
        else
        {
            $successResponse = $rawResponse->json('success');

            if(!$successResponse)
            {
                $errorCode = collect($rawResponse->json('error-codes'))->first();
                $errorMessage = $this->getErrorMessageFromAllCodes($errorCode);
                $this->setActualErrorMessage($errorMessage);
            }
            return $successResponse;
        }
    }


    /**
     * This builds the error message of the package, if any.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->actualErrorMessage;
    }
}
