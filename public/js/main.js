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
  'ngRoute',
  'LocalStorageModule',
  'ngTable',
  'app.constants'
  ]).
  config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .when("/", {templateUrl: "views/dashboard.html", controller: "DashboardController"})
    .when("/comprarservicio", {templateUrl: "views/comprarservicio.html", controller: "ServiciosController"})
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

  if (!localStorageService.cookie.get('cliente')) {
      $window.location.href = 'login.html';
  }

  $scope.id_empleado = localStorageService.cookie.get('cliente').id;
  $scope.codigo_empleado = localStorageService.cookie.get('cliente').codigo_empleado;
  $scope.nombre = localStorageService.cookie.get('cliente').primer_nombre + ' ' + localStorageService.cookie.get('cliente').primer_apellido;
  $scope.nombre_completo = localStorageService.cookie.get('cliente').primer_nombre + ' ' + localStorageService.cookie.get('cliente').segundo_nombre + ' ' + localStorageService.cookie.get('cliente').primer_apellido+ ' ' + localStorageService.cookie.get('cliente').segundo_apellido;

  loader();

});

app.controller('DashboardController', function ($scope, $window, $http, APP, serviciosService) {

  $scope.data = [];
  $scope.msg = {
      mostrar: 0,
      title: "",
      message: "",
      color: ""
  }
  $scope.mostrar = 0;
  $scope.comprados = 0;
  $scope.usados = 0;
  $scope.sin_usar = 0;

  $scope.cargar_datos = function()
  {
    serviciosService.getDataCompras("GET", { id_empleado: $scope.id_empleado }).then(function(dataResponse){

      $scope.data = dataResponse.data.records;
      $scope.comprados = dataResponse.data.contadores.comprados;
      $scope.usados = dataResponse.data.contadores.usados;
      $scope.sin_usar = dataResponse.data.contadores.sin_usar;

    });
  }

  $scope.cargar_datos();

  $scope.cambiar_password = function()
  {
    $scope.mostrar = 1;
  }

  $scope.cancelar = function()
  {
    $scope.mostrar = 0;
  }

  $scope.guardar = function()
  {
    var data = {
      pass_actual: $scope.usuario.pass_actual,
      password: $scope.usuario.password,
      id: $scope.id_empleado
    };

    $http({
      method: 'POST',
      url: APP.api + 'empleados/cambiar_password',
      params: data
    })
    .then(function(dataResponse){
      if(dataResponse.data.result)
      {
        $scope.usuario = {};
        alert(dataResponse.data.message);
        $scope.mostrar = 0;
      }
      else
      {
        showAlert("red", "Espera!", dataResponse.data.message);
      }
    });
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

});

app.controller('ServiciosController', function ($scope, $window, $http, APP, serviciosService) {

  $scope.msg = {
      mostrar: 0,
      title: "",
      message: "",
      color: ""
  }
  $scope.tipos_vehiculos = [];
  $scope.servicios = [];
  $scope.extras = [];
  $scope.mostrar_servicio = 0;
  $scope.mostrar_extra = 0;
  $scope.total = 0;
  $scope.title = "Comprar";

  $scope.cargar_datos = function()
  {
    serviciosService.getServicios().then(function(dataResponse){

      if(dataResponse.data.result)
      {
        $scope.tipos_vehiculos = dataResponse.data.records;
      }

    });
  }

  $scope.cargar_datos();

  $scope.limpiar_datos = function()
  {
    $scope.tipos_vehiculos = [];
    $scope.servicios = [];
    $scope.extras = [];
    $scope.mostrar_servicio = 0;
    $scope.mostrar_extra = 0;
    $scope.total = 0;
    $scope.title = "Comprar";
    $scope.cargar_datos();
  }

  $scope.llenarServicios = function()
  {
    if($scope.item_vehiculo!=undefined)
    {
      $scope.servicios = $scope.item_vehiculo.servicios;
      $scope.mostrar_servicio = 1;
    }
  }

  $scope.llenarExtras = function()
  {
    if($scope.item_vehiculo!=undefined)
    {
      $scope.mostrar_extra = 1;
      $scope.extras = $scope.item_vehiculo.extras;
      $scope.calcular();
    }
  }

  $scope.calcular = function()
  {
    $scope.total = 0;
    $scope.total += $scope.item_servicio.precio;
    angular.forEach($scope.extras, function(extra)
    {
      if(!!extra.selected)
        $scope.total += extra.precio;
    });
  }

  $scope.comprar = function()
  {
    $scope.title = "Cargando...";
    var extras = [];

    angular.forEach($scope.extras, function(extra)
    {
      if(!!extra.selected)
        extras.push( extra );
    });

    data_servicio = {
      tipo_vehiculo: { id: $scope.item_vehiculo.id, nombre: $scope.item_vehiculo.nombre },
      servicio: $scope.item_servicio,
      extras: extras,
      total: $scope.total
    }

    data = {
      id_empleado: $scope.id_empleado,
      cantidad: $scope.item_servicio.cantidad,
      detalle: angular.toJson(data_servicio),
      total: $scope.total
    }

    serviciosService.comprar(data).then(function(dataResponse){

      if(dataResponse.data.result)
      {
        alert("Has adquirido tu servicio con exito, en un momento te enviaremos un correo con el codigo de servicio.");
        $scope.limpiar_datos();
      }
      else
      {
        alert(dataResponse.data.message);
      }

    });
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

});
  