<?php 

namespace App\Controller\Api;
use \App\Model\Entity\User;
use \App\Model\Db\Pagination;

class Users extends Api{

	//RESPOMSÁVEL POR GERAR UM TOKEN JWT
	public static function logar($request){
		$postVars = $request->getPostVars();

		//VALIDA OS CAMPOS OBRIGÁTORIOS
		if(!isset($postVars['email']) or !isset($postVars['senha'])){
			throw new \Exception("Os campos 'email' e 'senha' são obrigátorios", 400);
		}

		//VERIFICA O USUÁRIO POR EMAIL
		$obUser = User::getUserByEmail($postVars['email']);

		if(!$obUser->ativacao){
			throw new \Exception("O usuário não altorizado", 400);
		}
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


	private static function getUserItens($request,&$obPagination){
		$itens = [];

		//QUANTIDADE TOTAL DE REGISTROS
		$quantidadeTotal = User::getUser(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

		//PAGINA ATUAL
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;

		//INSTANCIA DE PAGINAÇÃO
		$obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);

		//RESULTADOS DA PAGINA
		$results = User::getUser(null,'id DESC', $obPagination->getLimit());

		//REDERIZA O ITEM
		while ($obUser = $results->fetchObject(User::class)) {
			$itens[] = [

				'id' => (int)$obUser->id,
				'nome' => $obUser->nome,
				'email' => $obUser->email,
				'cargo' => $obUser->cargo

			];
		}

		//RETORNA OS DEPOIMENTOS
		return $itens;
	}

	public static function buscaUsuarios($request){

		return [
			'operadores' => self::getUserItens($request,$obPagination),
			'paginacao' => parent::getPagination($request,$obPagination)
		];
	}




	public static function buscaUsuarioPeloId($request,$id){
		
		if(!is_numeric($id)){
			throw new \Exception("O id '".$id."' não é válido", 400);
		}
		$obUser = User::getUserById($id);
		
		if(!$obUser instanceof User){
			throw new \Exception("O registro ".$id." não foi encontrado", 404);
		}

		return [
			'id' => (int)$obUser->id,
			'nome' => $obUser->nome,
			'email' => $obUser->email,
			'cargo' => $obUser->cargo
		];

	}

	public static function cadNovoUsuario($request){
		//POST VARS
		$postVars = $request->getPostVars();
		
		//VALIDA OS CAMPOSS OBRIGATORIOS
		if(!isset($postVars['nome'])){
			throw new \Exception("As informações nome, email, cargo e senha são informações obrigatórias",400);
		}
		if(!isset($postVars['email'])){
			throw new \Exception("As informações nome, email, cargo e senha são informações obrigatórias",400);
		}
		if(!isset($postVars['cargo'])){
			throw new \Exception("As informações nome, email, cargo e senha são informações obrigatórias",400);
		}
		if(!isset($postVars['senha1'])){
			throw new \Exception("As informações nome, email, cargo e senha são informações obrigatórias",400);
		}

		if(!isset($postVars['senha2'])){
			throw new \Exception("As informações nome, email, cargo e senha são informações obrigatórias",400);
		}

		if($postVars['senha1'] != $postVars['senha2']){
			throw new \Exception("As senhas não coincidem.",400);
		}

		//BUSCA O USUÁRIO PELO EMAIL
		$obUser = User::getUserByEmail($postVars['email']);

		if($obUser instanceof User){
			throw new \Exception("Esse email já existe",400);
		}


// Instancia o objeto corretamente
		$obUser = new User;
// Atribui os valores ao objeto correto

		$obUser->nome       = filter_var($postVars['nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obUser->email            = filter_var($postVars['email'] ?? '', FILTER_SANITIZE_EMAIL);
		$obUser->cargo   = filter_var($postVars['cargo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obUser->senha          = password_hash($postVars['senha1'], PASSWORD_DEFAULT);

		$obUser->cadastrar();

		//RETORNA OS DETALHES DO CADASTRADO
		return [
			'id' => (int)$obUser->id,
			'nome' => $obUser->nome,
			'email' => $obUser->email,
			'cargo' => $obUser->cargo
		];
	}

	public static function editUsuario($request,$id){
		//POST VARS
		$postVars = $request->getPostVars();

		//VALIDA OS CAMPOSS OBRIGATORIOS
		if(!isset($postVars['nome'])){
			throw new \Exception("As informações nome, email, cargo e senha são informações obrigatórias",400);
		}
		if(!isset($postVars['email'])){
			throw new \Exception("As informações nome, email, cargo e senha são informações obrigatórias",400);
		}
		if(!isset($postVars['cargo'])){
			throw new \Exception("As informações nome, email, cargo e senha são informações obrigatórias",400);
		}


		//BUSCA O REGISTRO
		$obUser = User::getUserById($id);

		//VALIDA A INSTANCIA
		if(!$obUser instanceof User){
			throw new \Exception("O registro ".$id." não foi encontrada", 404);
		}

		//ATUALIZA O REGISTRO
		$obUser->nome       = filter_var($postVars['nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obUser->email            = filter_var($postVars['email'] ?? '', FILTER_SANITIZE_EMAIL);
		$obUser->cargo   = filter_var($postVars['cargo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

		$obUser->atualizar();

		//RETORNA OS DETALHES
		return [
			'id' => (int)$obUser->id,
			'nome' => $obUser->nome,
			'email' => $obUser->email,
			'cargo' => $obUser->cargo
		];
	}

	public static function ativacaoUsuario($request,$id){

		//BUSCA O REGISTRO
		$obUser = User::getUserById($id);

		//VALIDA A INSTANCIA
		if(!$obUser instanceof User){
			throw new \Exception("O registro ".$id." não foi encontrado", 404);
		}
		if($obUser->ativacao){
			$obUser->ativacao = false;
		} else {
			$obUser->ativacao = true;
		}

		//EXCLUI O REGISTRO
		$obUser->ativacao();

		//RETORNA OS DETALHES DA ATUALIZAÇÃO ATUALIZADA

		return [
			'sucesso' => $obUser->ativacao
		];
	}




}