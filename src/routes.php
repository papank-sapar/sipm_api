<?php
// Routes
use Thybag\SharePointAPI;

$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");
    $sp = new SharePointAPI('spadmin', 'Admin123', __DIR__  . '\Lists.xml', 'NTLM');
    $stat = [
	    "ProfilPihakInstitusi" => $sp->read('ProfilPihakInstitusi', NULL, NULL, ['ID', 'Nama']),
	    "MasterSuratTugas" => $sp->read('MasterSuratTugas', NULL, NULL, ['*']),
	    "DPLEShp" => $sp->read('DPLEShp', NULL, NULL, ['*']),
	    "UserAccount" => $sp->read('UserAccount', NULL, NULL, ['*']),
	    "MasterJenisPemeriksaan" => $sp->read('MasterJenisPemeriksaan', NULL, NULL, ['*']),
	    "MasterTemaPengawasan" => $sp->read('MasterTemaPengawasan', NULL, NULL, ['*']),
	    "DPLEKesimpulanPihak" => $sp->read('DPLEKesimpulanPihak', NULL, NULL, ['*']),
	    "TimSuratTugas" => $sp->read('TimSuratTugas', NULL, NULL, ['ID', 'UserAccount']),
    ];


    // print_r($stat);

    return $response->withJson($stat);
    // return $response->withJson(count($sp->read('MasterSuratTugas', NULL, NULL, ['*'])));
    // return $response->withJson($sp->readListMeta('MasterSuratTugas', false));
    // return $response->withJson($sp->query('GroupAccess')
    // 	->raw_where('<In><FieldRef Name="GroupAccess" /><Values><Value Type="Lookup">14</Value></Values></In>')
    // 	->get());
});

$app->get('/monitoring-tanggapan', 'ReportCtrl:monitoringTanggapan');
$app->get('/perusahaan', 'ReportCtrl:monitoring');
$app->get('/test', 'ReportCtrl:coba');
$app->get('/check', function ($request, $response, $args) {
    return 'Bisa';
});

$app->group('/master-data', function () {
	$this->post('/register', 'UserController:register');
	$this->post('/login', 'AuthController:login');
	$this->get('/logout', 'UserController:logout');
});