<?php

require 'vendor/autoload.php';

use Mailgun\Mailgun;



/*# Instantiate the client.
$mgClient = new Mailgun('key-3ax6xnjp29jd6fds4gc373sgvjxteol0');
$domain = "samples.mailgun.org";

# Make the call to the client.
$result = $mgClient->sendMessage("$domain",
                  array('from'    => 'Excited User <me@samples.mailgun.org>',
                        'to'      => 'Baz <baz@example.com>',
                        'subject' => 'Hello',
                        'text'    => 'Testing some Mailgun awesomness!'));

*/

						

function generarCodigo($longitud) {
			 $key = '';
			 $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
			 $max = strlen($pattern)-1;
			 for($i=0;$i < $longitud;$i++) $key .= $pattern{mt_rand(0,$max)};
			 return $key;
			}
			 
/* 
 * parte de facebook para mostrar los datos
 *  
 require_once("libfb/confb.php");
 $config = array(
      'appId' => '600024680063899',
      'secret' => '7ba9ae72ed3137d9e91f714deb7dc6ab',
      'fileUpload' => false, // optional
      'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
  );

  $facebook = new Facebook($config);
  $facebook_id = $facebook->getUser();
  
 */
require "NotORM.php";
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

//devuelve la lista de preguntas....
$app->get('/pregunta/', function () use($app, $db){
	$data = array();
    foreach ($db->pregunta() as $e) {
        $data[]  = array(
            "id" => $e["id"],
            "pregunta" => $e["pregunta"]
            
        );
    }
	$app->response->headers->set('Content-Type', 'application/json');
	echo json_encode($data);
		
    
});

$app->post('/pregunta/', function () use($app, $db){
	
	$app->response->headers->set('Content-Type', 'application/json');
	$pregunta = $app->request()->post();
	$result = $db->pregunta->insert($pregunta);
	echo json_encode(array("id" => $result["id"]));

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


$app->put("/pregunta/:id", function ($id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $pregunta = $db->pregunta()->where("id", $id);
    if ($pregunta->fetch()) {
        $post = $app->request()->put();
        $result = $pregunta->update($post);
        echo json_encode(array(
            "status" => (bool)$result,
            "message" => "Pregunta updated successfully"
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Pregunta id $id does not exist"
        ));
    }
});

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
	$opcion = $app->request()->post();
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
            "email" => $e["email"]
            
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
            "email" => $data["email"]
            
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Usuario ID $id does not exist"
            ));
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

$app->post('/respuesta/', function () use($app, $db){
	
	$app->response->headers->set('Content-Type', 'application/json');
	$respuesta = $app->request()->post();
	$result = $db->respuesta->insert($respuesta);
	echo json_encode(array("id" => $result["id"]));

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
$app->post('/resultado/', function () use($app, $db){
	
	$app->response->headers->set('Content-Type', 'application/json');
	$resultado = $app->request()->post();
	$result = $db->resultado->insert($resultado);
	echo json_encode(array("id" => $result["id"]));

});

$app->get("/resultado/:id_usuario", function ($usuario_id) use ($app, $db) {
    $app->response()->header("Content-Type", "application/json");
    $resultado = $db->resultado()->where("usuario_id", $usuario_id);
    if ($data = $resultado->fetch()) {
        echo json_encode(array(
            "id" => $data["id"],
            "inicio" => $data["inicio"],
            "fin" => $data["fin"],
            "promedio" => $data["promedio"],
            "usuario_id" => $data["usuario_id"]
            
            ));
    }
    else{
        echo json_encode(array(
            "status" => false,
            "message" => "Respuesta con Usuario ID $id does not exist"
            ));
    }
});


$app->run();

?>