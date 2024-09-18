<?php


namespace PaymentPlugins\PayPalSDK\Client;


use PaymentPlugins\PayPalSDK\Exception\AccessTokenExpiredException;
use PaymentPlugins\PayPalSDK\Exception\ApiException;
use PaymentPlugins\PayPalSDK\Exception\AuthenticationException;
use PaymentPlugins\PayPalSDK\Exception\AuthorizationException;
use PaymentPlugins\PayPalSDK\Exception\BadRequestException;
use PaymentPlugins\PayPalSDK\Exception\InternalServerException;
use PaymentPlugins\PayPalSDK\Exception\NotFoundException;
use PaymentPlugins\PayPalSDK\Exception\UnprocessableEntity;
use PaymentPlugins\PayPalSDK\Utils;

abstract class BaseHttpClient extends AbstractClient
{

    const DEFAULT_TIMEOUT = 80;

    private $http;

    private $client_id;

    private $secret_key;

    protected $environment = self::PRODUCTION;

    private $access_token;

    private $tokenHandler;

    private $merchantId;

    /**
     * @param array $config
     * @param mixed $http Can be any type of client like \GuzzleHttp\Client that supports a request method.
     */
    public function __construct($config = array(), $http = null)
    {
        $this->initialize($config);

        $this->http = $http;
    }

    protected function initialize($config)
    {
        if (!isset($config['client_id'])) {
            throw new \InvalidArgumentException('$client_id is a required property');
        }
        if (!isset($config['secret_key'])) {
            throw new \InvalidArgumentException('$secret_key is a required property');
        }
        $this->client_id = $config['client_id'];
        $this->secret_key = $config['secret_key'];

        if (isset($config['access_token'])) {
            $this->access_token = $config['access_token'];
        }
        if (isset($config['environment'])) {
            $this->environment = $config['environment'];
        }
    }

    public abstract function environment($env);

    public function request($method, $path, $responseClass = null, $params = null, $options = array())
    {
        try {
            $options = (object)$this->getRequestOptions($options);
            if ($params) {
                if ($method === 'GET') {
                    $options->query = $params;
                } else {
                    if ($options->headers['Content-Type'] === 'application/json') {
                        $options->json = $params;
                    } elseif ($options->headers['Content-Type'] === 'application/x-www-form-urlencoded') {
                        $options->form_params = $params;
                    }
                }
            }

            $response = $this->http->request(strtoupper($method), $this->getRequestUrl($path), (array)$options);

            list($statusCode, $body) = $this->handleRequestResponse($response);

            if (in_array($statusCode, self::SUCCESS_RESPONSE_CODES)) {
                $body = $this->decodeResponseBody($body);
                if ($responseClass) {
                    return Utils::convertResponseToObject($responseClass, $body, $params);
                }

                return (object)$body;
            } else {
                //throw exception based on status code
                $this->handleApiException($statusCode, $this->decodeResponseBody($body));
            }
        } catch (AccessTokenExpiredException $e) {
            if (($handler = $this->getExpiredTokenHandler())) {
                call_user_func($handler, $this);
            }
            unset($options->headers['Authorization']);

            return $this->request($method, $path, $responseClass, $params, (array)$options);
        }
    }

    private function handleApiException($statusCode, $response)
    {
        $args = [$statusCode, $response];
        switch ($statusCode) {
            case 400:
                throw new BadRequestException(...$args);
            case 401:
                // if an access token was used, it expired
                if ($this->access_token && isset($response['error']) && $response['error'] === 'invalid_token') {
                    throw new AccessTokenExpiredException(...$args);
                } else {
                    throw new AuthenticationException(...$args);
                }
            case 403:
                throw new AuthorizationException(...$args);
            case 404:
                throw new NotFoundException(...$args);
            case 422:
                throw new UnprocessableEntity(...$args);
            case 500:
                throw new InternalServerException(...$args);
        }
    }

    protected function handleRequestResponse($response)
    {
        return [$response->getStatusCode(), $response->getBody()->getContents()];
    }

    public function post($path, $params = null, $options = null)
    {
        return $this->request('POST', $path, $params, $options);
    }

    public function get($uri)
    {
        // TODO: Implement get() method.
    }

    public function put($path, $params = null, $options = null)
    {
        // TODO: Implement put() method.
    }

    public function delete($path, $params = null, $options = null)
    {
        // TODO: Implement delete() method.
    }

    private function getRequestOptions($options)
    {
        return Utils::deepMerge($options, ['headers' => $this->getHeaders()]);
    }

    protected function getHeaders()
    {
        return array(
            'Authorization' => $this->getAuthorizationHeader(),
            'Content-Type' => 'application/json',
            'PayPal-Partner-Attribution-Id' => 'PaymentPlugins_PCP',
            'Prefer' => 'return=representation'
        );
    }

    private function getAuthorizationHeader()
    {
        if ($this->isAccessTokenAuthentication()) {
            return $this->getBearerAuthorizationHeader();
        } else {
            return $this->getBasicAuthorizationHeader();
        }
    }

    public function getBearerAuthorizationHeader()
    {
        return 'Bearer ' . $this->getAccessToken();
    }

    public function getBasicAuthorizationHeader()
    {
        return 'Basic ' . $this->getBasicAuth();
    }

    private function isAccessTokenAuthentication()
    {
        return $this->access_token;
    }

    public function getAccessToken()
    {
        return $this->access_token;
    }


    public function getBasicAuth()
    {
        return base64_encode(sprintf('%s:%s', $this->client_id, $this->secret_key));
    }

    protected function baseUrl()
    {
        switch ($this->environment) {
            case self::PRODUCTION:
                return 'https://api-m.paypal.com';
            case self::SANDBOX:
                return 'https://api-m.sandbox.paypal.com';
        }

        return '';
    }

    public function getRequestUrl($uri)
    {
        return $this->baseUrl() . '/' . rtrim($uri, '/\\');
    }

    private function decodeResponseBody($body)
    {
        return json_decode($body, true);
    }

    public function registerExpiredTokenHandler($handler)
    {
        $this->tokenHandler = $handler;
    }

    public function getExpiredTokenHandler()
    {
        return $this->tokenHandler;
    }

    public function setAccessToken($token)
    {
        $this->access_token = $token;
    }

    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
    }

    public function getMerchantId()
    {
        return $this->merchantId;
    }

    public function getEnvironment()
    {
        return $this->environment;
    }

}