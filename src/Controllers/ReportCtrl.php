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
        try {
            $report = $this->dao->getMonitoringTanggapan($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function pelanggaranPerusahaan($request, $response, $args) {
        try {
            $report = $this->dao->getPelanggaranPerusahaan($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function peraturan($request, $response, $args) {
        try {
            $report = $this->dao->getPeraturan($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $report
                )
            );
        } catch (Exception $e) {
            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_ERROR, 
                    $e, 
                    []
                )
            );
        }
    }

    public function getCurrentLogin($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $user = $db->getUserDetail($args['id']);
        return $response->withJson(Helpers::createResponse(
                                ERR_SERVER_SUCCESS, "Success.", $user
        ));
    }

}
