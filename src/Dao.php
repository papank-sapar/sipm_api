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
        // $perusahaan = Helpers::checkResults($this->getPerusahaan($request->getQueryParam('perusahaan')));
        // $jenis_pemeriksaan = Helpers::checkResults($this->getJenisPemeriksaan($request->getQueryParam('jenis_pemeriksaan')));
        // $jenis_pemeriksaan = Helpers::createLOV($jenis_pemeriksaan, 'ID', 'JenisPemeriksaan');
        // $tema_pengawasan = Helpers::checkResults($this->getTemaPengawasan($request->getQueryParam('tema_pengawasan')));
        // $tema_pengawasan = Helpers::createLOV($tema_pengawasan, 'ID', 'TemaPengawasan');
        // $user_account = Helpers::checkResults($this->getUserAccount($request->getQueryParam('user_account')));
        // $pic = Helpers::checkResults($this->getPIC());

        // return $this->sampleJSON($request);
        // 
        // // Get DPLE shp
        // $select = [
        //     'ID' => 'id_shp',
        //     'MasterSuratTugas' => 'id_surat_tugas',
        //     'NomorSurat' => 'nomor_surat_ojk'
        // ];

        // $filter = '<Contains><FieldRef Name="NomorSurat" /><Value Type="Text">' . $request->getQueryParam('nomor_surat_ojk') . '</Value></Contains>';
        // $shp = $this->getListData('DPLEshp', $select, $filter);
        // // $date = \Thybag\SharepointApi::dateTime("21-12-2012"); 
        // // echo $date;
        // // die();
        // $select = [
        //     'ID' => 'id_surat_tugas',
        //     'NomorSuratTugas' => 'nomor_surat_tugas',
        //     'AwalPeriode' => 'awal_periode',
        //     'AkhirPeriode' => 'akhir_periode',
        //     'ProfilPihakInstitusi_Nama' => 'id_perusahaan',
        // ];
        
        // // Filter data SHP
        // $count = 1;
        // $filter = '';
        // if ($request->getQueryParam('awal_periode') && $request->getQueryParam('akhir_periode')) {
        //     $count++;
        //     $filter .= '<Geq><FieldRef Name="AwalPeriode" /><Value Type="DateTime">' . \Thybag\SharepointApi::dateTime($request->getQueryParam('awal_periode')) . '</Value></Geq>';
        // } else if ($request->getQueryParam('awal_periode') || $request->getQueryParam('akhir_periode')) {
        //     $count++;
        //     $filter .= '<Geq><FieldRef Name="AwalPeriode" /><Value Type="DateTime">' . \Thybag\SharepointApi::dateTime($request->getQueryParam('awal_periode')) . '</Value></Geq>';
        // }

        // if ($request->getQueryParam('nomor_surat_tugas')) {
        //     $count++;
        //     $filter .= '<Contains><FieldRef Name="NomorSuratTugas" /><Value Type="Text">' . $request->getQueryParam('nomor_surat_tugas') . '</Value></Contains>';
        // }

        // $filter .= '<Eq><FieldRef Name="Direktorat" /><Value Type="Text">DPLE</Value></Eq>';

        // if ($count > 1) {
        //     $filter = '<And>' . $filter . '</And>';
        // }

        // $shp = $this->getListData('MasterSuratTugas', $select, $filter);
        
        // Filter surat tugas
        $select = [
            'ID' => 'id_surat_tugas',
            'NomorSuratTugas' => 'nomor_surat_tugas',
            'AwalPeriode' => 'awal_periode',
            'AkhirPeriode' => 'akhir_periode',
            'ProfilPihakInstitusi_Nama' => 'id_perusahaan',
        ];

        $surat_tugas = $this->sp_client
            ->query('MasterSuratTugas')
            ->fields(array_keys($select));
        $surat_tugas = $surat_tugas
            ->where('Direktorat','=', 'DPLE')
            ->and_where('TemaPengawasan','not_null', '')
            ->and_where('JenisPemeriksaan','not_null', '');

        if ($request->getQueryParam('awal_periode')) 
            $surat_tugas = $surat_tugas->and_where('AwalPeriode','>=',\Thybag\SharepointApi::dateTime($request->getQueryParam('awal_periode')));
        if ($request->getQueryParam('akhir_periode')) 
            $surat_tugas = $surat_tugas->and_where('AkhirPeriode','<=',\Thybag\SharepointApi::dateTime($request->getQueryParam('akhir_periode')));
        if ($request->getQueryParam('nomor_surat_tugas')) 
            $surat_tugas = $surat_tugas->and_where('NomorSuratTugas','contains', $request->getQueryParam('nomor_surat_tugas'));
        $surat_tugas = Helpers::createResults(Helpers::checkResults($surat_tugas->get()), $select);

        // Filter SHP
        $select = [
            'ID' => 'id_shp',
            'MasterSuratTugas' => 'id_surat_tugas',
            'NomorSurat' => 'nomor_surat_ojk',
        ];

        $shp = $this->sp_client
            ->query('DPLEshp')
            ->fields(array_keys($select));
        $shp = $shp->where('MasterSuratTugas','not_null', '');

        if ($request->getQueryParam('nomor_surat_ojk')) 
            $shp = $shp->and_where('NomorSurat','contains',$request->getQueryParam('nomor_surat_ojk'));
        $shp = Helpers::createResults(Helpers::checkResults($shp->get()), $select);

        // Filter PIC
        $select = [
            'ID' => 'id_pic',
            'SuratTugas' => 'id_surat_tugas',
            'UserAccount' => 'id_user_account',
        ];

        $pic = $this->sp_client
            ->query('TimSuratTugas')
            ->fields(array_keys($select));
        $pic = $pic->where('PIC','=', 1)
            ->and_where('UserAccount', 'not_null', '')
            ->and_where('SuratTugas', 'not_null', '');

        $pic = Helpers::createResults(Helpers::checkResults($pic->get()), $select);

        // Filter UserAccount
        $select = [
            'ID' => 'id_user_account',
            'NamaLengkap' => 'pic',
        ];

        $user_account = $this->sp_client
            ->query('UserAccount')
            ->fields(array_keys($select));

        if ($request->getQueryParam('pic')) 
            $user_account = $user_account->where('NamaLengkap','contains', $request->getQueryParam('pic'));

        $user_account = Helpers::createResults(Helpers::checkResults($user_account->get()), $select);

        // Filter Temuan
        $select = [
            'ID' => 'id_temuan',
            'DPLEshp' => 'id_shp',
            'Temuan' => 'temuan'
        ];

        $temuan = $this->sp_client
            ->query('DPLEKesimpulanPihak')
            ->fields(array_keys($select));

        $temuan = $temuan
            ->where('DPLEshp','not_null', '')
            ->and_where('Temuan', 'not_null', '');

        if ($request->getQueryParam('temuan')) 
            $temuan = $temuan->and_where('Temuan','contains', $request->getQueryParam('temuan'));

        $temuan = Helpers::createResults(Helpers::checkResults($temuan->get()), $select);

        // Create structure data
        // $data = [];
        // foreach ($shp as $item) {
        //     $lookup_surat_tugas = Helpers::getLookupFromArray($surat_tugas, 'id_surat_tugas', $item['id_surat_tugas']);
        //     $lookup_temuan = Helpers::getLookupFromArray($temuan, 'id_shp', $item['id_shp']);
        //     $lookup_pic = Helpers::getLookupFromArray($pic, 'id_surat_tugas', $item['id_surat_tugas']);

        //     $attr_pic = "";
        //     if ($lookup_pic) {
        //         $lookup_user_account = Helpers::getLookupFromArray($user_account, 'id_user_account', $lookup_pic['id_user_account']);
        //         $attr_pic = $lookup_user_account? $lookup_user_account['pic']: '';
        //     }

        //     $data[] = [
        //         'id_shp' => $item['id_shp'],
        //         'id_surat_tugas' => $item['id_surat_tugas'],
        //         'nomor_surat_ojk' => $item['nomor_surat_ojk'],
        //         'nomor_surat_tugas' => ($lookup_surat_tugas? $lookup_surat_tugas['nomor_surat_tugas']: ''),
        //         'id_surat_tugas_2' => $lookup_surat_tugas['id_surat_tugas'],
        //         'temuan' => ($lookup_temuan? $lookup_temuan['temuan']: ''),
        //         'pic' => $attr_pic
        //         // 'id_shp' => $item['id_shp'],
        //         // 'id_shp' => $item['id_shp'],
        //     ];
        // }
        
        return $surat_tugas;
    }

    public function getTemaPengawasan($tema_pengawasan = null) {
        $list_name = 'MasterTemaPengawasan';
        $fields = ['ID', 'TemaPengawasan'];
        $result = [];

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

    public function sampleJSON($request) {
        return [
            [
                "perusahaan" => "Perusahaan A",
                "nomor_st" => "ST-001",
                "nomor_surat_ojk" => "OJK-001",
                "pic" => "PIC A",
                "tanggal_pemeriksaan" => "18-22 Juli 2016",
                "jenis_pemeriksaan" => "Pemeriksaan 1",
                "tema_pengawasan" => "Tema Pengawasan 1",
                "lokasi" => "Kantor Pusat",
                "judul_temuan" => "Temuan 1",
                "sisa_waktu" => 20
            ],
            [
                "perusahaan" => "Perusahaan B",
                "nomor_st" => "ST-002",
                "nomor_surat_ojk" => "OJK-002",
                "pic" => "PIC B",
                "tanggal_pemeriksaan" => "18-22 Juli 2016",
                "jenis_pemeriksaan" => "Pemeriksaan 2",
                "tema_pengawasan" => "Tema Pengawasan 2",
                "lokasi" => "Kantor Pusat",
                "judul_temuan" => "Temuan 2",
                "sisa_waktu" => 40
            ]
        ];
    }
}
