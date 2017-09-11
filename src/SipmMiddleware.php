<?php

namespace App;

use App\Database;
use App\Helpers;

class SipmMiddleware {

    public function __construct($container) {
        $this->container = $container;
        $this->logger = $container->logger;
    }

    public function __invoke($request, $response, $next) {
        // $server_timestamp = time();

        // // Check header request
        // if (!$this->checkRequestHeader($request)) {
        //     Helpers::createLogger($this->container->logger, LOGGER_ERROR, __CLASS__, "Request header is not valid.");
        //     return $response->withStatus(403)->withJson(Helpers::createResponse(ERR_SERVER_REQ_HEADER_NOT_VALID, 'Request header is not valid.'));
        // }

        // // Validate API key
        // if (!$this->validateApiKey($request->getHeaderLine(HTTP_HEADER_API_KEY))) {
        //     Helpers::createLogger($this->container->logger, LOGGER_ERROR, __CLASS__, "API key is not valid. API key: " . $request->getHeaderLine(HTTP_HEADER_API_KEY));
        //     return $response->withStatus(403)->withJson(Helpers::createResponse(ERR_SERVER_API_KEY_NOT_VALID, 'Header request is not valid.s'));
        // }

        // // Validate timestamp
        // if (!$this->validateTimestamp(intval($request->getHeaderLine(HTTP_HEADER_TIMESTAMP)), $server_timestamp)) {
        //     Helpers::createLogger($this->container->logger, LOGGER_ERROR, __CLASS__, "Request timeout. ");
        //     return $response->withStatus(403)->withJson(Helpers::createResponse(ERR_SERVER_REQUEST_TIMEOUT, 'Request time out.'));
        // }

        // // Check route group
        // if (Helpers::getRouteGroup($request->getUri()->getPath()) === GROUP_ROUTE_AUTH) {
        //     return $next($request, $response);
        // }

        // Check access token
        // $db = new Database($this->container->get('settings')['database']);

        // // Validate access token
        // $user_id = $db->validateAccessToken($request->getHeaderLine(HTTP_HEADER_ACCESS_TOKEN));

        // if ($user_id) {
        //     $request->withAttribute(HTTP_REQ_ATT_USER_ID, $user_id);
        //     Helpers::createLogger($this->container->logger, LOGGER_INFO, __CLASS__, "Access token is valid. User ID: " . $user_id);

            return $next($request, $response);
        // }

        // Access token is not valid
        // Helpers::createLogger($this->container->logger, LOGGER_ERROR, __CLASS__, "Access token is not valid.");
        // return $response->withStatus(403)->withJson(Helpers::createResponse(ERR_SERVER_TOKEN_NOT_VALID, 'You must login.'));
    }

    private function checkAccessToken($access_token) {
        $db = new Database($this->container->get('settings')['database']);
        var_dump($db->getCurrentLogin($access_token));
    }

    private function checkRequestHeader($request) {
        // Check API key
        if (!$request->hasHeader(HTTP_HEADER_API_KEY)) {
            Helpers::createLogger($this->logger, LOGGER_ERROR, __CLASS__, "API key is not found.");
            return false;
        }

        // Check Timestamp
        if (!$request->hasHeader(HTTP_HEADER_TIMESTAMP)) {
            Helpers::createLogger($this->logger, LOGGER_ERROR, __CLASS__, "Timestamp is not found.");
            return false;
        }

        // Check access token
        // if (!$request->hasHeader(HTTP_HEADER_ACCESS_TOKEN)) {
        // 	Helpers::createLogger($this->logger, LOGGER_ERROR, __CLASS__, "Access token is not found.");
        // 	return false;
        // }

        return true;
    }

    private function validateApiKey($api_key) {
        return API_KEY_PUBLIC === $api_key;
    }

    private function validateTimestamp($client_timestamp, $server_timestamp) {
        Helpers::createLogger($this->logger, LOGGER_ERROR, __CLASS__, $client_timestamp . " : " . $server_timestamp);
        return true;
    }

}
