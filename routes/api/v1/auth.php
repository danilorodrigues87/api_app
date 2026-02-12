<?php
use \App\Http\Response;
use \App\Controller\Api;

//ROTA DE AUTORIZAÇÃO DA API
$obRouter->post('/api/v1/login',[
	'middlewares' => [
		'api'
	],
	function($request){
		return new Response(201,Api\Login::logar($request),'application/json');
	}
]);
