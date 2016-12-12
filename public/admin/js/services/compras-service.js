app.service('comprasService',['$http', 'APP',  function($http, APP) {

    delete $http.defaults.headers.common['X-Requested-With'];

    this.getData = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'compras',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

    this.delete = function(id) {
        return $http.delete(APP.api + 'compras/' + id);
    };

    this.update = function(parametros) {
        return $http.put(APP.api + 'compras/' + parametros.id, parametros);
    };

    this.create = function(parametros) {
        return $http.post(APP.api + 'compras', parametros);
    };

    this.consultar = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'consultar_servicio',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

    this.agregar = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'agregar_servicio',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

    this.colas = function(metodo, id) {

        return $http({
            method: metodo,
            url: APP.api + 'consultar_colas/'+id,
            params: {},
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

    this.lavado = function(metodo, parametros) {

        return $http({
            method: metodo,
            url: APP.api + 'lavado',
            params: parametros,
            headers: {
                'Authorization': 'Token token=xxxxYYYYZzzz'
            }
        });
    };

}]);