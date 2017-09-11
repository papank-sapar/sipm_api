<?php

namespace App;

use Thybag\SharePointAPI;
use App\Helpers;

class Dao {

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
        $this->sp_client->lowercaseIndexs(FALSE);
    }

    public function getAccount() {
        // return $this->sp_client->read('MasterSuratTugas', 10, ['TemaPengawasan' => '8']); 
        // $sp->query('list of pets')->raw_where('<Eq><FieldRef Name="Title" /><Value Type="Text">Hello World</Value></Eq>')->limit(10)->get();
        return $this->master_data->getTemaPengawasan();
    }

    public function getMonitoringTanggapan($request) {
        $perusahaan = Helpers::checkResults($this->getPerusahaan($request->getQueryParam('perusahaan')));
        $jenis_pemeriksaan = Helpers::checkResults($this->getJenisPemeriksaan($request->getQueryParam('jenis_pemeriksaan')));
        $tema_pengawasan = Helpers::checkResults($this->getTemaPengawasan($request->getQueryParam('tema_pengawasan')));
        $user_account = Helpers::checkResults($this->getUserAccount($request->getQueryParam('user_account')));
        $pic = Helpers::checkResults($this->getPIC());

        return $pic;
    }

    public function getTemaPengawasan($tema_pengawasan = null) {
        $list_name = 'MasterTemaPengawasan';
        $fields = ['ID', 'TemaPengawasan'];

        if ($tema_pengawasan) {
            return $this->sp_client
                ->query($list_name)
                ->fields($fields)
                ->raw_where('<Contains><FieldRef Name="TemaPengawasan" /><Value Type="Text">' . $tema_pengawasan . '</Value></Contains>')->get();

        } else {
            return $this->sp_client
                ->query($list_name)
                ->fields($fields)
                ->get();            
        }
    }

    public function getPIC() {
        $list_name = 'TimSuratTugas';
        $fields = ['ID', 'UserAccount', 'SuratTugas'];

        return $this->sp_client
            ->query($list_name)
            ->fields($fields)
            ->raw_where('<And>
                <Contains><FieldRef Name="PIC" /><Value Type="Boolean">Yes</Value></Contains>
                <IsNotNull><FieldRef Name="SuratTugas" /></IsNotNull>
            </And>')
            ->get();
    }

    public function getPerusahaan($perusahaan = null) {
        $list_name = 'ProfilPihakInstitusi';
        $fields = ['ID', 'Nama'];

        if ($perusahaan) {
            return $this->sp_client
                ->query($list_name)
                ->fields($fields)
                ->raw_where('<Contains><FieldRef Name="Nama" /><Value Type="Text">' . $perusahaan . '</Value></Contains>')
                ->get();

        } else {
            return $this->sp_client
                ->query($list_name)
                ->fields($fields)
                ->get();            
        }
    }

    public function getJenisPemeriksaan($jenis_pemeriksaan = null) {
        $list_name = 'MasterJenisPemeriksaan';
        $fields = ['ID', 'JenisPemeriksaan'];

        if ($jenis_pemeriksaan) {
            return $this->sp_client
                ->query($list_name)
                ->fields($fields)
                ->raw_where('<Contains><FieldRef Name="JenisPemeriksaan" /><Value Type="Text">' . $jenis_pemeriksaan . '</Value></Contains>')
                ->get();

        } else {
            return $this->sp_client
                ->query($list_name)
                ->fields($fields)
                ->get();            
        }
    }

    public function getUserAccount($nama = null) {
        $list_name = 'UserAccount';
        $fields = ['ID', 'NamaLengkap'];

        if ($nama) {
            return $this->sp_client
                ->query($list_name)
                ->fields($fields)
                ->raw_where('<Contains><FieldRef Name="NamaLengkap" /><Value Type="Text">' . $nama . '</Value></Contains>')
                ->get();

        } else {
            return $this->sp_client
                ->query($list_name)
                ->fields($fields)
                ->get();            
        }
    }
}
