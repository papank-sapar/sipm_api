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

    public function getMonitoringTanggapan($request) {

        // Filter Perusahaan
        $select = [
            'ID' => 'id_profil',
            'NamaPihak' => 'perusahaan',
        ];

        $list_perusahaan = $this->sp_client
            ->query('MasterProfil')
            ->fields(array_keys($select));
        $list_perusahaan = $list_perusahaan->where("JenisPihak", "=", "Perusahaan");

        if ($request->getQueryParam('perusahaan')) {
            $list_perusahaan = $list_perusahaan->and_where('NamaPihak','contains', $request->getQueryParam('perusahaan'));
        }

        $list_perusahaan = Helpers::createLOV($list_perusahaan->get(), $select);

        if ($request->getQueryParam('perusahaan') && !count($list_perusahaan)) return [];

        // Filter surat tugas
        $select = [
            'ID' => 'id_surat_tugas',
            'NomorSuratTugas' => 'nomor_surat_tugas',
            'AwalPeriode' => 'awal_periode',
            'AkhirPeriode' => 'akhir_periode',
            'TemaPengawasan' => 'id_tema_pengawasan',
            'JenisPemeriksaan' => 'id_jenis_pemeriksaan',
            'Lokasi' => 'lokasi',
        ];

        $list_surat_tugas = $this->sp_client
            ->query('MasterSuratTugas')
            ->fields(array_keys($select));
        $list_surat_tugas = $list_surat_tugas
            ->where('Direktorat','=', 'DPLE');

        if ($request->getQueryParam('awal_periode')) 
            $list_surat_tugas = $list_surat_tugas->and_where('AwalPeriode','>=',\Thybag\SharepointApi::dateTime($request->getQueryParam('awal_periode')));
        if ($request->getQueryParam('akhir_periode')) 
            $list_surat_tugas = $list_surat_tugas->and_where('AkhirPeriode','<=',\Thybag\SharepointApi::dateTime($request->getQueryParam('akhir_periode')));
        if ($request->getQueryParam('nomor_surat_tugas')) 
            $list_surat_tugas = $list_surat_tugas->and_where('NomorSuratTugas','contains', $request->getQueryParam('nomor_surat_tugas'));
        
        $list_surat_tugas = Helpers::createLOV($list_surat_tugas->get(), $select);
        
        if (($request->getQueryParam('awal_periode') || $request->getQueryParam('nomor_surat_tugas') || $request->getQueryParam('akhir_periode')) && !count($list_surat_tugas)) return [];

        // Filter UserAccount
        $select = [
            'ID' => 'id_user_account',
            'NamaLengkap' => 'nama_lengkap',
        ];

        $list_user_account = $this->sp_client
            ->query('UserAccount')
            ->fields(array_keys($select));

        if ($request->getQueryParam('pic')) 
            $list_user_account = $list_user_account->where('NamaLengkap','contains', $request->getQueryParam('pic'));

        $list_user_account = Helpers::createLOV($list_user_account->get(), $select);
        
        if ($request->getQueryParam('pic') && !count($list_user_account)) return [];
        
        // LOV jenis pemeriksaan
        $select = [
            'ID' => 'id_jenis_pemeriksaan',
            'JenisPemeriksaan' => 'jenis_pemeriksaan'
        ];

        $list_jenis_pemeriksaan = $this->sp_client
            ->query('MasterJenisPemeriksaan')
            ->fields(array_keys($select));
        
        if ($request->getQueryParam('jenis_pemeriksaan')){
            $list_jenis_pemeriksaan = $list_jenis_pemeriksaan->where('JenisPemeriksaan', 'contains', $request->getQueryParam('jenis_pemeriksaan'));
        }

        $list_jenis_pemeriksaan = Helpers::createLOV($list_jenis_pemeriksaan->get(), $select);

        if ($request->getQueryParam('jenis_pemeriksaan') && !count($list_jenis_pemeriksaan)) return [];

        // LOV tema pengawasan
        $select = [
            'ID' => 'id_tema_pengawasan',
            'TemaPengawasan' => 'tema_pengawasan'
        ];

        $list_tema_pengawasan = $this->sp_client
                ->query('MasterTemaPengawasan')
                ->fields(array_keys($select));
        
        if ($request->getQueryParam('tema_pengawasan')){
            $list_tema_pengawasan = $list_tema_pengawasan->where('TemaPengawasan', 'contains', $request->getQueryParam('tema_pengawasan'));
        }

        $list_tema_pengawasan = Helpers::createLOV($list_tema_pengawasan->get(), $select);

        if ($request->getQueryParam('tema_pengawasan') && !count($list_tema_pengawasan)) return [];

        // Filter SHP Pihak
        $select = [
            'ID' => 'id_shp_pihak',
            'MasterProfil' => 'id_profil',
            'DPLEKesimpulanPihak' => 'id_kesimpulan_pihak',
        ];

        $list_shp_pihak = $this->sp_client
            ->query('DPLESHPPihak')
            ->fields(array_keys($select));
        $list_shp_pihak = $list_shp_pihak->where('DPLEKesimpulanPihak','not_null', '');

        $list_shp_pihak = Helpers::createLOV($list_shp_pihak->get(), $select);

        if ($request->getQueryParam('nomor_surat_ojk') && !count($list_shp_pihak)) return [];

        // Filter SHP
        $select = [
            'ID' => 'id_shp',
            'MasterSuratTugas' => 'id_surat_tugas',
            'NomorSurat' => 'nomor_surat_ojk',
        ];

        $list_shp = $this->sp_client
            ->query('DPLEshp')
            ->fields(array_keys($select));
        $list_shp = $list_shp->where('MasterSuratTugas','not_null', '');

        if ($request->getQueryParam('nomor_surat_ojk')) {
            $list_shp = $list_shp->and_where('NomorSurat','contains',$request->getQueryParam('nomor_surat_ojk'));
        }

        $list_shp = Helpers::createLOV($list_shp->get(), $select);

        if ($request->getQueryParam('nomor_surat_ojk') && !count($list_shp)) return [];

        // Filter PIC
        $select = [
            'ID' => 'id_pic',
            'SuratTugas' => 'id_surat_tugas',
            'UserAccount' => 'id_user_account',
        ];

        $list_tim_surat = $this->sp_client
            ->query('TimSuratTugas')
            ->fields(array_keys($select));
        $list_tim_surat = $list_tim_surat->where('PIC','=', 1)
            ->and_where('UserAccount', 'not_null', '')
            ->and_where('SuratTugas', 'not_null', '');

        $list_tim_surat = Helpers::createLOV($list_tim_surat->get(), $select, "SuratTugas");

        // Filter Temuan
        $select = [
            'ID' => 'id_kesimpulan_pihak',
            'DPLEshp' => 'id_shp',
            'Temuan' => 'temuan'
        ];

        $list_kesimpulan_pihak = $this->sp_client
            ->query('DPLEKesimpulanPihak')
            ->fields(array_keys($select));

        $list_kesimpulan_pihak = $list_kesimpulan_pihak
            ->where('DPLEshp','not_null', '');

        if ($request->getQueryParam('temuan')) 
            $list_kesimpulan_pihak = $list_kesimpulan_pihak->and_where('Temuan','contains', $request->getQueryParam('temuan'));

        $list_kesimpulan_pihak = Helpers::createLOV($list_kesimpulan_pihak->get(), $select);

        if ($request->getQueryParam('temuan') && count($list_kesimpulan_pihak)) return [];

        // Create structure data
        $data = [];
        foreach ($list_shp_pihak as $id_shp_pihak => $shp_pihak) {
            $perusahaan = isset($list_perusahaan[$shp_pihak['id_profil']])? $list_perusahaan[$shp_pihak['id_profil']]['perusahaan']: "";
            
            if (!$perusahaan) continue;

            $lookup_kesimpulan_pihak = isset($list_kesimpulan_pihak[$shp_pihak['id_kesimpulan_pihak']])? $list_kesimpulan_pihak[$shp_pihak['id_kesimpulan_pihak']]: [];

            $lookup_shp = isset($list_shp[$lookup_kesimpulan_pihak['id_shp']])? $list_shp[$lookup_kesimpulan_pihak['id_shp']]: [];

            $lookup_surat_tugas = isset($list_surat_tugas[$lookup_shp['id_surat_tugas']])? $list_surat_tugas[$lookup_shp['id_surat_tugas']] :[];

            if ( !$lookup_surat_tugas && ($request->getQueryParam('awal_periode') 
                || $request->getQueryParam('akhir_periode') 
                || $request->getQueryParam('nomor_surat_tugas')))
                continue;

            $lookup_tim_surat = isset($list_tim_surat[$lookup_surat_tugas['id_surat_tugas']])? $list_tim_surat[$lookup_surat_tugas['id_surat_tugas']]: false;

            $pic = "";
            if ($lookup_tim_surat) {
                $pic = isset($list_user_account[$lookup_tim_surat['id_user_account']])? $list_user_account[$lookup_tim_surat['id_user_account']]['nama_lengkap']: '';
            }

            if (!$pic && $request->getQueryParam('pic')) continue;

            $jenis_pemeriksaan = isset($list_jenis_pemeriksaan[$lookup_surat_tugas['id_jenis_pemeriksaan']])? $list_jenis_pemeriksaan[$lookup_surat_tugas['id_jenis_pemeriksaan']]['jenis_pemeriksaan']: "";
            $tema_pengawasan = isset($list_tema_pengawasan[$lookup_surat_tugas['id_tema_pengawasan']])? $list_tema_pengawasan[$lookup_surat_tugas['id_tema_pengawasan']]['tema_pengawasan']: "";

            $data[] = [
                'id_shp_pihak' => $shp_pihak['id_shp_pihak'],
                'id_surat_tugas' => $lookup_surat_tugas['id_surat_tugas'],
                'perusahaan' => $perusahaan,
                'nomor_surat_ojk' => $lookup_shp['nomor_surat_ojk'],
                'id_surat_tugas' => $lookup_surat_tugas['id_surat_tugas'],
                'nomor_surat_tugas' => $lookup_surat_tugas['nomor_surat_tugas'],
                'temuan' => $lookup_kesimpulan_pihak['temuan'],
                'awal_periode' => $lookup_surat_tugas['awal_periode'],
                'akhir_periode' => $lookup_surat_tugas['akhir_periode'],
                'pic' => $pic,
                'jenis_pemeriksaan'=> $jenis_pemeriksaan,
                'tema_pengawasan' => $tema_pengawasan,
                'lokasi' => $lookup_surat_tugas['lokasi'],
                'sisa_waktu' => 10
            ];
        }
        
        return $data;
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
