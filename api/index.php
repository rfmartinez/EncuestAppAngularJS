<?php

session_start();

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
$user="deelepah_nuser";
$pass="+UVpbqt{ZA.s";
 
$pdo = new PDO("mysql:dbname=deelepah_ndb;host=127.0.0.1", $user, $pass);
$db = new NotORM($pdo);


$app = new \Slim\Slim();

$app->config(array(
    'debug' => true,
    'templates.path' => 'templates'
));

$app->get('/', function () use ($app, $db) {
	
	$post = $db->post();
    
    $data['post'] = $post->select()->where("estado", 2);
	$data['imagen'] = $db->media();
	
		
    $app->render('principal.php', $data);

});

$app->get('/theme', function () use ($app) {
    
    

    $app->render('theme.php');

});
$app->get('/ingresar', function ( )use ($app){
	$app->render('ingresar.php');
}
		
);

$app->get('/salir', function () use ($app){
	session_destroy();
	$app->redirect('/');
});

$app->post('/ingresar', function () use ($app, $db) {
    
	$request =$app->request;
	$user = $request->post('user');
	$password = $request->post('password');
	
	
 	$hasher = new PasswordHash(8, FALSE);
		
	if (empty($email) and empty($user)) {

		$app->flash('llenar','llenar todos los campos');
		
				
				
		} elseif($rows= $db->usuarios()->select("user, pass, id")->where("user", $user)->fetch()) {
						
					
						
						$hash=$rows['pass'];
						
			
					if ($hasher->CheckPassword($password, $hash)){
								$_SESSION['username']=$user;
								$_SESSION['id']=$rows["id"];					
						$app->redirect('/');
					}else{
							$app->flash('error','credenciales invalidas');
							$app->redirect('ingresar');
						
					}	
			
			
			
			
		}else{
			

			$app->flash('error','se produjo un error');
							$app->redirect('ingresar');
		}
	
   

});


$app->get('/resetPass', function () use ($app){
	
			$app->render('resetpass.php');
			
});

$app->post('/resetPass', function () use ($app, $db){
	
	$request =$app->request;
	$email = $request->post('email');
	
	
	if(empty($email)){
		
		$app->flash('info','llenar el campo');
		
	}elseif($user= $db->usuarios()->select()->where("email", $email)->fetch()){
		
				 
			$t_hasher = new PasswordHash(8, FALSE);
			$nuevapass = generarCodigo(4);
			$hash = $t_hasher->HashPassword($nuevapass);
			
			echo $hash;
			if($insertado = $user->update(array('pass' => $hash))){
				echo "<br/>";	
				echo $nuevapass;
				
				$mgClient = new Mailgun('key-9mfpl01wmc4d-18f8bw992ph-j3twli1');
				$domain = "sandbox9653.mailgun.org";

				# Make the call to the client.
				$result = $mgClient->sendMessage("$domain",
				                  array('from'    => 'Querido Usuario <rafael.antonio.martinezs@gmail.com>',
				                        'to'      => $email,
				                        'subject' => 'Contraseña nueva',
				                        'text'    => 'tu nueva pass! '.$nuevapass.' '));
						
				$app->redirect('ingresar');
				exit();
			}else{
				
				echo "no inserto";
			}
			
			
			
	}else{
		
		echo "no hay resultados";
	}
			//$app->render('resetpass.php');
			
			

});


$app->get('/registrar', function () use ($app) {
    
    $app->render('registrar-usuario.php');

});

$app->get('/registrarfb',function () use ($app){
	
	$app->render('registrarfb.php');
	
	
});


$app->post('/registrar', function () use ($app, $db){

	$request =$app->request;
	$username = $request->post('user');
	$email = $request->post('email');
	$password = $request->post('password');
	$t_hasher = new PasswordHash(8, FALSE);
	$hash = $t_hasher->HashPassword($password);
	
	

	
	

	if (empty($email) and empty($password) and empty($username)) {

		$app->flash('llenar','llenar todos los campos');
		
		
	}else{

			if ($user= $db->usuarios()->select()->where("user", $username)->fetch()){
				
				$app->flash('error', 'ya existe un usuario con ese nombre! porfavor introduzca otro');
				
				$app->redirect('registrar');
					exit();
				
			} elseif($user= $db->usuarios()->select()->where("email", $email)->fetch()) {
				
				$app->flash('error', 'ya existe ese correo! porfavor introduzca otro');
				$app->redirect('registrar');
				exit();
				
			}else{
				
				if ($insertado = $db->usuarios()->insert(array('user' => $username, 'pass' => $hash, 'email' => $email))) {
					
					$app->flash('error', 'se agrego correctamente');
						$app->redirect('ingresar');
						exit();
					
					
				} else {
					$app->flash('error','se produjo un error');
					$app->redirect('registrar');
					exit();
				}
				
			}
			
			
		

			
		}

	

	});

$app->get('/publicar', function () use ($app){
	
	$app->render('publicar.php');

});

$app->post('/publicar', function () use ($app, $db){
	
	$id=$_SESSION['id'];
	$request = $app->request;
	$titulo = $request->post('titulo');
	$desc = $request->post('descripcion');
	$img = $_FILES['upimagen']['name'];
	$tmp_name = $_FILES['upimagen']['tmp_name'];
	$destino = 'images/'.$id.$img;
	$videoId = $request->post('yurl');
	$tipoMedia ="deafult";
	
	if(empty($img)){
		
		$tipoMedia = "video";
		
	}else{
		
		$tipoMedia = "imagen";
	}
	
	
	
	$tipo = $_FILES['upimagen']['type'];
	
	
	/*
	echo "Titulo ".$titulo." desc ".$desc." img  ".$img." videoId  ".$videoId." tipo de media ".$tipoMedia."    id     ".$id;
	*/
	
		
	if ($tipoMedia==="video") {
		
			if ($insertadoMedia = $db->media()->insert(array('url' => $videoId,'tipo' => $tipoMedia))) {
				
							
				$lastInsertId = intval($insertadoMedia['id']);
				
				
				
					if ($insertado = $db->post()->insert(array('titulo' => $titulo, 'descripcion' => $desc,'estado' => 2, 'media_id'=>$lastInsertId, 'usuarios_id' => $id ))) {
						
						$app->redirect('publicar');
					} else {
						
						echo "hubo un error al insertar el post";
						
					}
				
				
			} else {
				
				echo "hubo un error al insertar";
			}
		
		
	} else {
		
				if (!(strpos($tipo, "png") || strpos($tipo, "jpeg") || strpos($tipo, "jpg") ) ){
						
						$app->flash('tipo','solo se aceptan extenciones png, jpeg');
						$app->redirect('publicar');
						
				}else{
									
						
								
								
							
								
					
							if ($insertadoMedia = $db->media()->insert(array('url' => $destino,'tipo' => $tipoMedia))) {
									
									move_uploaded_file($tmp_name, $destino);
									
									$lastInsertId = intval($insertadoMedia['id']);
									
											if ($insertado = $db->post()->insert(array('titulo' => $titulo, 'descripcion' => $desc,'estado' => 2, 'media_id'=>$lastInsertId, 'usuarios_id' => $id ))) {
											
												$app->flash('tipo','se registro correctamente');
												
												$app->redirect('publicar');
													
											} else {
												$app->flash('tipo','no se registro');
												
											}
									
								
							}else{
								
									$app->flash('tipo','error al insertar media');
								
							}
						
				}	
						
				
			}	
							
					
					
					
				
		
				
	
	
	
		
		
		
		
		
		
});

$app->get("/post/:id", function ($id) use ($app, $db){
		
	$post = $db->post()->where("id", $id);
	
	
	
	if($data['post'] = $post->fetch()){
				
				
				
				$mediaId=intval($data['post']['media_id']);
		
			$data['related']=$db->post()->select()->where("estado", 2)->limit(3);
			
			if($data['media']=$db->media()->select()->where("id",$mediaId)->fetch()){
				
				$app->render('post.php', $data);
		
			}else{
				
				echo "no se encontro ningun resultado para media";
			}
		
		}else{
			
			echo "el post no existe";
		}
	 
	
	});
	

//zona de perfil de usuario

$app->get("/perfil", function () use ($app, $db){
	
	
	
	$post=$db->post()->where("usuarios_id", $_SESSION['id']);
	
	
	
	if (empty($_SESSION['id'])) {
		
		 $app->redirect('ingresar');
		
	} else {
		
			$usuario = $db->usuarios()->where("id", $_SESSION['id']);
			
			if ($result['user']=$usuario->fetch()) {
				
					$post=$db->post()->where("usuarios_id", $_SESSION['id']);
						
						$result['postme']=$post;
						
			
					
				$app->render('perfil.php', $result);	
				
			} else {
				
				$resul['error']= "no se ha encontrado el usuario";
				
				$app->render('perfil.php', $result);
			}
				
			}
	

});



$app->get("/videoValidator/:id", function ($id) use ($app){
	
	
	$ids = $id;
	
	if(strlen($ids)=='11'){//si es igual a 11 seguimos
		$url='http://i2.ytimg.com/vi/'.$ids.'/default.jpg';//pasamos el id a url
			if($conex= @fopen($url,"rt")) {$siono=1;}//si existe hacemos true
			else{$siono=3;}//si no, false
	}
	else{$siono=2;}//si es mayor que 11 false
		///VERIFICAR ID YOUTUBE///
		//$app->response->headers->set("Content-Type", "application/json");
		//echo json_encode($siono);
	
	echo $siono;
});
$app->post("/videoValidator", function () use ($app){
	
	

							
	});
$app->run();

?>