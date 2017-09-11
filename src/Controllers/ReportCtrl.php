<?php

namespace App\Controllers;

use App\Helpers;
use App\Dao;

class ReportCtrl {

    private $dao;

    public function __construct($container) {
        $this->container = $container;
        $this->dao = new Dao($container);
    }

    public function monitoringTanggapan($request, $response, $args) {
        $report = $this->dao->getMonitoringTanggapan($request);
        return $response->withJson(
            Helpers::createResponse(
                ERR_SERVER_SUCCESS, 
                "Success", 
                $report
            )
        );
    }

    public function monitoring($request, $response, $args) {
        return $response->withJson(
            Helpers::createResponse(
                ERR_SERVER_SUCCESS, 
                "Success", 
                $this->dao->getAccount()
            )
        );
    }

    public function coba($request, $response, $args) {
        print_r(\Thybag\SharepointApi::lookup('10','MasterJenisPemeriksaan'));
    }

    public function login($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $required_param = [
            'username' => REQ_TYPE_TEXT,
            'password' => REQ_TYPE_TEXT,
        ];

        $input = Helpers::extractRequestParam($request);

        if (Helpers::checkRequiredParam($input, $required_param) === false) {
            Helpers::createLogger($this->container->logger, LOGGER_INFO, __FUNCTION__, $log_message);
            return $response->withJson(Helpers::createResponse(
                                    ERR_SERVER_ERROR, "Insufficient parameters;"
            ));
        } else {
            $user = $db->login($input['username'], md5($input['password']));
            return $response->withJson(Helpers::createResponse(
                                    ERR_SERVER_SUCCESS, count($user) ? "Success." : "User is not registered.", $user
            ));
        }
    }

    public function logout($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);

        $user = $db->logout($request->getHeaderLine('Access-Token'));
        var_dump($user);
        return $response->withJson(Helpers::createResponse(
                                ERR_SERVER_SUCCESS, "Logout successfully."
        ));
    }

    public function register($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $required_param = [
            'username' => REQ_TYPE_TEXT,
            'email' => REQ_TYPE_EMAIL,
            'password' => REQ_TYPE_TEXT,
        ];

        $input = Helpers::extractRequestParam($request);

        if (Helpers::checkRequiredParam($input, $required_param) === false) {
            return $response->withJson(Helpers::createResponse(
                                    ERR_SERVER_ERROR, "Insufficient parameters;"
            ));
        } else {
            return $db->createUser($input['username'], $input['email'], md5($input['password'])) ?
                    $response->withJson(Helpers::createResponse(ERR_SERVER_SUCCESS, "Success.")) :
                    $response->withJson(Helpers::createResponse(ERR_SERVER_ERROR, "Username or email already exist."));
        }
    }

    public function getUser($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $user = $db->getUserDetail($args['id']);
        return $response->withJson(Helpers::createResponse(
                                ERR_SERVER_SUCCESS, "Success.", $user
        ));
    }

    public function getCurrentLogin($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $user = $db->getUserDetail($args['id']);
        return $response->withJson(Helpers::createResponse(
                                ERR_SERVER_SUCCESS, "Success.", $user
        ));
    }

}
