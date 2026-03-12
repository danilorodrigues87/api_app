<?php 

namespace App\Http\Middleware;

class Api{

	//EXECUTA O MIDDLEWARE
	public function handle($request, $next){

		//ALTERA O CONTENT TYPE PARA JSON
		$request->getRouter()->setContentType('application/json');

		$checkKey = AppAuth::checkAppKey($request);

		if(!$checkKey){
			throw new \Exception("Autorização negada.", 401); 
		}

		//EXECUTA O PROXIMO NIVEL DO MIDDLEWARE
		return $next($request);
	}

}