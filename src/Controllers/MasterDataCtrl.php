<?php

namespace App\Controllers;

use App\Helpers;
use App\Dao;

class MasterDataCtrl {

    private $dao;

    public function __construct($container) {
        $this->container = $container;
        $this->dao = new Dao($container);
    }

    public function getTemaPengawasan($request, $response, $args) {
        try {
            $list_tema_pengawasan = $this->dao->getTemaPengawasan($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $list_tema_pengawasan
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

    public function getJenisPemeriksaan($request, $response, $args) {
        try {
            $list_jenis_pemeriksaan = $this->dao->getJenisPemeriksaan($request);

            return $response->withJson(
                Helpers::createResponse(
                    ERR_SERVER_SUCCESS, 
                    "Success", 
                    $list_jenis_pemeriksaan
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
}
