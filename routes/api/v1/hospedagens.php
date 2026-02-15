<?php
use \App\Http\Response;
use \App\Controller\Api;

//ROTA RECEBIMENTO VIA API
$obRouter->get('/api/v1/hospedagens',[
	'middlewares' => [
		'api',
		'cache'
	],
	function($request){
		return new Response(200,Api\Hospedagens::getHospedagens($request),'application/json');
	}
]);

//ROTA DE CADASTRO VIA API
$obRouter->post('/api/v1/cadastro-hospedagem',[
	'middlewares' => [
		'api'
	],
	function($request){
		return new Response(201,Api\Hospedagens::cadNovaHospedagem($request),'application/json');
	}
]);

//ROTA RECEBIMENTO VIA API COM BASE NO ID
$obRouter->get('/api/v1/hospedagem/{id}',[
	'middlewares' => [
		'api',
		'cache'
	],
	function($request,$id){
		return new Response(200,Api\Hospedagens::getHospedagemPeloId($request,$id),'application/json');
	}
]);


//ROTA DE ATUALIZAÇÃO DIA API
$obRouter->put('/api/v1/edit-hospedagem/{id}',[
	'middlewares' => [
		'api',
	],
	function($request,$id){
		return new Response(200,Api\Hospedagens::setEditHospedagem($request,$id),'application/json');
	}
]);

//ROTA DE EXCLUSÃO DE DEPOIMENTOS VIA API
$obRouter->delete('/api/v1/delete-hospedagem/{id}',[
	'middlewares' => [
		'api'
	],
	function($request,$id){
		return new Response(200,Api\Hospedagens::seteDeleteHospedagem($request,$id),'application/json');
	}
]);


//ROTA DADOS DO DASHBOARD
$obRouter->get('/api/v1/dashboard',[
	'middlewares' => [
		'api',
	],
	function($request){
		return new Response(200,Api\Dashboard::dashboardData($request),'application/json');
	}
]);