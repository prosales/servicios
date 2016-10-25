'use strict';

/**
 * Config for the router
 */
angular.module('app')
  .run(
    [          '$rootScope', '$state', '$stateParams',
      function ($rootScope,   $state,   $stateParams) {
          $rootScope.$state = $state;
          $rootScope.$stateParams = $stateParams;
      }
    ]
  )
  .config(
    [          '$stateProvider', '$urlRouterProvider', 'JQ_CONFIG', 
      function ($stateProvider,   $urlRouterProvider, JQ_CONFIG) {
          
          $urlRouterProvider
              .otherwise('/app/dashboard');
          $stateProvider
              .state('app.usuarios', {
                  url: '/usuarios',
                  templateUrl: 'tpl/usuarios.html',
                  controller: 'UsuariosController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad ){
                          return $ocLazyLoad.load('toaster').then(
                              function(){
                                  return $ocLazyLoad.load('js/controllers/usuarios.js');
                              }
                          );
                      }]
                  }
              })
              .state('app.puestos', {
                  url: '/puestos',
                  templateUrl: 'tpl/puestos.html',
                  controller: 'PuestosController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad ){
                          return $ocLazyLoad.load('toaster').then(
                              function(){
                                  return $ocLazyLoad.load('js/controllers/puestos.js');
                              }
                          );
                      }]
                  }
              })
              .state('app', {
                  abstract: true,
                  url: '/app',
                  templateUrl: 'tpl/app.html'
              })
              .state('app.dashboard', {
                  url: '/dashboard',
                  templateUrl: 'tpl/dashboard.html',
                  controller: 'DashboardController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad ){
                          return $ocLazyLoad.load('toaster').then(
                              function(){
                                  return $ocLazyLoad.load('js/controllers/dashboard.js');
                              }
                          );
                      }]
                  }
              })
              .state('app.plantillas', {
                  url: '/plantillas',
                  templateUrl: 'tpl/plantillas.html',
                  controller: 'PlantillasController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad ){
                          return $ocLazyLoad.load('toaster').then(
                              function(){
                                  return $ocLazyLoad.load('js/controllers/plantillas.js');
                              }
                          );
                      }]
                  }
              })
              .state('app.flows', {
                  url: '/flows',
                  templateUrl: 'tpl/flows.html',
                  controller: 'FlowsController',
                  resolve: {
                      deps: ['$ocLazyLoad',
                        function( $ocLazyLoad ){
                          return $ocLazyLoad.load('toaster').then(
                              function(){
                                  return $ocLazyLoad.load('js/controllers/flows.js');
                              }
                          );
                      }]
                  }
              })
      }
    ]
  );
