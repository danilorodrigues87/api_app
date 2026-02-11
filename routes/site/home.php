<?php

use \App\Http\Response;
use \App\Controller\Site;

//ROTA ADMIN
$obRouter->get('/',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(200,Site\Home::index($request));
	}
]);


//ROTA ADMIN
$obRouter->get('/sobre',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(200,Site\About::index($request));
	}
]);