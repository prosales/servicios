app.service('plantillasService',['$http', 'APP',  function($http, APP) {

    delete $http.defaults.headers.common['X-Requested-With'];

    this.getData = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'plantillas',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

    this.delete = function(id) {
        return $http.delete(APP.api + 'plantillas/' + id);
    };

    this.update = function(parametros) {
        return $http.put(APP.api + 'plantillas/' + parametros.id, parametros);
    };

    this.create = function(parametros) {
        return $http.post(APP.api + 'plantillas', parametros);
    };

    this.getPlantilla = function(id) {
        return $http.get(APP.api + 'plantillas/' + id);
    }

}]);