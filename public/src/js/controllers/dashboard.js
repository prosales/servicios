app.controller('DashboardController', ['$scope', '$rootScope', '$modal', '$timeout', 'dashboardService', 'ngTableParams', '$filter', 'toaster', function($scope, $rootScope, $modal, $timeout , dashboardService, ngTableParams, $filter, toaster) {
  
    $scope.data = [];
    $scope.table = [];
    $rootScope.pageTitle = "Inicio / Bandeja";
    $scope.settings = {
        singular: 'Usuario',
        plural: 'Usuarios',
        modal: 'Crear Usuario',
        accion: 'Guardar'
    }


}]);