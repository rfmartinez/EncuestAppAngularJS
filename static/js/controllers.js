'use strict';
//importar debajo de app.js

//controlador principal para manejar el header
EncuestApp.controller('appCtrl', function ($scope, $cookieStore, $location, Session){

	//obtenemos el usuario conectado del servicio session
	$scope.usuarioConectado = Session.getUsuario();
	//validamos si usuarioConectado esta indefinido, osea si el servicio no trae nada queda undefined
	if (!$scope.usuarioConectado) {
		//si esta indefinido, lo declaramos
		$scope.usuarioConectado = {};

	};



	

	$scope.salir = function () {
		$scope.usuarioConectado = {"user": null};

		$cookieStore.remove('usuario');
		$cookieStore.remove('online');

		$location.path('/login');

	};

	$scope.entrar = function () {
		$location.path('/login');
		
	};


});

EncuestApp.controller('PreguntasCtrl', function($scope, PreguntasResource, OpcionResource){
	$scope.formData = {};
	$scope.opciones = [{}];
	$scope.createPregunta = function(){


  		var opc = $scope.opciones;
  		var isr = 0;
  		for (var i = 0; i < opc.length; i++) {

			if (opc[i].isrespuesta==1) {

				isr++;
			};

  				
  					
  		};

		if (isr==1) {

			PreguntasResource.save($scope.formData, function(data){
  			
  			$scope.formData = {};
 			
  			var op = $scope.opciones;
	  			for (var i = 0; i < op.length; i++) {

	  				var opcion = {"opcion":op[i].texto, "pregunta_id": data['id'], "respuesta":op[i].isrespuesta}

	  				OpcionResource.save(opcion, function(data) {

	  					$scope.opciones = [{}];
	  					
	  				});
	  			
	  			};


  			});
		} else{

			alert("debes escoger una respuesta");

		};
    };

    $scope.addOpcion = function() {
    	var opciones = $scope.opciones;
    	opciones[opciones.length] = {};
  	};
    
  	$scope.removeOpcion = function(index) {
    	$scope.opciones.splice(index, 1);
  	};
  	
  	$scope.OnChangeVal = function (index) {
  	// esta me ayuda a resetear los radio de las preguntas que se estan creando
		var opt = $scope.opciones;
  		
  		for (var i = 0; i < opt.length; i++) {
			if (i!=index){
  				$scope.opciones[i].isrespuesta=0;
  				//console.log($scope.opciones[i]);
			} 	
  		};

  		//console.log(index);
  	};

});

EncuestApp.controller('PreguntaCtrl', function ($scope,ResultadoResource, PreguntasResource,$location,$filter, OpcionResource, Session, $http ) {
	$scope.usuarioConectado = Session.getUsuario();
	$scope.ranking = ResultadoResource.get();
	$scope.resultadoUsuario = {};
	
	$http.get('api/resultado/'+$scope.usuarioConectado.id).success( function (data) {
 		
  		if (!data.message) {
  			
  			//$scope.resultadoUsuario = 1;
  			$location.path('/resultado');
  			
  		};
 
  	});

	  

  	$scope.formData = {};
	  $scope.msg = '';
	  $scope.d = {};
	  $scope.time = {};
	  $scope.opciones = [{}];
	  $scope.preguntas = {};
	  $scope.preguntaBorrar = new Array();
	  $scope.respuestas = new Array();
	  $scope.dataRespuesta = {"opcion_id":"", "pregunta_id":"", "usuario_id":""};
	  $scope.opcionSelected = {};
	  
	  //$scope.preguntaActual = {};
  

  //$scope.usuarioConectado = Session.getUsuario();
	  $http.get('api/pregunta/').success(function(data) {

	  		$scope.preguntas = data;
	  		console.log($scope.preguntas);

	  });

	
	$scope.comenzar =  function () {
	  	
	  	$scope.preguntaActual = $scope.preguntas[$scope.preguntas.length-1];
	  	$scope.time.start = Date.now();

	};

  	$scope.OnChangeVal = function (index) {
  	// esta me ayuda a resetear los radio de las preguntas que se estan creando
		var opt = $scope.opciones;
  		
  		for (var i = 0; i < opt.length; i++) {
			if (i!=index){
  				$scope.opciones[i].isrespuesta=0;
  				//console.log($scope.opciones[i]);
			} 	
  		};

  		//console.log(index);
  	};

	$scope.OnChangeRadio = function (index) {
	  	//funcion para obtener el valor de la opcion seleccionada, y guardarla en la variable d
	  	$scope.d.opcion = $scope.preguntaActual.opciones[index];
	  	//console.log($scope.d.opcion);
	 };
	 
  	$scope.seguir = function () {

  		if ($scope.d.opcion) {
	  		$scope.preguntaBorrar.push($scope.preguntaActual);
	  		$scope.dataRespuesta.opcion_id = $scope.d.opcion.id;
			$scope.dataRespuesta.usuario_id = $scope.usuarioConectado.id;
			$scope.dataRespuesta.pregunta_id = $scope.d.opcion.pregunta_id;
			console.log($scope.dataRespuesta);
	  		$scope.respuestas.push($scope.dataRespuesta);
	  		console.log($scope.respuestas);
	  		$scope.dataRespuesta = {};
	  		//borramos la ultima pregunta, que fue la que utilizamos.
	  		$scope.preguntas.splice($scope.preguntas.length-1, 1);

	  		//console.log($scope.dataRespuesta);
	  		//la pregunta actual ahora sera la ultima del array de preguntas
	  		$scope.preguntaActual = $scope.preguntas[$scope.preguntas.length-1];

  			console.log($scope.preguntas);

  			if ($scope.preguntas.length==0) {

  				$http.post('api/respuesta/usuario/'+$scope.usuarioConectado.id, $scope.respuestas)
  					.success(function(data) {

			      		$scope.msg = data.status;
			      		console.log($scope.msg);
			      		if (data.status && data.cod==2) {
				      		$http.post('api/resultado/usuario/'+$scope.usuarioConectado.id, $scope.time.seconds)
	  							.success(function(data) {

				      				console.log('api/resultado/usuario/'+$scope.usuarioConectado.id);
				      				$scope.ranking = ResultadoResource.get();
				      				$location.path('/resultado');
	  							})
	  							.error(function(data, status, headers, config) {
	     						// this isn't happening:
					     		console.log("error" + data);
					 			});
			      			
			      		};

  					})
  					.error(function(data, status, headers, config) {
     					// this isn't happening:
				     console.log("error" + data);
				 	}); 
		      
  					//$scope.time.end = $filter('date')(Date(),'M/d/yy HH:mm:ss');
  					$scope.time.end =  Date.now();

  					$scope.time.transcurrido = $scope.time.end - $scope.time.start;
  					var trans = $scope.time.transcurrido;
  					$scope.time.seconds = trans / 1000;

  				
  					
  			
  			}
  		
  		}else{

  				alert("debes escoger una opcion antes de seguir!");
  			};
	  };

  



  	


	  
  	
});



EncuestApp.controller('PreguntaEditCtrl', function ($scope, PreguntaResource,$location, OpcionResource, OpcionPreguntaResource, $routeParams) {

  var pregunta = PreguntaResource.get({id: $routeParams.id});
  $scope.formData = pregunta;
  var opciones = OpcionPreguntaResource.query({id: $routeParams.id });
  $scope.opciones = opciones;

  console.log($scope.opciones);

  //$scope.opciones = OpcionResource;
  $scope.createPregunta = function(){
  		PreguntasResource.save($scope.formData, function(data){
  			console.log($scope.formData);
  			console.log(data['id']);
  			$scope.formData = {};

  			console.log($scope.opciones);
  			var op = $scope.opciones;
  			for (var i = 0; i < op.length; i++) {

  				console.log(op[i].isrespuesta);

  				opcion = {"opcion":op[i].texto, "pregunta_id": data['id'], "respuesta":op[i].isrespuesta}

  				OpcionResource.save(opcion, function(data) {

  					$scope.opciones = [{}];
  					
  				});
  			
  			};


  		});
  		
  		
  		

    };

    $scope.addOpcion = function() {
    	var opciones = $scope.opciones;
    	opciones[opciones.length] = {};
  	};
    
  	$scope.removeOpcion = function(index) {
    	$scope.opciones.splice(index, 1);
  	};
  	
  	$scope.setChoiceForQuestion = function(c) {
        
        for(opcion in $scope.opciones){

        	if(opcion != c){

        		opcion.respuesta="false"
        	}

        }
        
        
        
        
    };


});


EncuestApp.controller('OpcionCtrl', function ($scope, PreguntaResource, OpcionResource, $routeParams) {
  $scope.formData = {};
  $scope.pregunta = PreguntaResource.get({id: $routeParams.id});
  $scope.createOpcion = function(){
  		OpcionResource.save($scope.formData);
  		$scope.formData = {};
  		

    };
   


});

EncuestApp.controller('UsuarioCtrl', function ($scope,$location, UsuarioResource, IniciarSession, $q, $log, $cookieStore, $http, Session) {
  	$scope.user = {};
  	
  	$scope.crearUsuario = function(){
  		console.log($scope.user);
  		UsuarioResource.save($scope.user, function(data){
  			$scope.user = {};
  			$scope.msg = data.error;
  			$location.path('/login');

  		});
  	};
      

    $scope.iniciarSesion = function () {

    	console.log($scope.user);

    	$http.post('api/login/', $scope.user ).success(function(data) {
		      
		      console.log(data);
		      if (data.message) {
		    		$scope.msg = data.message;
		    		    		

		    	}else{
		    		$scope.usuarioConectado.id = data.id;
		    		$scope.usuarioConectado.user = data.user;
			    	$scope.usuarioConectado.codigo = data.codigo;
			    	$scope.usuarioConectado.permiso = data.permiso;
			    	
			    	$log.info($scope.usuarioConectado);

			    	$cookieStore.put('usuario', data);
			    	$cookieStore.put('online', true);
			    	//$scope.usuarioConectado = Session.getUsuario();
			    	//console.log($scope.usuarioConectado);
			    	$location.path('/preguntas')
		    	};
		    	
		    });
  
    
    }; 


});

EncuestApp.controller('ResultadoCtrl', function ($scope,$http, Session, ResultadoResource) {
	$scope.resultado={};
	$scope.ranking = {};
	$scope.ranking = ResultadoResource.get();
	
  	$scope.usuario = Session.getUsuario();
  $http.get('api/resultado/'+$scope.usuario.id).success( function (data) {

  		
  		if (data.message) {
  			$scope.msg = data.message;

  		} else{

  			$scope.resultado = data;

  			//console.log(data);
  			
  		};

  });
	  
   


});


