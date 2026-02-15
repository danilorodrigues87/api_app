<?php
use \App\Http\Response;
use \App\Controller\Api;

//ROTA RECEBIMENTO VIA API
$obRouter->get('/api/v1/camas',[
	'middlewares' => [
		'api'
	],
	function($request){
		return new Response(200,Api\Camas::buscaCamas($request),'application/json');
	}
]);


//ROTA DE CADASTRO VIA API
$obRouter->post('/api/v1/cadastro-cama',[
	'middlewares' => [
		'api'
	],
	function($request){
		return new Response(201,Api\Camas::cadNovaCama($request),'application/json');
	}
]);

//ROTA RECEBIMENTO VIA API COM BASE NO ID
$obRouter->get('/api/v1/cama/{id}',[
	'middlewares' => [
		'api'
	],
	function($request,$id){
		return new Response(200,Api\Camas::buscaCamaPeloId($request,$id),'application/json');
	}
]);

//ROTA RECEBIMENTO VIA API COM BASE NO NUMERO
$obRouter->get('/api/v1/cama-numero/{num}',[
	'middlewares' => [
		'api'
	],
	function($request,$num){
		return new Response(200,Api\Camas::buscaCamaPeloNumero($request,$num),'application/json');
	}
]);


//ROTA DE ATUALIZAÇÃO VIA API
$obRouter->put('/api/v1/edit-cama/{id}',[
	'middlewares' => [
		'api'
	],
	function($request,$id){
		return new Response(200,Api\Camas::editCama($request,$id),'application/json');
	}
]);

//ROTA DE EXCLUSÃO DE DEPOIMENTOS VIA API
$obRouter->delete('/api/v1/delete-cama/{id}',[
	'middlewares' => [
		'api'
	],
	function($request,$id){
		return new Response(200,Api\Camas::deleteCama($request,$id),'application/json');
	}
]);