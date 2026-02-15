<?php 

namespace App\Controller\Api;
use \App\Model\Entity\Membros as EntityMembros;
use \App\Model\Db\Pagination;

class Membros extends Api{

	private static function getMembroItens($request,&$obPagination){
		$itens = [];

		//QUANTIDADE TOTAL DE REGISTROS
		$quantidadeTotal = EntityMembros::getMembros(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

		//PAGINA ATUAL
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;

		//INSTANCIA DE PAGINAÇÃO
		$obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);

		//RESULTADOS DA PAGINA
		$results = EntityMembros::getMembros(null,'id DESC', $obPagination->getLimit());

		//REDERIZA O ITEM
		while ($obMembro = $results->fetchObject(EntityMembros::class)) {
			$itens[] = [

				'id' => (int)$obMembro->id,
				'nome_completo' => $obMembro->nome_completo,
				'telefone' => $obMembro->telefone,
				'cidade_residencia' => $obMembro->cidade_residencia,
				'ministerio' => $obMembro->ministerio,
				'admin_pertencente' => $obMembro->admin_pertencente,
				'data_chegada' => $obMembro->data_chegada,
				'dias_estadia' => $obMembro->dias_estadia,
				'observacoes_medicas' => $obMembro->observacoes_medicas,
				'codigo_barras' => $obMembro->codigo_barras

			];
		}

		//RETORNA OS DEPOIMENTOS
		return $itens;
	}

	public static function buscaMembros($request){

		return [
			'hospedagens' => self::getMembroItens($request,$obPagination),
			'paginacao' => parent::getPagination($request,$obPagination)
		];
	}


	public static function buscaPorCodigoBarras($request,$codigo){
		
		if(!is_numeric($codigo)){
			throw new \Exception("O id '".$codigo."' não é válido", 400);
		}
		$obMembro = EntityMembros::getMembroByCode($codigo);
		
		if(!$obMembro instanceof EntityMembros){
			throw new \Exception("O registro ".$codigo." não foi encontrado", 404);
		}

		return [
			'id' => (int)$obMembro->id,
			'nome_completo' => $obMembro->nome_completo,
			'telefone' => $obMembro->telefone,
			'cidade_residencia' => $obMembro->cidade_residencia,
			'ministerio' => $obMembro->ministerio,
			'admin_pertencente' => $obMembro->admin_pertencente,
			'data_chegada' => $obMembro->data_chegada,
			'dias_estadia' => $obMembro->dias_estadia,
			'observacoes_medicas' => $obMembro->observacoes_medicas,
			'codigo_barras' => $obMembro->codigo_barras
		];

	}



	public static function buscaMembroPeloId($request,$id){
		
		if(!is_numeric($id)){
			throw new \Exception("O id '".$id."' não é válido", 400);
		}
		$obMembro = EntityMembros::getMembroById($id);
		
		if(!$obMembro instanceof EntityMembros){
			throw new \Exception("O registro ".$id." não foi encontrado", 404);
		}

		return [
			'id' => (int)$obMembro->id,
			'nome_completo' => $obMembro->nome_completo,
			'telefone' => $obMembro->telefone,
			'cidade_residencia' => $obMembro->cidade_residencia,
			'ministerio' => $obMembro->ministerio,
			'admin_pertencente' => $obMembro->admin_pertencente,
			'data_chegada' => $obMembro->data_chegada,
			'dias_estadia' => $obMembro->dias_estadia,
			'observacoes_medicas' => $obMembro->observacoes_medicas,
			'codigo_barras' => $obMembro->codigo_barras
		];

	}

	public static function cadNovoMembro($request){
		//POST VARS
		$postVars = $request->getPostVars();
		
		//VALIDA OS CAMPOSS OBRIGATORIOS
		if(!isset($postVars['nome_completo'])){
			throw new \Exception("As informações nome, telefone, cidade, administração pertencente e data de são informações obrigatórias",400);
		}
		if(!isset($postVars['telefone'])){
			throw new \Exception("As informações nome, telefone, cidade, administração pertencente e data de são informações obrigatórias",400);
		}
		if(!isset($postVars['cidade_residencia'])){
			throw new \Exception("As informações nome, telefone, cidade, administração pertencente e data de são informações obrigatórias",400);
		}
		if(!isset($postVars['admin_pertencente'])){
			throw new \Exception("As informações nome, telefone, cidade, administração pertencente e data de são informações obrigatórias",400);
		}
		if(!isset($postVars['data_chegada'])){
			throw new \Exception("As informações nome, telefone, cidade, administração pertencente e data de são informações obrigatórias",400);
		}

	// Instancia o objeto corretamente
$obMembro = new EntityMembros;

// Atribui os valores ao objeto correto ($obMembro)
// Usando FILTER_SANITIZE_SPECIAL_CHARS para compatibilidade com PHP 8.x
$obMembro->nome_completo       = filter_var($postVars['nome_completo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$obMembro->telefone            = filter_var($postVars['telefone'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$obMembro->cidade_residencia   = filter_var($postVars['cidade_residencia'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$obMembro->ministerio          = filter_var($postVars['ministerio'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$obMembro->admin_pertencente   = filter_var($postVars['admin_pertencente'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

// Datas: ideal validar o formato ou ao menos tratar como string simples
$obMembro->data_chegada        = filter_var($postVars['data_chegada'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

// Números: garantindo que seja um inteiro
$obMembro->dias_estadia        = filter_var($postVars['dias_estadia'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

$obMembro->observacoes_medicas = filter_var($postVars['observacoes_medicas'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$obMembro->codigo_barras       = filter_var($postVars['codigo_barras'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
// Chama o cadastro no objeto que recebeu os dados
$obMembro->cadastrar();

		//RETORNA OS DETALHES DO CADASTRADO
		return [
			'id' => (int)$obMembro->id,
			'nome_completo' => $obMembro->nome_completo,
			'telefone' => $obMembro->telefone,
			'cidade_residencia' => $obMembro->cidade_residencia,
			'ministerio' => $obMembro->ministerio,
			'admin_pertencente' => $obMembro->admin_pertencente,
			'data_chegada' => $obMembro->data_chegada,
			'dias_estadia' => $obMembro->dias_estadia,
			'observacoes_medicas' => $obMembro->observacoes_medicas,
			'codigo_barras' => $obMembro->codigo_barras
		];
	}

	public static function editMembro($request,$id){
		//POST VARS
		$postVars = $request->getPostVars();

		//VALIDA OS CAMPOSS OBRIGATORIOS
	//VALIDA OS CAMPOSS OBRIGATORIOS
		if(!isset($postVars['nome_completo'])){
			throw new \Exception("As informações nome, telefone, cidade, administração pertencente e data de são informações obrigatórias",400);
		}
		if(!isset($postVars['telefone'])){
			throw new \Exception("As informações nome, telefone, cidade, administração pertencente e data de são informações obrigatórias",400);
		}
		if(!isset($postVars['cidade_residencia'])){
			throw new \Exception("As informações nome, telefone, cidade, administração pertencente e data de são informações obrigatórias",400);
		}
		if(!isset($postVars['admin_pertencente'])){
			throw new \Exception("As informações nome, telefone, cidade, administração pertencente e data de são informações obrigatórias",400);
		}
		if(!isset($postVars['data_chegada'])){
			throw new \Exception("As informações nome, telefone, cidade, administração pertencente e data de são informações obrigatórias",400);
		}


		//BUSCA O REGISTRO
		$obMembro = EntityMembros::getMembroById($id);

		//VALIDA A INSTANCIA
		if(!$obMembro instanceof EntityMembros){
			throw new \Exception("O registro ".$id." não foi encontrada", 404);
		}

		//ATUALIZA O REGISTRO
		// Atribui os valores ao objeto correto ($obMembro)
// Usando FILTER_SANITIZE_SPECIAL_CHARS para compatibilidade com PHP 8.x
$obMembro->nome_completo       = filter_var($postVars['nome_completo'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$obMembro->telefone            = filter_var($postVars['telefone'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$obMembro->cidade_residencia   = filter_var($postVars['cidade_residencia'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$obMembro->ministerio          = filter_var($postVars['ministerio'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$obMembro->admin_pertencente   = filter_var($postVars['admin_pertencente'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

// Datas: ideal validar o formato ou ao menos tratar como string simples
$obMembro->data_chegada        = filter_var($postVars['data_chegada'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

// Números: garantindo que seja um inteiro
$obMembro->dias_estadia        = filter_var($postVars['dias_estadia'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

$obMembro->observacoes_medicas = filter_var($postVars['observacoes_medicas'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$obMembro->codigo_barras       = filter_var($postVars['codigo_barras'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obMembro->atualizar();

		//RETORNA OS DETALHES
		return [
			'id' => (int)$obMembro->id,
			'nome_completo' => $obMembro->nome_completo,
			'telefone' => $obMembro->telefone,
			'cidade_residencia' => $obMembro->cidade_residencia,
			'ministerio' => $obMembro->ministerio,
			'admin_pertencente' => $obMembro->admin_pertencente,
			'data_chegada' => $obMembro->data_chegada,
			'dias_estadia' => $obMembro->dias_estadia,
			'observacoes_medicas' => $obMembro->observacoes_medicas,
			'codigo_barras' => $obMembro->codigo_barras
		];
	}

	public static function deleteMembro($request,$id){

		//BUSCA O REGISTRO
		$obMembro = EntityMembros::getMembroById($id);

		//VALIDA A INSTANCIA
		if(!$obMembro instanceof EntityMembros){
			throw new \Exception("O registro ".$id." não foi encontrado", 404);
		}

		//EXCLUI O REGISTRO
		$obMembro->excluir();

		//RETORNA OS DETALHES DA TRILHA ATUALIZADA

		return [
			'sucesso' => true
		];
	}


}