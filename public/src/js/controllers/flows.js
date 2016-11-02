app.controller('FlowsController', ['$scope', '$rootScope', '$modal', '$timeout', 'flowsService', 'plantillasService', 'usuariosService', 'localStorageService', 'ngTableParams', '$filter', 'toaster', function($scope, $rootScope, $modal, $timeout , flowsService, plantillasService, usuariosService, localStorageService, ngTableParams, $filter, toaster) {
  
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
    $scope.flow = {};
    $scope.cargando = 0;
    $scope.archivos = [];

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
    }

    $scope.view = function(item)
    {
        $scope.mostrar(2);
        $scope.settings.title = "Historico";
        $scope.view_flow = item;
    }

    $scope.cancelar = function()
    {
        $scope.mostrar(0);
    }

    $scope.guardar = function()
    {
        if(idregistro==0)
        {
            var responsables = [];
            angular.forEach($scope.dataPlantilla, function(value, index){
                valor = { idusuario: value.idresponsable, proceso: value.proceso, documento: value.documento  };
                responsables.push( valor );
            });
            var data = {
                descripcion: $scope.flow.descripcion,
                detalle: angular.toJson(responsables),
                usuario_creo: localStorageService.cookie.get('login').id,
                files: angular.toJson($scope.archivos),
                pasos: responsables.length,
                archivos: $scope.archivos.length
            }

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
    }

    $scope.mostrar = function(numero) {

            $scope.numero_paso = numero;

    };

    $scope.mostrarResponsables = function()
    {
        plantillasService.getPlantilla($scope.flow.idplantilla).then(function(dataResponse) {
                if(dataResponse.data.result)
                {
                    $scope.dataPlantilla = angular.fromJson(dataResponse.data.records.detalle);
                }
        });
    }

    $scope.eliminarItem = function(item)
    {
        var index = $scope.archivos.indexOf(item);
        $scope.archivos.splice(index, 1);
    }

    $scope.uploadFile = function(files) {

        $scope.cargando = 1;

        var fd = new FormData();
        
        fd.append("file", files[0]);

        flowsService.upload(fd).then(function(dataResponse) {
            if(dataResponse.data.result)
            {
                $scope.cargando = 0;
                $scope.archivos.push( dataResponse.data.url );
            }
            else
            {
                $scope.cargando = 0;
            }
        });

    };


}]);