'use strict';

var EncuestApp = angular.module('EncuestaApp', ['ngRoute', 'ngResource', 'ngCookies']);

EncuestApp.run(function($rootScope, $location, $cookieStore){
	
	$rootScope.$on('$routeChangeStart',function (event, next, current){

		if ($cookieStore.get('online') == false || $cookieStore.get('online') == null) {

			if (next.templateUrl == 'templates/crearPreguntas.html' || next.templateUrl == 'templates/editarPreguntas.html' || next.templateUrl == 'templates/preguntas.html' ){

				console.log(next.templateUrl);
				$location.path('/login');
			};
			
			
		}else{

			var usuario = $cookieStore.get('usuario');

			if ((next.templateUrl == 'templates/login.html' || next.templateUrl == 'templates/crearPreguntas.html') && usuario.permiso != 1 ) {

				$location.path('/inicio');
			};
		};


	});

	
});

EncuestApp.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/', {
        templateUrl: 'templates/inicio.html',
        //controller: 'PhoneListCtrl'
      }).
      when('/login', {
        templateUrl: 'templates/login.html',
        controller: 'UsuarioCtrl'
      }).
      when('/singup', {
        templateUrl: 'templates/singUp.html',
        controller: 'UsuarioCtrl'
      }).
      when('/preguntas', {
        templateUrl: 'templates/preguntas.html',
        controller: 'PreguntaCtrl',
      }).
      when('/crear', {
        templateUrl: 'templates/crearPreguntas.html',
        controller: 'PreguntasCtrl',
      }).
      when('/pregunta/:id/edit/', {
        templateUrl: 'templates/editarPregunta.html',
        controller: 'PreguntaEditCtrl',
      }).
      when('/home', {
        templateUrl: 'templates/finalizar.html',
        controller: 'PreguntaCtrl',
      }).
      when('/resultado', {
        templateUrl: 'templates/resultado.html',
        controller: 'ResultadoCtrl',
      }).
      otherwise({
        redirectTo: '/'
      });
 }]);