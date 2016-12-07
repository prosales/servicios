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
var app = angular.module('myApp', [
  'ngRoute',
  'LocalStorageModule',
  'ngTable',
  'app.constants'
  ]).
  config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .when("/", {templateUrl: "views/dashboard.html", controller: "DashboardController"})
    .when("/usuarios", {templateUrl: "views/usuarios.html", controller: "UsuariosController"})
    .when("/empleados", {templateUrl: "views/empleados.html", controller: "EmpleadosController"})
    .when("/tipos_vehiculos", {templateUrl: "views/tipos_vehiculos.html", controller: "TiposVehiculosController"})
    .when("/servicios", {templateUrl: "views/servicios.html", controller: "ServiciosController"})
    .when("/404", {templateUrl: "views/404.html"})

    // else 404
    .otherwise({
        redirectTo: '/404'
      })

  }]);

function iniciar()
{
  $("#progressBar").css({
      "opacity":1,
      "width":"10%"
  });
}

function loader()
{
  $(document).scrollTop(0);

    var loaded = 0;
    var imgCounter = $(".main-content img").length;
    if(imgCounter > 0){
      function doProgress() {
        $(".main-content img").load(function() {
          loaded++;
          var newWidthPercentage = (loaded / imgCounter) * 100;
          animateLoader(newWidthPercentage + '%');      
        })
      } 
      function animateLoader(newWidth) {
        $("#progressBar").width(newWidth);
        if(imgCounter === loaded){
          setTimeout(function(){
                    $("#progressBar").animate({opacity:0});
                },500);
        }
      }
      doProgress();
    }else{
      setTimeout(function(){
          $("#progressBar").css({
            "opacity":0,
            "width":"100%"
          });
      },500);
    }

    // Activates Tooltips for Social Links
    $('[data-toggle="tooltip"]').tooltip(); 

    // Activates Popovers for Social Links 
    $('[data-toggle="popover"]').popover();  

    //*** Refresh Content ***//
      $('.refresh-content').on("click", function(){
        $(this).parent().parent().addClass("loading-wait").delay(3000).queue(function(next){
          $(this).removeClass("loading-wait");
          next();
      });
      $(this).addClass("fa-spin").delay(3000).queue(function(next){
          $(this).removeClass("fa-spin");
          next();
      });
      });

      //*** Expand Content ***//
      $('.expand-content').on("click", function(){
        $(this).parent().parent().toggleClass("expand-this");
      });

      //*** Delete Content ***//
      $('.close-content').on("click", function(){
        $(this).parent().parent().slideUp();
      });

      // Activates Tooltips for Social Links
      $('.tooltip-social').tooltip({
        selector: "a[data-toggle=tooltip]"
      });
}

app.controller('MainController', function($scope, $window, localStorageService) {

  if (!localStorageService.cookie.get('login')) {
      $window.location.href = 'login.html';
  }

  $scope.username = localStorageService.cookie.get('login').nombre;
  $scope.user = localStorageService.cookie.get('login').usuario;

});

app.controller('DashboardController', function ($scope, $window, $http) {

  loader();

});

app.controller('UsuariosController', function ($scope, $window, usuariosService) {

  $scope.data = [];
  $scope.settings = {
    singular: 'Usuario',
    plural: 'Usuarios',
    accion: 'Crear'
  }
  $scope.msg = {
      mostrar: 0,
      title: "",
      message: "",
      color: ""
  }
  $scope.mostrar = 0;

  $scope.cargar_datos = function()
  {
    $scope.mostrar = 0;
    $scope.msg = {
        mostrar: 0,
        title: "",
        message: "",
        color: ""
    }
    usuariosService.getData("GET", {}).then(function(dataResponse)
    {
      $scope.data = dataResponse.data.records;
    });
  }

  $scope.cargar_datos();

  usuariosService.getTipos().then(function(dataResponse)
  {
    $scope.tipos_usuarios = dataResponse.data.records;
  });

  $scope.crear = function()
  {
    $scope.settings.accion = 'Crear';
    $scope.mostrar = 1;
    $scope.item = {};
  }

  $scope.editar = function(item)
  {
    $scope.settings.accion = 'Editar';
    $scope.mostrar = 1;
    $scope.item = item;
  }

  $scope.eliminar = function(item)
  {
    $scope.settings.accion = 'Eliminar';
    $scope.mostrar = 1;
    $scope.item = item;
  }

  $scope.cancelar = function()
  {
    $scope.mostrar = 0;
  }

  $scope.guardar = function(item)
  {
    if($scope.settings.accion == "Crear")
    {
      usuariosService.create(item).then(function(dataResponse)
      {
        if(dataResponse.data.result)
        {
          showAlert("green", "Exito!", dataResponse.data.message);
          setTimeout(function(){ $scope.cargar_datos(); }, 3000);
        }
        else
        {
          showAlert("red", "Espera!", dataResponse.data.message);
        }
      });
    }
    else if($scope.settings.accion == "Editar")
    {
      usuariosService.update(item).then(function(dataResponse)
      {
        if(dataResponse.data.result)
        {
          showAlert("green", "Exito!", dataResponse.data.message);
          setTimeout(function(){ $scope.cargar_datos(); }, 3000);
        }
        else
        {
          showAlert("red", "Espera!", dataResponse.data.message);
        }
      });
    }
    else
    {
      usuariosService.delete(item.id).then(function(dataResponse)
      {
        if(dataResponse.data.result)
        {
          showAlert("green", "Exito!", dataResponse.data.message);
          setTimeout(function(){ $scope.cargar_datos(); }, 3000);
        }
        else
        {
          showAlert("red", "Espera!", dataResponse.data.message);
        }
      });
    }
  }

  function showAlert(color, title, message)
  {
    $scope.msg = {
      mostrar: 1,
      title: title,
      message: message,
      color: color
    }
  }
  

  loader();

});

app.controller('EmpleadosController', function ($scope, $window, empleadosService) {

  $scope.data = [];
  $scope.settings = {
    singular: 'Empleado',
    plural: 'Empleados',
    accion: 'Crear'
  }
  $scope.mostrar = 0;

  $scope.cargar_datos = function()
  {
    $scope.mostrar = 0;
    empleadosService.getData("GET", {}).then(function(dataResponse)
    {
      $scope.data = dataResponse.data.records;
    });
  }

  $scope.cargar_datos();

  loader();

});

app.controller('TiposVehiculosController', function ($scope, $window, vehiculosService) {

  $scope.data = [];
  $scope.settings = {
    singular: 'Tipo Vehiculo',
    plural: 'Tipos Vehiculos',
    accion: 'Crear'
  }
  $scope.msg = {
      mostrar: 0,
      title: "",
      message: "",
      color: ""
  }
  $scope.mostrar = 0;

  $scope.cargar_datos = function()
  {
    $scope.mostrar = 0;
    $scope.msg = {
        mostrar: 0,
        title: "",
        message: "",
        color: ""
    }
    vehiculosService.getData("GET", {}).then(function(dataResponse)
    {
      $scope.data = dataResponse.data.records;
    });
  }

  $scope.cargar_datos();

  $scope.crear = function()
  {
    $scope.settings.accion = 'Crear';
    $scope.mostrar = 1;
    $scope.item = {};
  }

  $scope.editar = function(item)
  {
    $scope.settings.accion = 'Editar';
    $scope.mostrar = 1;
    $scope.item = item;
  }

  $scope.eliminar = function(item)
  {
    $scope.settings.accion = 'Eliminar';
    $scope.mostrar = 1;
    $scope.item = item;
  }

  $scope.cancelar = function()
  {
    $scope.mostrar = 0;
  }

  $scope.guardar = function(item)
  {
    if($scope.settings.accion == "Crear")
    {
      vehiculosService.create(item).then(function(dataResponse)
      {
        if(dataResponse.data.result)
        {
          showAlert("green", "Exito!", dataResponse.data.message);
          setTimeout(function(){ $scope.cargar_datos(); }, 3000);
        }
        else
        {
          showAlert("red", "Espera!", dataResponse.data.message);
        }
      });
    }
    else if($scope.settings.accion == "Editar")
    {
      vehiculosService.update(item).then(function(dataResponse)
      {
        if(dataResponse.data.result)
        {
          showAlert("green", "Exito!", dataResponse.data.message);
          setTimeout(function(){ $scope.cargar_datos(); }, 3000);
        }
        else
        {
          showAlert("red", "Espera!", dataResponse.data.message);
        }
      });
    }
    else
    {
      vehiculosService.delete(item.id).then(function(dataResponse)
      {
        if(dataResponse.data.result)
        {
          showAlert("green", "Exito!", dataResponse.data.message);
          setTimeout(function(){ $scope.cargar_datos(); }, 3000);
        }
        else
        {
          showAlert("red", "Espera!", dataResponse.data.message);
        }
      });
    }
  }

  function showAlert(color, title, message)
  {
    $scope.msg = {
      mostrar: 1,
      title: title,
      message: message,
      color: color
    }
  }
  

  loader();

});

app.controller('ServiciosController', function ($scope, $window, serviciosService, vehiculosService) {

  $scope.data = [];
  $scope.combos = [{id: 0, descripcion: "No"},{id: 1, descripcion: "Si"}];
  $scope.precios = [];
  $scope.settings = {
    singular: 'Servicio',
    plural: 'Servicios',
    accion: 'Crear'
  }
  $scope.msg = {
      mostrar: 0,
      title: "",
      message: "",
      color: ""
  }
  $scope.mostrar = 0;
  var dataPrecios = [];

  $scope.cargar_datos = function()
  {
    $scope.mostrar = 0;
    $scope.msg = {
        mostrar: 0,
        title: "",
        message: "",
        color: ""
    }
    serviciosService.getData("GET", {}).then(function(dataResponse)
    {
      $scope.data = dataResponse.data.records;
    });

    vehiculosService.getData("GET", {}).then(function(dataResponse)
    {
      dataPrecios = dataResponse.data.records;
    });
  }

  $scope.cargar_datos();

  $scope.crear = function()
  {
    $scope.settings.accion = 'Crear';
    $scope.mostrar = 1;
    $scope.item = { cantidad: 1 };
    $scope.precios = dataPrecios;
  }

  $scope.editar = function(item)
  {
    $scope.settings.accion = 'Editar';
    $scope.mostrar = 1;
    $scope.item = item;
    $scope.precios = angular.fromJson(item.precios);
  }

  $scope.eliminar = function(item)
  {
    $scope.settings.accion = 'Eliminar';
    $scope.mostrar = 1;
    $scope.item = item;
    $scope.precios = angular.fromJson(item.precios);
  }

  $scope.cancelar = function()
  {
    $scope.mostrar = 0;
  }

  $scope.guardar = function(item)
  {
    if($scope.settings.accion == "Crear")
    {
      item.precios = angular.toJson($scope.precios);
      
      serviciosService.create(item).then(function(dataResponse)
      {
        if(dataResponse.data.result)
        {
          showAlert("green", "Exito!", dataResponse.data.message);
          setTimeout(function(){ $scope.cargar_datos(); }, 3000);
        }
        else
        {
          showAlert("red", "Espera!", dataResponse.data.message);
        }
      });
    }
    else if($scope.settings.accion == "Editar")
    {
      var datos = {
        id: item.id,
        nombre: item.nombre,
        precios: angular.toJson($scope.precios),
        cantidad: item.cantidad,
        es_combo: item.es_combo
      }

      serviciosService.update(datos).then(function(dataResponse)
      {
        if(dataResponse.data.result)
        {
          showAlert("green", "Exito!", dataResponse.data.message);
          setTimeout(function(){ $scope.cargar_datos(); }, 3000);
        }
        else
        {
          showAlert("red", "Espera!", dataResponse.data.message);
        }
      });
    }
    else
    {
      serviciosService.delete(item.id).then(function(dataResponse)
      {
        if(dataResponse.data.result)
        {
          showAlert("green", "Exito!", dataResponse.data.message);
          setTimeout(function(){ $scope.cargar_datos(); }, 3000);
        }
        else
        {
          showAlert("red", "Espera!", dataResponse.data.message);
        }
      });
    }
  }

  function showAlert(color, title, message)
  {
    $scope.msg = {
      mostrar: 1,
      title: title,
      message: message,
      color: color
    }
  }
  

  loader();

});
  