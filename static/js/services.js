'use strict';
 
 //var EncuestApp = angular.module('EncuestaApp', ['ngRoute', 'ngResource', 'ngCookies']);
EncuestApp.factory('PreguntasResource', ['$resource',
  function($resource){
    return $resource('api/pregunta/', {}, {
    	get: {method:'GET'},	
      	save: {method:'POST'},
    });
  }]);

EncuestApp.factory('PreguntaResource', ['$resource',
  function($resource){
    return $resource('api/pregunta/:id', {}, {
    	get: {method:'GET'},	
      	
    });
  }]);

EncuestApp.factory('OpcionResource', ['$resource',
  function($resource){
    return $resource('api/opcion/', {}, {
    	get: {method:'GET'},
      	save: {method:'POST'},
    });
  }]);

EncuestApp.factory('OpcionPreguntaResource', ['$resource',
  function($resource){
    return $resource('api/pregunta/:id/opciones/', {}, {
    	query: {method:'GET', isArray:true},
      	
    });
  }]);

EncuestApp.factory('UsuarioResource', ['$resource',
  function($resource){
    return $resource('api/usuario/', {}, {
    	get: {method:'GET', isArray:true},
      	save: {method:'POST'},
    });
  }]);

EncuestApp.factory('IniciarSession', ['$resource',
  function($resource){
    return $resource('api/login/', {}, {
    	iniciar: {method:'POST'},
    });
  }]);

EncuestApp.factory('ResultadoResource', ['$resource',
  function($resource){
    return $resource('api/resultado/', {}, {
    	get: {method:'GET', isArray:true},
    });
  }]);



EncuestApp.factory('Session', ['$cookieStore', 
	function($cookieStore){
		return {
			getUsuario: function(){ 
				return $cookieStore.get('usuario')
			}
		
	};
}]);
