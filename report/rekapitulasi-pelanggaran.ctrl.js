'use strict';

angular.module('astra.Login')
    .controller('RekapitulasiPelanggaranCtrl', ['$http', '$scope',
        function($http, $scope) {
            var baseURL = 'http://112.78.191.125:8080/';
            $scope.filterData = {}
            // Grup Surat Tugas
            var suratTugasTable, shpTable;

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
                            .get());


                        var pelanggaranTable = TAFFY(peraturanTable()
                            .join(shpPeraturanTable, ['id_peraturan', 'id_peraturan'])
                            .get());

                        for (let i in dataTable().get()) {
                            dataTable().get()[i].pelanggaran = pelanggaranTable({ id_shp_kesimpulan_pihak: dataTable().get()[i].id_shp_kesimpulan_pihak }).get()
                        }

                        console.log("dataTable().get()")
                        console.log(dataTable().get())

                        // Daftar perusahaan yg memiliki pelanggaran
                        var perusahaanList = dataTable().distinct('pihak');
                        $scope.perusahaanList = perusahaanList;

                        // Mengelompokan temuan berdasarkan perusahaan
                        var temuanByPerusahaan = [];

                        for (let i in perusahaanList) {
                            let temuan = dataTable({ pihak: perusahaanList[i] }).get();
                            let temuanTable = TAFFY(temuan)

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

                            let totalPelanggaran = 0
                            for (let j in distinctPelanggaran) {
                                let jumlah = pelanggaranPerPerusahaanTable({id_peraturan: distinctPelanggaran[j]}).count();
                                let pelanggaran = peraturanTable({ id_peraturan: distinctPelanggaran[j] }).get()[0];
                                pelanggaranPerPerusahaanList.push({
                                    grup_peraturan: pelanggaran.grup_peraturan,
                                    id_parent: pelanggaran.id_parent,
                                    id_peraturan: pelanggaran.id_peraturan,
                                    jumlah: jumlah,
                                    keterangan: pelanggaran.keterangan,
                                    level: pelanggaran.level,
                                    level1: pelanggaran.level1,
                                    level2: pelanggaran.level2,
                                    level3: pelanggaran.level3,
                                    peraturan: pelanggaran.peraturan,
                                    tracking_peraturan: pelanggaran.tracking_peraturan,
                                })

                                totalPelanggaran += jumlah
                            }

                            let pelanggaranPerPerusahaanTb = TAFFY(pelanggaranPerPerusahaanList)
                            // console.log(pelanggaranPerPerusahaanTb().sum('jumlah'))
                            let pemeriksaaan = temuanTable().distinct('id_surat_tugas');

                            temuanByPerusahaan.push({ 
                                perusahaan: perusahaanList[i], 
                                pelanggaran: pelanggaranPerPerusahaanTb().get(),
                                pemeriksaaan: pemeriksaaan,
                                jumlah_pemeriksaan: pemeriksaaan.length,
                                jumlah_pelanggaran: totalPelanggaran
                            })
                        }

                        console.log("temuanByPerusahaan")
                        console.log(temuanByPerusahaan)
                        $scope.temuanByPerusahaanList = temuanByPerusahaan
                    })
            };
        }
    ])