<?php
// Routes
use Thybag\SharePointAPI;

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Home access.");
    return "Server is up!";
});

$app->get('/monitoring-tanggapan', 'ReportCtrl:monitoringTanggapan');
$app->get('/pelanggaran-per-perusahaan', 'ReportCtrl:pelanggaranPerPerusahaan');
$app->get('/jenis-pemeriksaan', 'MasterDataCtrl:getJenisPemeriksaan');
$app->get('/tema-pengawasan', 'MasterDataCtrl:getTemaPengawasan');

$app->get('/peraturan', 'ReportCtrl:getPeraturan');
$app->get('/pihak', 'ReportCtrl:getPihak');
$app->get('/pihak/individu', 'ReportCtrl:getPihakIndividu');
$app->get('/pihak/individu/alamat', 'ReportCtrl:getAlamatIndividu');
$app->get('/pihak/individu/identitas', 'ReportCtrl:getIdentitasIndividu');
$app->get('/pihak/institusi', 'ReportCtrl:getPihakInstitusi');
$app->get('/shp', 'ReportCtrl:getShp');
$app->get('/shp/kesimpulan-pihak', 'ReportCtrl:getShpKesimpulanPihak');
$app->get('/shp/peraturan', 'ReportCtrl:getShpPeraturan');
$app->get('/shp/pihak', 'ReportCtrl:getShpPihak');
$app->get('/surat-tugas', 'ReportCtrl:getSuratTugas');
$app->get('/surat-tugas/tim', 'ReportCtrl:getTimSuratTugas');
$app->get('/user', 'ReportCtrl:getUser');
