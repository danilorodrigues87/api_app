<?php
use \App\Http\Response;
use \App\Controller\Api;

//ROTA RECEBIMENTO VIA API
$obRouter->get('/api/v1/membros',[
	'middlewares' => [
		'api'
	],
	function($request){
		return new Response(200,Api\Membros::buscaMembros($request),'application/json');
	}
]);


//ROTA DE CADASTRO VIA API
$obRouter->post('/api/v1/cadastro-membro',[
	'middlewares' => [
		'api'
	],
	function($request){
		return new Response(201,Api\Membros::cadNovoMembro($request),'application/json');
	}
]);

//ROTA RECEBIMENTO VIA API COM BASE NO ID
$obRouter->get('/api/v1/membro/{id}',[
	'middlewares' => [
		'api'
	],
	function($request,$id){
		return new Response(200,Api\Membros::buscaMembroPeloId($request,$id),'application/json');
	}
]);

//ROTA RECEBIMENTO VIA API COM BASE NO ID
$obRouter->get('/api/v1/membro-codigo/{code}',[
	'middlewares' => [
		'api'
	],
	function($request,$code){
		return new Response(200,Api\Membros::buscaPorCodigoBarras($request,$code),'application/json');
	}
]);


//ROTA DE ATUALIZAÇÃO DE DEPOIMENTOS VIA API
$obRouter->put('/api/v1/edit-membro/{id}',[
	'middlewares' => [
		'api'
	],
	function($request,$id){
		return new Response(200,Api\Membros::editMembro($request,$id),'application/json');
	}
]);

//ROTA DE EXCLUSÃO DE DEPOIMENTOS VIA API
$obRouter->delete('/api/v1/delete-membro/{id}',[
	'middlewares' => [
		'api'
	],
	function($request,$id){
		return new Response(200,Api\Membros::deleteMembro($request,$id),'application/json');
	}
]);