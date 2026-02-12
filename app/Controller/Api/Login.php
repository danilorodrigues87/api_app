<?php 
namespace App\Controller\Api;
use \App\Model\Entity\User;

class Login extends Api{

	//RESPOMSÁVEL POR GERAR UM TOKEN JWT
	public static function logar($request){
		$postVars = $request->getPostVars();

		//VALIDA OS CAMPOS OBRIGÁTORIOS
		if(!isset($postVars['email']) or !isset($postVars['senha'])){
			throw new \Exception("Os campos 'email' e 'senha' são obrigátorios", 400);
		}

		//VERIFICA O USUÁRIO POR EMAIL
		$obUser = User::getUserByEmail($postVars['email']);
		if(!$obUser instanceof User or !password_verify($postVars['senha'], $obUser->senha)){
			throw new \Exception("O usuário ou senha são inválidoss", 400);
		}

		//RETORNA O TOKEN GERADO
		return [
			'nome' => $obUser->nome,
			'email' => $obUser->email,
			'cargo' => $obUser->cargo
		];
	}
}