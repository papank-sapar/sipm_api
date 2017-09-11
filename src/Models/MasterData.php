<?php

namespace App\Models;

use Thybag\SharePointAPI;

class MasterData {

    private $logger;
    private $sp_client;

    // Constructor
    public function __construct($container) {
        $this->sp_client = new SharePointAPI(
            $container->get('settings')['spuser']['username'], 
            $container->get('settings')['spuser']['password'], 
            $container->get('settings')['spuser']['wsdl_path'], 
            'NTLM'
        );
    }

    public function getMasterData ($list_name, $mapping) {
        $result = $this->sp_client
            ->query($list_name)
            ->get();

        $master_data = [];

        foreach ($result as $item) {
            $attribute = [];
            foreach ($mapping as $mapping_key => $mapping_value) {
                $attribute[$mapping_value] = $item[$mapping_key];
            }

            $master_data[] = $attribute;
        }

        return $master_data;
    }

    public function getTemaPengawasan() {
        $mapping = [
            'id' => 'id',
            'temapengawasanid' => 'kode',
            'temapengawasan' => 'tema_pengawasan'
        ];

        return $this->getMasterData('MasterTemaPengawasan', $mapping);
    }

    // public function getTemaPengawasan() {
    //     $mapping = [
    //         'id' => 'id',
    //         'temapengawasanid' => 'kode',
    //         'temapengawasan' => 'tema_pengawasan'
    //     ];

    //     return $this->getMasterData('MasterTemaPengawasan', $mapping);
    // }
}
