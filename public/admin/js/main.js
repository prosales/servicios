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
    .when("/clientes", {templateUrl: "views/clientes.html", controller: "EmpleadosController"})
    .when("/tipos_vehiculos", {templateUrl: "views/tipos_vehiculos.html", controller: "TiposVehiculosController"})
    .when("/servicios", {templateUrl: "views/servicios.html", controller: "ServiciosController"})
    .when("/extras", {templateUrl: "views/extras.html", controller: "ExtrasController"})
    .when("/colas", {templateUrl: "views/colas.html", controller: "ColasController"})
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

  $scope.userid = localStorageService.cookie.get('login').id;
  $scope.username = localStorageService.cookie.get('login').nombre;
  $scope.user = localStorageService.cookie.get('login').usuario;
  $scope.type = localStorageService.cookie.get('login').id_tipo_usuario;

  loader();

});

app.controller('DashboardController', function ($scope, $window, $http, comprasService) {

  $scope.codigo = ""
  $scope.compra = {};
  $scope.extras = [];
  $scope.mostrar = 0;
  $scope.id_compra = 0;

  $scope.limpiar_datos = function()
  {
    $scope.codigo = ""
    $scope.compra = {};
    $scope.extras = [];
    $scope.mostrar = 0;
    $scope.id_compra = 0;
  }

  $scope.consultar = function()
  {
    if($scope.codigo!="")
    {
      comprasService.consultar("GET", {codigo: $scope.codigo}).then(function(dataResponse){

        if(dataResponse.data.result)
        {
          info = dataResponse.data.records;
          $scope.id_compra = info.id;
          $scope.compra = {
            cliente: info.empleado.primer_nombre + " " +info.empleado.primer_apellido,
            tipo_vehiculo: info.detalle.tipo_vehiculo.nombre,
            servicio: info.detalle.servicio.nombre,
            total: info.total
          }
          $scope.extras = info.detalle.extras;
          $scope.mostrar = 1;
        }
        else
        {
          alert(dataResponse.data.message);
          $scope.limpiar_datos();
        }

      });
    }
    else
    {
      alert("Debes ingresar un codigo para consultar datos.");
    }
  }

  $scope.agregar = function()
  {
    comprasService.agregar("GET", {id_compra: $scope.id_compra}).then(function(dataResponse){

      if(dataResponse.data.result)
      {
        alert("Servicio agregado a cola correctamente");
        $scope.limpiar_datos();
      }
      else
      {
        alert(dataResponse.data.message);
      }

    });
  }

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
  

});

app.controller('EmpleadosController', function ($scope, $window, empleadosService) {

  $scope.data = [];
  $scope.settings = {
    singular: 'Cliente',
    plural: 'Clientes',
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
    empleadosService.getData("GET", {}).then(function(dataResponse)
    {
      $scope.data = dataResponse.data.records;
    });
  }

  $scope.cargar_datos();

  $scope.activar = function(item)
  {
    $scope.settings.accion = 'Activar';
    $scope.mostrar = 1;
    $scope.item = item;
  }

  $scope.cancelar = function()
  {
    $scope.mostrar = 0;
  }

  $scope.guardar = function(item)
  {
      empleadosService.activar(item.id).then(function(dataResponse)
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
  

});

app.controller('ExtrasController', function ($scope, $window, extrasService, vehiculosService) {

  $scope.data = [];
  $scope.precios = [];
  $scope.settings = {
    singular: 'Extra',
    plural: 'Extras',
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
    extrasService.getData("GET", {}).then(function(dataResponse)
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
      
      extrasService.create(item).then(function(dataResponse)
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

      extrasService.update(datos).then(function(dataResponse)
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
      extrasService.delete(item.id).then(function(dataResponse)
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
  

});

app.controller('ColasController', function ($scope, $window, comprasService) {

  $scope.data = [];
  
  $scope.cargar_datos = function()
  {
    comprasService.colas("GET", $scope.userid).then(function(dataResponse){

      $scope.data = dataResponse.data.records;

    });
  }

  $scope.cargar_datos();

  $scope.iniciar = function(item)
  {
    comprasService.lavado("GET", {id: item.id, inicio: 1}).then(function(dataResponse){

      if(dataResponse.data.result)
      {
        alert("Iniciando lavado...");
        $scope.cargar_datos();
      }
      else
      {
        alert(dataResponse.data.message);
      }

    });
  }

  $scope.finalizar = function(item)
  {
    comprasService.lavado("GET", {id: item.id}).then(function(dataResponse){

      if(dataResponse.data.result)
      {
        alert("Has finalizado el lavado correctamente");
        $scope.cargar_datos();
      }
      else
      {
        alert(dataResponse.data.message);
      }

    });
  }

  $scope.getStringArray = function(item) {
      return item.map(function(elem) {
          return elem.nombre;
      }).join(", ");
  }

});
  