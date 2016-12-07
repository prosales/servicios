app.service('serviciosService',['$http', 'APP',  function($http, APP) {

    delete $http.defaults.headers.common['X-Requested-With'];

    this.getData = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'servicios',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

    this.delete = function(id) {
        return $http.delete(APP.api + 'servicios/' + id);
    };

    this.update = function(parametros) {
        return $http.put(APP.api + 'servicios/' + parametros.id, parametros);
    };

    this.create = function(parametros) {
        return $http.post(APP.api + 'servicios', parametros);
    };

}]);