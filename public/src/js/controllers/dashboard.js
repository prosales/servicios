app.controller('DashboardController', ['$scope', '$rootScope', '$modal', '$timeout', 'dashboardService', 'flowsService', 'localStorageService', 'ngTableParams', '$filter', 'toaster', function($scope, $rootScope, $modal, $timeout , dashboardService, flowsService, localStorageService, ngTableParams, $filter, toaster) {
  
    $scope.flows = [];
    $rootScope.pageTitle = "Inicio / Bandeja";
    $scope.settings = {
        singular: 'Usuario',
        plural: 'Usuarios',
        modal: 'Crear Usuario',
        accion: 'Guardar'
    }
    $scope.numero_paso = 0;
    $scope.cargando = 0;
    $scope.archivos = [];
    $scope.archivos_news = [];

    function actualizar_datos()
    {
        dashboardService.getData('GET', {idusuario: localStorageService.cookie.get('login').id}).then(function(dataResponse) {
                $scope.flows = dataResponse.data.records;
        });
    }

    actualizar_datos();

    $scope.editar = function(item)
    {
    	$scope.numero_paso = 1;

        console.log(item);

    	$scope.flow = {
    		proceso: item.proceso,
    		comentario: "",
    		id: item.id
    	}
        $scope.archivos = item.files;
    }

    $scope.guardar = function(opcion)
    {
    	if(opcion == 1)
    	{
    		var data = {
    			aprobado: opcion,
    			comentario: $scope.flow.comentario,
                files: angular.toJson($scope.archivos_news),
    			id: $scope.flow.id
    		};

    		dashboardService.aprobarFlow(data).then(function(dataResponse){
    			if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    actualizar_datos();
                    $scope.numero_paso = 0;
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
    		});
    	}
    	else if(opcion == 2)
    	{
    		var data = {
    			aprobado: opcion,
    			comentario: $scope.flow.comentario,
    			id: $scope.flow.id
    		};

    		dashboardService.aprobarFlow(data).then(function(dataResponse){
    			if(dataResponse.data.result)
                {
                    toaster.pop('success', 'Exito!', dataResponse.data.message);
                    actualizar_datos();
                    $scope.numero_paso = 0;
                }
                else
                {
                    toaster.pop('Error', 'Espera!', dataResponse.data.message);
                }
    		});
    	}
    	else
    	{
    		$scope.numero_paso = 0;
    		$scope.flow = {};
    	}
    }

    $scope.uploadFile = function(files) {

        $scope.cargando = 1;

        var fd = new FormData();
        
        fd.append("file", files[0]);

        flowsService.upload(fd).then(function(dataResponse) {
            if(dataResponse.data.result)
            {
                $scope.cargando = 0;
                $scope.archivos_news.push( dataResponse.data.url );
            }
            else
            {
                $scope.cargando = 0;
            }
        });

    };

}]);