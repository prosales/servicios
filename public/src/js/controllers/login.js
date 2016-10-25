'use strict';

/* Controllers */
  // signin controller
app.controller('LoginController', ['$scope', '$http', '$state', 'localStorageService', 'APP', '$window', function($scope, $http, $state, localStorageService, APP, $window) {

    localStorageService.cookie.clearAll();
    $scope.app = {
      name: "WORKFLOW",
      settings: {
          themeID: 1,
          navbarHeaderColor: 'bg-black',
          navbarCollapseColor: 'bg-white-only',
          asideColor: 'bg-black',
          headerFixed: true,
          asideFixed: false,
          asideFolded: false,
          asideDock: false,
          container: false
        }
    }
    $scope.user = {};
    $scope.authError = null;
    $scope.login = function() {
      $scope.authError = null;
      // Try to login
      $http.post(APP.api + 'login', {usuario: $scope.user.usuario, password: $scope.user.password})
      .then(function(response) {
        if ( !response.data.result ) {
          $scope.authError = response.data.message;
        }else{
          localStorageService.cookie.set('login', response.data.records, 10);
          $window.location.href = 'index.html';
        }
      }, function(x) {
        $scope.authError = 'Server Error';
      });
    };
  }])
;