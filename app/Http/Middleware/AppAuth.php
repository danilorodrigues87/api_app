<?php 
namespace App\Http\Middleware;

class AppAuth{


	public static function checkAppKey($request){

		// 1. Obtém os headers
		$headers = $request->getHeaders();
    
		// 2. Verifica se a chave existe e não está vazia
		if (empty($headers['x-app-source-secret-key'])) {
			return false;
		}

		// 3. Extrai o valor (ajuste se o framework retornar array, ex: $headers['x-app-source-secret-key'][0])
		$headerSecretKey = $headers['x-app-source-secret-key'];

		// 4. Comparação segura
		// Se não forem iguais, lança a exceção
		if ($headerSecretKey !== APP_KEY) {
			return false;
		}


		return true;
	}
	

}