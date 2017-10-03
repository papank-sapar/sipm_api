'use strict';

angular.module('astra.Login')
    .controller('PelanggaranPerPerusahaanCtrl', ['$templateCache', '$http', '$scope', '$rootScope', '$location', '$cookies', 'CONSTANT',
        function($templateCache, $http, $scope, $rootScope, $location, $cookies, CONSTANT) {
            // var baseURL = 'http://112.78.191.125:8080/';
            var baseURL = 'http://localhost:81/';
            $scope.filterData = {}
            // Grup Surat Tugas
            var userTable, timSuratTugasTable, suratTugasTable, shpTable;

            // Grup Peraturan
            var peraturanTable, shpPeraturanTable;

            // Grup Pihak
            var shpPihakTable, pihakTable, pihakInstitusiTable;

            var shpKesimpulanPihakTable, suratTugasJoinKesimpulan;

            var filter = '';
            $scope.fetch = function() {
                console.log("filter");
                console.log($scope.filterData);
                $scope.perusahaanList = []
                $scope.temuanByPerusahaanList = []

                $http({ url: baseURL + "peraturan" })
                    .then(function(response) {
                        peraturanTable = TAFFY(response.data.data);

                        return $http({ url: baseURL + "shp/peraturan" })
                    })
                    .then(function(response) {
                        shpPeraturanTable = TAFFY(response.data.data);

                        return $http({ url: baseURL + "shp/kesimpulan-pihak" })
                    })
                    .then(function(response) {
                        shpKesimpulanPihakTable = TAFFY(response.data.data);

                        filter = '';
                        if ($scope.filterData.perusahaan) {
                            filter = "?jenis_pihak=perusahaan&pihak=" + $scope.filterData.perusahaan
                        }

                        return $http({ url: baseURL + "pihak" + filter })
                    })
                    .then(function(response) {
                        pihakTable = TAFFY(response.data.data);

                        filter = '';
                        if ($scope.filterData.kodePerusahaan) {
                            filter = "?kode_perusahaan=" + $scope.filterData.kodePerusahaan
                        }

                        return $http({ url: baseURL + "pihak/institusi" + filter })
                    })
                    .then(function(response) {
                        pihakInstitusiTable = TAFFY(response.data.data);

                        return $http({ url: baseURL + "shp/pihak" })
                    })
                    .then(function(response) {
                        shpPihakTable = TAFFY(response.data.data);

                        filter = '';
                        if ($scope.filterData.PIC) {
                            filter = "?nama_lengkap=" + $scope.filterData.PIC
                        }

                        return $http({ url: baseURL + "user" + filter })
                    })
                    .then(function(response) {
                        userTable = TAFFY(response.data.data);

                        return $http({ url: baseURL + "surat-tugas/tim" })
                    })
                    .then(function(response) {
                        timSuratTugasTable = TAFFY(response.data.data);

                        return $http({ url: baseURL + "shp" })
                    })
                    .then(function(response) {
                        shpTable = TAFFY(response.data.data);

                        filter = '';
                        if ($scope.filterData.awalPeriode) {
                            filter = "?awal_periode=" + $scope.filterData.awalPeriode + "&akhir_periode=" + $scope.filterData.akhirPeriode
                        }

                        return $http({ url: baseURL + "surat-tugas" + filter })
                    })
                    .then(function(response) {
                        suratTugasTable = TAFFY(response.data.data);

                        // Join tables
                        var dataTable = TAFFY(pihakInstitusiTable()
                            .join(pihakTable, ['id_pihak', 'id_pihak'])
                            .join(shpPihakTable, ['id_pihak', 'id_pihak'])
                            .join(shpKesimpulanPihakTable, ['id_shp_kesimpulan_pihak', 'id_shp_kesimpulan_pihak'])
                            .join(shpTable, ['id_shp', 'id_shp'])
                            .join(suratTugasTable, ['id_surat_tugas', 'id_surat_tugas'])
                            .join(timSuratTugasTable, ['id_surat_tugas', 'id_surat_tugas'])
                            .join(userTable, ['id_user', 'id_user'])
                            .get());

                        var pelanggaranTable = TAFFY(peraturanTable()
                            .join(shpPeraturanTable, ['id_peraturan', 'id_peraturan'])
                            .get());

                        for (let i in dataTable().get()) {
                            dataTable().get()[i].pelanggaran = pelanggaranTable({ id_shp_kesimpulan_pihak: dataTable().get()[i].id_shp_kesimpulan_pihak }).get()
                        }

                        // Daftar perusahaan yg memiliki pelanggaran
                        var perusahaanList = dataTable().distinct('pihak');
                        $scope.perusahaanList = perusahaanList;

                        // Mengelompokan temuan berdasarkan perusahaan
                        var temuanByPerusahaan = [];

                        for (let i in perusahaanList) {
                            let temuan = dataTable({ pihak: perusahaanList[i] }).get();

                            // Menggabungkan pelanggaran berdasarkan peraturannya
                            let pelanggaranList = [];

                            for (let j in temuan) {
                                pelanggaranList = pelanggaranList.concat(temuan[j].pelanggaran)
                            }

                            let pelanggaranPerPerusahaanTable = TAFFY(pelanggaranList)

                            // Mengambil pelanggaran utk tiap perusahaan
                            let distinctPelanggaran = pelanggaranPerPerusahaanTable().distinct('id_peraturan')

                            // Menyusun pelanggaran utk tiap perusahaan dan menghitung jumlah pelanggaran
                            // utk tiap-tiap peraturan
                            let pelanggaranPerPerusahaanList = []

                            for (let j in distinctPelanggaran) {
                                pelanggaranPerPerusahaanList[j] = peraturanTable({ id_peraturan: distinctPelanggaran[j] }).get()[0];
                                pelanggaranPerPerusahaanList[j]['jumlah'] = pelanggaranPerPerusahaanTable().filter({ id_peraturan: distinctPelanggaran[j] }).count()
                            }

                            pelanggaranPerPerusahaanList = TAFFY(pelanggaranPerPerusahaanList)

                            temuanByPerusahaan.push({ perusahaan: perusahaanList[i], pelanggaran: pelanggaranPerPerusahaanList().order('level asec, peraturan asec').get() })
                        }

                        console.log("temuanByPerusahaan")
                        console.log(temuanByPerusahaan)
                        $scope.temuanByPerusahaanList = temuanByPerusahaan
                    })
            };
        }
    ])