<?php


namespace PaymentPlugins\PayPalSDK\Client;


abstract class AbstractClient implements ClientInterface
{

    const SUCCESS_RESPONSE_CODES = array(200, 201, 202, 204);

    const ERROR_RESPONSE_CODES = array(400, 401, 403, 404, 405, 406, 415, 422, 429);

    public abstract function request($method, $uri, $options);

    public abstract function post($path, $params = null, $options = null);

    public abstract function get($path);

    public abstract function put($path, $params = null, $options = null);

    public abstract function delete($path, $params = null, $options = null);

    public function getBaseDirectory()
    {
        return dirname(__DIR__, 2);
    }
}