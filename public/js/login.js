/**
 * AngularJS Tutorial 1
 * @author Nick Kaye <nick.c.kaye@gmail.com>
 */

/**
 * Main AngularJS Web Application
 */

/**
 * Configure the Routes
 */
var app = angular.module('myAppClient', [
  'LocalStorageModule',
  'app.constants'
  ])

app.controller('LoginController', function ($scope, $window, $http, APP, localStorageService) {

    $scope.user = {};
    $scope.tag = 1;

    if (localStorageService.get('cliente')) {
        $window.location.href = 'index.html';
    }
    localStorageService.cookie.clearAll();

    $scope.login = function()
    {
      $http({
        method: 'POST',
        url: APP.api + 'login_cliente',
        data: $scope.user
      })
      .then(function(dataResponse){
        if(dataResponse.data.result)
        {
          if (localStorageService.cookie.isSupported) {
              localStorageService.cookie.set('cliente', dataResponse.data.records, 10);
              $window.location.href = 'index.html';
          }
        }
        else
        {
          alert(dataResponse.data.message);
        }
      });
    }

    $scope.enviar = function()
    {
      $http({
        method: 'POST',
        url: APP.api + 'empleados',
        params: $scope.sign
      })
      .then(function(dataResponse){
        if(dataResponse.data.result)
        {
          var empleado = dataResponse.data.records;
          alert("Registro creado, comunicate con Recursos Humanos para habilitar tu usuario.");
          $window.open(APP.api + "carta?nombre="+empleado.primer_nombre+" "+empleado.segundo_nombre+" "+empleado.primer_apellido+" "+empleado.segundo_apellido+"&codigo="+empleado.codigo_empleado, "_blank");
          $scope.sign = {};
          $scope.tag = 1;
        }
        else
        {
          alert(dataResponse.data.message);
        }
      });
    }

    $scope.signup = function()
    {
      $scope.tag = 2;
    }

    $scope.cancelar = function()
    {
      $scope.tag = 1;
    }

});

  