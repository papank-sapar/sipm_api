'use strict';

angular.module('astra.Login')

    .controller('LoginCtrl', ['$scope', '$rootScope', '$location', '$cookies', 'CONSTANT',
        function($scope, $rootScope, $location, $cookies, CONSTANT) {
            $scope.login = function(user) {
                if (user.username === 'admin') {
                    console.log('Admin');
                    $cookies.put(CONSTANT.COOKIES.CURRENT_USER, CONSTANT.ROLE.ADMIN);
                } else {
                    console.log('User');
                    $cookies.put(CONSTANT.COOKIES.CURRENT_USER, CONSTANT.ROLE.USER);
                }

                $location.path("/dashboard");
            };

            $scope.logout = function() {
                $cookies.remove(CONSTANT.COOKIES.CURRENT_USER);
                $location.path("/");
            };

            $scope.ROLE = $cookies.get(CONSTANT.COOKIES.CURRENT_USER);
        }
    ])

    .controller('DashboardCtrl', ['$templateCache', '$http', '$scope', '$rootScope', '$location', '$cookies', 'CONSTANT',
        function($templateCache, $http, $scope, $rootScope, $location, $cookies, CONSTANT) {
            var baseURL = 'http://localhost:81/';

            // Grup Surat Tugas
            var userList, timSuratTugasList, suratTugasList, shpList;

            // Grup Peraturan
            var peraturanList, shpPeraturanList;

            var shpKesimpulanPihakList, suratTugasJoinKesimpulan;
            // Grup Pihak
            var shpPihakList, pihakList, pihakInstitusiList;

            var grupPihak, grupPeraturan, grupSuratTugas;


            $scope.fetch = function() {
                $http({ url: baseURL + "peraturan" })
                    .then(function(response) {
                        peraturanList = TAFFY(response.data.data);

                        return $http({ url: baseURL + "shp/peraturan" })
                    })
                    .then(function(response) {
                        shpPeraturanList = TAFFY(response.data.data);

                        return $http({ url: baseURL + "shp/kesimpulan-pihak" })
                    })
                    .then(function(response) {
                        shpKesimpulanPihakList = TAFFY(response.data.data);

                        grupPeraturan = TAFFY(shpKesimpulanPihakList()
                            .join(shpPeraturanList, ['id_shp_kesimpulan_pihak', 'id_shp_kesimpulan_pihak'])
                            .join(peraturanList, ['id_peraturan', 'id_peraturan'])
                            .get())

                        return $http({ url: baseURL + "pihak" })
                    })
                    .then(function(response) {
                        pihakList = TAFFY(response.data.data);

                        return $http({ url: baseURL + "pihak/institusi" })
                    })
                    .then(function(response) {
                        pihakInstitusiList = TAFFY(response.data.data);

                        return $http({ url: baseURL + "shp/pihak" })
                    })
                    .then(function(response) {
                        shpPihakList = TAFFY(response.data.data);

                        grupPihak = TAFFY(pihakList()
                            .join(pihakInstitusiList, ['id_pihak', 'id_pihak'])
                            .join(shpPihakList, ['id_pihak', 'id_pihak'])
                            .get())

                        return $http({ url: baseURL + "user" })
                    })
                    .then(function(response) {
                        userList = TAFFY(response.data.data);

                        return $http({ url: baseURL + "surat-tugas/tim" })
                    })
                    .then(function(response) {
                        timSuratTugasList = TAFFY(response.data.data);

                        return $http({ url: baseURL + "shp" })
                    })
                    .then(function(response) {
                        shpList = TAFFY(response.data.data);

                        return $http({ url: baseURL + "surat-tugas" })
                    })
                    .then(function(response) {
                        suratTugasList = TAFFY(response.data.data);

                        grupSuratTugas = TAFFY(suratTugasList()
                            .join(timSuratTugasList, ['id_surat_tugas', 'id_surat_tugas'])
                            .join(userList, ['id_user', 'id_user'])
                            .join(shpList, ['id_surat_tugas', 'id_surat_tugas'])
                            .get())

                        suratTugasJoinKesimpulan = TAFFY(grupSuratTugas()
                            .join(shpKesimpulanPihakList, ['id_shp', 'id_shp'])
                            .get())

                        let data = grupPeraturan().get()
                        for (let i in data) {
                            console.log(data[i].id_shp_peraturan + ": " + grupPeraturan({id_shp_peraturan: data[i].id_shp_peraturan}).count())
                        }

                        // console.log(grupPeraturan().get())

                    })
            };
        }
    ])