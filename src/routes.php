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
$app->get('/peraturan', 'ReportCtrl:peraturan');
$app->get('/jenis-pemeriksaan', 'MasterDataCtrl:getJenisPemeriksaan');
$app->get('/tema-pengawasan', 'MasterDataCtrl:getTemaPengawasan');
