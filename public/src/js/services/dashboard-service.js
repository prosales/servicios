app.service('dashboardService',['$http', 'APP',  function($http, APP) {

    delete $http.defaults.headers.common['X-Requested-With'];

    this.getData = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'flows_pendientes',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

    this.aprobarFlow = function(parametros) {
        return $http.post(APP.api + 'aprobar_flow', parametros);
    };



}]);