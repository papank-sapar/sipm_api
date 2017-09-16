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
        
        // LOV jenis pemeriksaan
        $select = [
            'ID' => 'id_jenis_pemeriksaan',
            'JenisPemeriksaan' => 'jenis_pemeriksaan'
        ];

        $jenis_pemeriksaan = $this->sp_client
            ->query('MasterJenisPemeriksaan')
            ->fields(array_keys($select));
            
        $jenis_pemeriksaan = Helpers::createLOV($jenis_pemeriksaan->get(), $select);

        // LOV tema pengawasan
        $select = [
            'ID' => 'id_tema_pengawasan',
            'TemaPengawasan' => 'tema_pengawasan'
        ];

        $tema_pengawasan = $this->sp_client
                ->query('MasterTemaPengawasan')
                ->fields(array_keys($select))
                ->get();
        $tema_pengawasan = Helpers::createLOV($tema_pengawasan, $select);

        // Filter surat tugas
        $select = [
            'ID' => 'id_surat_tugas',
            'NomorSuratTugas' => 'nomor_surat_tugas',
            'AwalPeriode' => 'awal_periode',
            'AkhirPeriode' => 'akhir_periode',
            'ProfilPihakInstitusi_Nama' => 'id_perusahaan',
            'TemaPengawasan' => 'id_tema_pengawasan',
            'JenisPemeriksaan' => 'id_jenis_pemeriksaan',
            'Lokasi' => 'lokasi',
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
        
        $surat_tugas = Helpers::createLOV($surat_tugas->get(), $select);
        
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
        $shp = Helpers::createLOV($shp->get(), $select);

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

        $pic = Helpers::createLOV($pic->get(), $select, "SuratTugas");

        // Filter UserAccount
        $select = [
            'ID' => 'id_user_account',
            'NamaLengkap' => 'nama_lengkap',
        ];

        $user_account = $this->sp_client
            ->query('UserAccount')
            ->fields(array_keys($select));

        if ($request->getQueryParam('pic')) 
            $user_account = $user_account->where('NamaLengkap','contains', $request->getQueryParam('pic'));

        $user_account = Helpers::createLOV($user_account->get(), $select);

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

        $temuan = Helpers::createLOV($temuan->get(), $select, "DPLEshp");

        // return $pic;
        // Create structure data
        $data = [];
        foreach ($shp as $id_shp => $item) {
            $lookup_surat_tugas = isset($surat_tugas[$item['id_surat_tugas']])? $surat_tugas[$item['id_surat_tugas']]: false;
            $lookup_temuan = isset($temuan[$id_shp])? $temuan[$id_shp]: false;

            $lookup_tim_surat_tugas = $lookup_surat_tugas? isset($pic[$lookup_surat_tugas["id_surat_tugas"]])? $pic[$lookup_surat_tugas["id_surat_tugas"]]: false: false;
            $pic = $lookup_tim_surat_tugas? $user_account[$lookup_tim_surat_tugas["id_user_account"]]["nama_lengkap"]: "";

            $data[] = [
                'id_shp' => $id_shp,
                'id_surat_tugas' => $item['id_surat_tugas'],
                'nomor_surat_ojk' => $item['nomor_surat_ojk'],
                'nomor_surat_tugas' => ($lookup_surat_tugas? $lookup_surat_tugas['nomor_surat_tugas']: ''),
                'temuan' => ($lookup_temuan? $lookup_temuan['temuan']: ''),
                'awal_periode' => ($lookup_surat_tugas? $lookup_surat_tugas['awal_periode']: ''),
                'akhir_periode' => ($lookup_surat_tugas? $lookup_surat_tugas['akhir_periode']: ''),
                'pic' => $pic,
                'jenis_pemeriksaan'=> $lookup_surat_tugas? $jenis_pemeriksaan[$lookup_surat_tugas['id_jenis_pemeriksaan']]['jenis_pemeriksaan']: '',
                'tema_pengawasan' => $lookup_surat_tugas? $tema_pengawasan[$lookup_surat_tugas['id_tema_pengawasan']]['tema_pengawasan']: '',
                'lokasi' => ($lookup_surat_tugas? $lookup_surat_tugas['lokasi']: '')
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
