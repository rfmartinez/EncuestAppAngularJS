<?php

require 'vendor/autoload.php';

require "NotORM.php";

desarrollo
 $servidor= "172.0.0.1";
$user="root";
$pass="";
 
$pdo = new PDO("mysql:dbname=encuesta;host=127.0.0.1", $user, $pass);


$db = new NotORM($pdo);


$app = new \Slim\Slim();

$app->config(array(
    'debug' => true,
    
));

//principal
$app->get('/', function (){
	
	
	echo "Api para encuesta!";
		
    

});

$app->post('/usuario/', function () use($app, $db){
    
    //$app->response->headers->set('Content-Type', 'application/json');
    // con getBody recojemos el json que enviamos desde angular, y lo convertimos a array para que se inserte a la base de datos
    $ujson = $app->request()->getBody();

    $u = (array) json_decode($app->request()->getBody());

    
    
    
    if ($r = $db->usuario()->where("user", $u['user'] )->fetch()){
                
                echo json_encode(array(
                        "error" => 'ya existe un usuario con ese usuario',
                                               
                    ));
                
            } elseif($r = $db->usuario()->where("codigo", $u['codigo'] )->fetch()) {
                
                echo json_encode(array(
                        "error" => 'ya existe un usuario con ese codigo',
                                               
                    ));
                
            }else{


                $result = $db->usuario->insert($u);

                echo json_encode(array("id" => $result["id"]));
                
            }
            

    

});



//devuelve la lista de preguntas....
$app->get('/pregunta/', function () use($app, $db){
	
	$app->response->headers->set('Content-Type', 'application/json');
	
	$data = array();
	$allPreguntas = array();
	$preguntas = $db->pregunta();
	$pArray = array();
	
	
	// para convertir las preguntas de objeto NotOrm a array normal.
    foreach ($preguntas as $p) {
			$allPreguntas[]= array(
			"id" => $p["id"],
            "pregunta" => $p["pregunta"]);
		
	}
	
	
	
	
	//obtengo la cantidad de preguntas que quiero mostrar, array_rand devuelve un array con los indices seleccionados 
	//aleatoriamente	
	$rand_keys = array_rand($allPreguntas, 3);
	//print_r($rand_keys);
		
	//for principal para recorrer las preguntas convertidas en array normal	
	for ($i=0; $i < count($allPreguntas); $i++) {
		//foreach para obtener el valor del indice que devuelve array_rand
		foreach ($rand_keys as $key => $value) {
			//si el $i (index) del array principal es igual al $value de foreach, entonces
			//lo que quiere decir que es una pregunta de las que se seleccionaron aleatoriamente
			if ($i == $value) {
			//obtengo el array de la pregunta actual del ciclo	
			 $pindex =	$allPreguntas[$value];
			 //array para guardar las opciones de las preguntas
			 $op = array();
			 //recorrro la respuesta de la consulta de las opciones cuyo id sea igual al de la pregunta actual ($pindex['id'])
				foreach ($db->opcion()->where('pregunta_id', $pindex['id']) as $o){
					//por cada opcion lo convierto en un array y lo guardo en $op
		            $op[] = array('id' => $o["id"] ,
		                        'opcion' => $o["opcion"],
		                        'pregunta_id' => $o['pregunta_id']); 
		
		         }
	        // $data inserto las preguntas con sus respectivas opciones
		        $data[]  = array(
	            "id" => $pindex["id"],
	            "pregunta" => $pindex["pregunta"],
	            "opciones" => $op,
	
	            
	        	);
			 
			}
		} 
		
	}
    
    //si hay algo en $data entonces imprimimos data como json    
    if ($data) {
        # code...
    	
    	echo json_encode($data);
    }else{
		echo json_encode(array(
            "error" => "revise la base de datos si contiene preguntas!",
            
            ));
		
        
    }
    
	
		
    
});



$app->post('/pregunta/', function () use($app, $db){
	
	$app->response->headers->set('Content-Type', 'application/json');
    // con getBody recojemos el json que enviamos desde angular, y lo convertimos a array para que se inserte a la base de datos
	$pregunta = (array) json_decode($app->request()->getBody());

    $result = $db->pregunta->insert($pregunta);

	echo json_encode(array("id" => $result["id"]));

});

$app->get('/pregunta/:id/opciones/', function ($id) use($app, $db){
    
    $app->response->headers->set('Content-Type', 'application/json');
    $pregunta = $db->opcion()->where("pregunta_id", $id);
    foreach ($db->opcion()->where("pregunta_id", $id) as $e) {

        $data[]  = array(
            "id" => $e["id"],
            "opcion" => $e["opcion"],
            "respuesta" => $e["respuesta"],
            "pregunta_id" => $e["pregunta_id"]
            
        );
    }
    if (count($data) != -1) {
        echo json_encode($data);
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "encuesta ID $id does not exist"
            ));
    }

});

$app->get("/pregunta/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $pregunta = $db->pregunta()->where("id", $id);
    if ($data = $pregunta->fetch()) {
        echo json_encode(array(
            "id" => $data["id"],
            "pregunta" => $data["pregunta"]
            
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "encuesta ID $id does not exist"
            ));
    }
});

/*
$app->put("/pregunta/:id", function ($id) use ($app, $db) {
    $pregunta = $db->pregunta()->where('id', $id);
    $data = null;
 
    if ($pregunta->fetch()) {
        
        $p = (array) json_decode($app->request()->getBody());
 
        
        $result = $pregunta->update($p);
        echo json_encode(array(
            "status" => (bool)$result,
            "message" => "Pregunta updated successfully"
            ));
    }else{
        echo json_encode(array(
            "status" => false,
            "message" => "Pregunta id $id does not exist"
        ));
    }
});
*/
// rutas opciones
$app->get('/opcion/', function () use($app, $db){
	$data = array();	
    foreach ($db->opcion() as $e) {
        $data[]  = array(
            "id" => $e["id"],
            "opcion" => $e["opcion"],
            "pregunta_id" => $e["pregunta_id"]
            
        );
    }
	$app->response->headers->set('Content-Type', 'application/json');
	echo json_encode($data);
		
    
});

$app->post('/opcion/', function () use($app, $db){
	
	$app->response->headers->set('Content-Type', 'application/json');
    $opcion = (array) json_decode($app->request()->getBody());
	$result = $db->opcion->insert($opcion);
	echo json_encode(array("id" => $result["id"]));

});

$app->get("/opcion/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $opcion = $db->opcion()->where("id", $id);
    if ($data = $opcion->fetch()) {
        echo json_encode(array(
            "id" => $data["id"],
            "opcion" => $data["opcion"],
            "pregunta_id" => $data["pregunta_id"]
            
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Opcion ID $id does not exist"
            ));
    }
});

$app->put("/opcion/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $opcion = $db->opcion()->where("id", $id);
    if ($opcion->fetch()) {
        $post = $app->request()->put();
        $result = $opcion->update($post);
        echo json_encode(array(
            "status" => (bool)$result,
            "message" => "Opcion updated successfully"
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Opcion id $id does not exist"
        ));
    }
});


// rutas usuario

$app->get('/usuario/', function () use($app, $db){
	$data = array();	
    foreach ($db->usuario() as $e) {
        $data[]  = array(
            "id" => $e["id"],
            "user" => $e["user"],
            "codigo" => $e["codigo"]
            
        );
    }
	$app->response->headers->set('Content-Type', 'application/json');
	echo json_encode($data);
		
    
});

$app->post('/usuario/', function () use($app, $db){
	
	$app->response->headers->set('Content-Type', 'application/json');
	$usuario = $app->request()->post();
	$result = $db->usuario->insert($usuario);
	echo json_encode(array("id" => $result["id"]));

});

$app->get("/usuario/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $usuario = $db->usuario()->where("id", $id);
    if ($data = $usuario->fetch()) {
        echo json_encode(array(
            "id" => $data["id"],
            "user" => $data["user"],
            
            
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Usuario ID $id does not exist"
            ));
    }
});

$app->post('/login/', function () use($app, $db){
    
   $app->response->headers->set('Content-Type', 'application/json');
    $usuario = (array) json_decode($app->request()->getBody());
    if ($usuario['user'] and $usuario['codigo']) {

        $user = $db->usuario()->where("user", $usuario['user'] )->where("codigo", $usuario['codigo']);
        if ($data = $user->fetch()) {

            echo json_encode(array("id" => $data["id"], 
                                    "user" => $data["user"],
                                    "codigo"=> $data["codigo"],
                                    "permiso"=> $data["permiso"]));
        }else{

            echo json_encode(array(
            "status" => false,
            "message" => "Usuario no existe"
            ));

        }
        

    }

});


/*
$app->put("/opcion/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $opcion = $db->opcion()->where("id", $id);
    if ($opcion->fetch()) {
        $post = $app->request()->put();
        $result = $opcion->update($post);
        echo json_encode(array(
            "status" => (bool)$result,
            "message" => "Opcion updated successfully"
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Opcion id $id does not exist"
        ));
    }
});

*/

$app->post('/respuesta/usuario/:id', function ($id) use($app, $db){
	
	$app->response->headers->set('Content-Type', 'application/json');
	$respuesta = json_decode($app->request()->getBody());
    $validaUser =  $db->respuesta()->where("usuario_id", $id);
    
        if (!$validaUser->fetch()) {

            foreach ($respuesta as $res) {
                $respuestaArray = array('usuario_id' => $res->usuario_id , 'opcion_id' => $res->opcion_id, 'pregunta_id' => $res->pregunta_id);
                $result = $db->respuesta->insert($respuestaArray);
            
                };
        
                echo json_encode(array(
                    "status" => "successfully"
                    ,"cod" => 2));
        } else {
                echo json_encode(array("status" => "Usted ya ha enviado sus respuestas, No se puede volver a enviar!", "cod" => 1));
                
        };
        
    //$result = $db->respuesta->insert($respuesta);

});

$app->get("/respuesta/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");

    $respuesta = $db->respuesta()->where("id", $id);
    if ($data = $respuesta->fetch()) {
        echo json_encode(array(
            "id" => $data["id"],
            "usuario_id" => $data["usuario_id"],
            "opcion_id" => $data["opcion_id"],
            "pregunta_id" => $data["pregunta_id"]
            
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Usuario ID $id does not exist"
            ));
    }
});

// resultado
$app->post('/resultado/usuario/:id', function ($id) use($app, $db){
	
	//$app->response->headers->set('Content-Type', 'application/json');
	$tiempo = $app->request()->getBody();
    $respuestas = $db->respuesta()->where('usuario_id', $id);
    $correctas = 0;
    $promedio = 0;
    //print_r($respuestas);
    $numRespuestas = 0;

    
    if ($respuestas->fetch()) {
         foreach ($respuestas as $respuesta) {
            $numRespuestas++;
            $opcion_id = $respuesta['opcion_id'];
            $opcion = $db->opcion[$opcion_id];
            if ($opcion['respuesta']==1) {
                $correctas++;
            }

           echo " id ".$opcion['id']." Opcion ".$opcion['opcion']." Correcta ".$opcion['respuesta']. "\n";
            //echo "por lo menos entra, ";

        }

        $promedio = $correctas/$numRespuestas;

        //echo "promedio ".$promedio;
        
        $resul = $db->resultado->insert(array(
            'promedio' => $promedio,
             'tiempo' => $tiempo,
             'usuario_id' => $id));

        echo json_encode(array(
            'id' => $resul['id'],
            'promedio' => $resul['promedio'],
             'tiempo' => $resul['tiempo'],
             'usuario_id' => $resul['usuario_id'])
            );

   
     } else {

         echo json_encode(array(
            "status" => false,
            "message" => "Usuario ID $id does not exist"
            ));
     }
    
});


$app->get("/resultado/", function () use ($app, $db) {

    $data = array();
    foreach ($db->resultado() as $r) {
       
       $user = $db->usuario[$r["usuario_id"]];
        
        $data[]  = array(
            "id" => $r["id"],
            "tiempo" => floatval($r["tiempo"]),
            "promedio" => floatval($r["promedio"]),
            "usuario_id" => $r["usuario_id"],
            "usuario" => $user['user']           
        );
    }
        
    if ($data) {
        # code...
        $app->response->headers->set('Content-Type', 'application/json');
        echo json_encode($data);
    }else{

        
    }
});

$app->get("/resultado/:id_usuario", function ($usuario_id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $resultado = $db->resultado()->where("usuario_id", $usuario_id);
    if ($data = $resultado->fetch()) {
        echo json_encode(array(
            "id" => $data["id"],
            "tiempo" => $data["tiempo"],            
            "promedio" => $data["promedio"],
            "usuario_id" => $data["usuario_id"]
            
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Respuesta con Usuario ID $usuario_id does not exist"
            ));
    }
});



$app->run();

?>