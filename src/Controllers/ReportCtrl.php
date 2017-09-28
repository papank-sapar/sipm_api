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

    public function pelanggaranPerPerusahaan($request, $response, $args) {
        try {
            $report = $this->dao->getPelanggaranPerPerusahaan($request);

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

    public function getPeraturan($request, $response, $args) {
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

    public function getProfil($request, $response, $args) {
        try {
            $report = $this->dao->getProfil($request);

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

    public function getAlamatIndividu($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $user = $db->getUserDetail($args['id']);
        return $response->withJson(Helpers::createResponse(
                                ERR_SERVER_SUCCESS, "Success.", $user
        ));
    }

    public function getIdentitasIndividu($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $user = $db->getUserDetail($args['id']);
        return $response->withJson(Helpers::createResponse(
                                ERR_SERVER_SUCCESS, "Success.", $user
        ));
    }

    public function getProfilInstitusi($request, $response, $args) {
        try {
            $report = $this->dao->getProfilInstitusi($request);

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

    public function getShp($request, $response, $args) {
        try {
            $report = $this->dao->getShp($request);

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

    public function getShpKesimpulanPihak($request, $response, $args) {
        try {
            $report = $this->dao->getShpKesimpulanPihak($request);

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

    public function getShpPeraturan($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $user = $db->getUserDetail($args['id']);
        return $response->withJson(Helpers::createResponse(
                                ERR_SERVER_SUCCESS, "Success.", $user
        ));
    }

    public function getShpPihak($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $user = $db->getUserDetail($args['id']);
        return $response->withJson(Helpers::createResponse(
                                ERR_SERVER_SUCCESS, "Success.", $user
        ));
    }

    public function getSuratTugas($request, $response, $args) {
        try {
            $report = $this->dao->getSuratTugas($request);

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

    public function getTimSuratTugas($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $user = $db->getUserDetail($args['id']);
        return $response->withJson(Helpers::createResponse(
                                ERR_SERVER_SUCCESS, "Success.", $user
        ));
    }

    public function getUser($request, $response, $args) {
        $db = new Database($this->container->get('settings')['database']);
        $user = $db->getUserDetail($args['id']);
        return $response->withJson(Helpers::createResponse(
                                ERR_SERVER_SUCCESS, "Success.", $user
        ));
    }
}
