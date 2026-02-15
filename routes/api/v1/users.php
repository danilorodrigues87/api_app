<?php
use \App\Http\Response;
use \App\Controller\Api;

//ROTA DE AUTORIZAÇÃO DA API
$obRouter->post('/api/v1/login',[
	'middlewares' => [
		'api'
	],
	function($request){
		return new Response(201,Api\Users::logar($request),'application/json');
	}
]);


//ROTA DE CADASTRO VIA API
$obRouter->post('/api/v1/cadastro-usuario',[
	'middlewares' => [
		'api'
	],
	function($request){
		return new Response(201,Api\Users::cadNovoUsuario($request),'application/json');
	}
]);

//ROTA RECEBIMENTO VIA API
$obRouter->get('/api/v1/usuarios',[
	'middlewares' => [
		'api'
	],
	function($request){
		return new Response(200,Api\Users::buscaUsuarios($request),'application/json');
	}
]);

//ROTA RECEBIMENTO VIA API COM BASE NO ID
$obRouter->get('/api/v1/usuario/{id}',[
	'middlewares' => [
		'api'
	],
	function($request,$id){
		return new Response(200,Api\Users::buscaUsuarioPeloId($request,$id),'application/json');
	}
]);


//ROTA DE ATUALIZAÇÃO  VIA API
$obRouter->put('/api/v1/edit-usuario/{id}',[
	'middlewares' => [
		'api'
	],
	function($request,$id){
		return new Response(200,Api\Users::editUsuario($request,$id),'application/json');
	}
]);

//ROTA DE ATIVAÇÃO DESATIVAÇÃO DE USUÁRIO VIA API
$obRouter->put('/api/v1/ativacao-usuario/{id}',[
	'middlewares' => [
		'api'
	],
	function($request,$id){
		return new Response(200,Api\Users::ativacaoUsuario($request,$id),'application/json');
	}
]);