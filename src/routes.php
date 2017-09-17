<?php
// Routes
use Thybag\SharePointAPI;

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Home access.");
    return "Server is up!";
});

$app->get('/monitoring-tanggapan', 'ReportCtrl:monitoringTanggapan');
$app->get('/pelanggaran-perusahaan', 'ReportCtrl:pelanggaranPerusahaan');
$app->get('/peraturan', 'ReportCtrl:peraturan');
