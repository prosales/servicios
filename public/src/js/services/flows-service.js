app.service('flowsService',['$http', 'APP',  function($http, APP) {

    delete $http.defaults.headers.common['X-Requested-With'];

    this.getData = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'flows',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

    this.delete = function(id) {
        return $http.delete(APP.api + 'flows/' + id);
    };

    this.update = function(parametros) {
        return $http.put(APP.api + 'flows/' + parametros.id, parametros);
    };

    this.create = function(parametros) {
        return $http.post(APP.api + 'flows', parametros);
    };

    this.upload = function(parametros) {
        return $http.post(APP.api + 'upload', parametros, {
            withCredentials: true,
            headers: {'Content-Type': undefined },
            transformRequest: angular.identity
        });
    }

}]);