app.controller('FlowsController', ['$scope', '$rootScope', '$modal', '$timeout', 'flowsService', 'plantillasService', 'localStorageService', 'ngTableParams', '$filter', 'toaster', function($scope, $rootScope, $modal, $timeout , flowsService, plantillasService, localStorageService, ngTableParams, $filter, toaster) {
  
    $scope.data = [];
    $scope.table = [];
    $rootScope.pageTitle = "Flows";
    $scope.settings = {
        singular: 'Flow',
        plural: 'Flows',
        title: 'Crear',
        accion: 'Crear'
    }
    $scope.numero_paso = 0;
    $scope.aprobadores = [{ nombre:  "", idpuesto: 0}];
    $scope.detalle = {};

    var idregistro = 0;

    function actualizar_datos()
    {
        flowsService.getData('GET', {}).then(function(dataResponse) {
                $scope.data = dataResponse.data.records;
                $scope.table = dataResponse.data.records;
                $scope.tableParams.reload();
        });
    }

    actualizar_datos();

    plantillasService.getData('GET', {}).then(function(dataResponse) {
        $scope.plantillas = dataResponse.data.records;
        $scope.plantillas.push( {id: 0, nombre: "Seleccione"} );
    });

    $scope.detalle = { descripcion: "", idplantilla: 0 };

    $scope.tableParams = new ngTableParams({
        page: 1,
        count: 5,
        sorting: {
            id: 'asc',
            nombre: 'asc'
        }
    }, {
        filterDelay: 50,
        total: $scope.table.length,
        getData: function($defer, params) {
            var searchStr = params.filter().search;
            if (searchStr) {

                searchStr = searchStr.toLowerCase();
                $scope.table = $scope.data.filter(function(item) {
                    return item.nombre.toLowerCase().indexOf(searchStr) > -1 || item.descripcion.toLowerCase().indexOf(searchStr) > -1;
                });

            } else {
                $scope.table = $scope.data;
            }
            $scope.table = params.sorting() ? $filter('orderBy')($scope.table, params.orderBy()) : $scope.table;
            $defer.resolve($scope.table.slice((params.page() - 1) * params.count(), params.page() * params.count()));
        }
    });

    $scope.crear = function()
    {
        $scope.mostrar(1);
        $scope.settings.accion = "Guardar";
        $scope.settings.title = "Crear";
        $scope.flow = {};
        $scope.aprobadores = [{ nombre:  "", idpuesto: 0}];
    }

    $scope.editar = function(item)
    {
        $scope.mostrar(1);
        $scope.settings.accion = "Editar";
        $scope.settings.title = "Editar";

        $scope.flow = {
            nombre: item.nombre,
            descripcion: item.descripcion
        };

        $scope.aprobadores = JSON.parse(item.detalle);
        idregistro = item.id;
    }

    $scope.cancelar = function()
    {
        $scope.mostrar(0);
    }

    $scope.guardar = function()
    {
        if(idregistro==0)
        {
            var data = {
                nombre: $scope.flow.nombre,
                descripcion: $scope.flow.descripcion,
                usuario_creo: localStorageService.cookie.get('login').id,
                usuario_modifico: 0,
                pasos: $scope.aprobadores.length,
                detalle: angular.toJson($scope.aprobadores)
            }

            //console.log(data);

            flowsService.create( data ).then(function(dataResponse){
                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    actualizar_datos();
                    $scope.mostrar(0);
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            });
        }
        else
        {
            var data = {
                id: idregistro,
                nombre: $scope.flow.nombre,
                descripcion: $scope.flow.descripcion,
                usuario_creo: localStorageService.cookie.get('login').id,
                usuario_modifico: 0,
                pasos: $scope.aprobadores.length,
                detalle: angular.toJson($scope.aprobadores)
            }

            //console.log(data);

            flowsService.update( data ).then(function(dataResponse){
                if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    actualizar_datos();
                    $scope.mostrar(0);
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
            });
        }
    }

    $scope.mostrar = function(numero) {

            $scope.numero_paso = numero;

    };

    $scope.mostrarResponsables = function()
    {
        plantillasService.getData('GET', $scope.flow.idplantilla).then(function(dataResponse) {
                if(dataResponse.data.result)
                {
                    //$scope.dataPlantilla = angular.fromJson(dataResponse.data.records.detalle);
                    console.log(dataResponse.data.records.detalle);
                }
        });
    }


}]);