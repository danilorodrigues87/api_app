<?php
use \App\Http\Response;
use \App\Controller\Api;

$obRouter->get('/health',[
	function($request){
		return new Response(200,[
			'status' => 'ok',
			'app' => 'api-app'
		],'application/json');
	}
]);

//ROTA RECEBIMENTO DE MENSAGEM VIA API
$obRouter->get('/api/v1',[
	'middlewares' => [
		'api'
	],
	function($request){
		return new Response(200,Api\Api::getDetails($request),'application/json');
	}
]);

