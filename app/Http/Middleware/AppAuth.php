<?php 
namespace App\Http\Middleware;

class AppAuth{


	public static function checkAppKey($request){

		// 1. Obtém os headers
		$headers = $request->getHeaders();

		// 2. Verifica se a chave existe e não está vazia
		if (empty($headers['X-App-Source-Secret-Key'])) {
			return false;
		}

		// 3. Extrai o valor (ajuste se o framework retornar array, ex: $headers['X-App-Source-Secret-Key'][0])
		$headerSecretKey = $headers['X-App-Source-Secret-Key'];

		// 4. Comparação segura
		// Se não forem iguais, lança a exceção
		if ($headerSecretKey !== APP_KEY) {
			return false;
		}


		return true;
	}
	

}