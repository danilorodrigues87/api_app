<?php

use \App\Http\Response;
use \App\Controller\Site;


//ROTA SOBRE
$obRouter->get('/sobre',[
	'middlewares' => [
		
	],
	function($request){
		return new Response(200,Site\About::index($request));
	}
]);