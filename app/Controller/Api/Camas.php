<?php 

namespace App\Controller\Api;
use \App\Model\Entity\Camas as EntityCamas;
use \App\Model\Entity\Setores;
use \App\Model\Db\Pagination;

class Camas extends Api{


	private static function getCamaById($request,&$obPagination){
		$itens = [];

		//QUANTIDADE TOTAL DE REGISTROS
		$quantidadeTotal = EntityCamas::getCamas(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

		//PAGINA ATUAL
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;

		//INSTANCIA DE PAGINAÇÃO
		$obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);

		//RESULTADOS DA PAGINA
		$results = EntityCamas::getCamas(null,'id ASC', $obPagination->getLimit());

		//REDERIZA O ITEM
		while ($obCama = $results->fetchObject(EntityCamas::class)) {
			$itens[] = [

				'id' => (int)$obCama->id,
				'numero_cama' => $obCama->numero_cama,
				'status_ocupacao' => $obCama->status_ocupacao

			];
		}

		//RETORNA OS DEPOIMENTOS
		return $itens;
	}

	public static function buscaCamas($request){

		return [
			'camas' => self::getCamaById($request,$obPagination),
			'setores' => self::setoresParaSelect()
		];
	}

	private static function setoresParaSelect(){

		$itens = [];


		//RESULTADOS DA PAGINA
		$results = Setores::getSetores(null,'nome_setor ASC',);

		//REDERIZA O ITEM
		while ($obCama = $results->fetchObject(Setores::class)) {
			$itens[] = [

				'id' => (int)$obCama->id,
				'nome_setor' => $obCama->nome_setor
			];
		}

		//RETORNA OS DEPOIMENTOS
		return $itens;


	}


	public static function buscaCamaPeloNumero($request,$numero_cama){
		

		$obCama = EntityCamas::getCamaByNumber($numero_cama);
		
		if(!$obCama instanceof EntityCamas){
			throw new \Exception("A cama de numero ".$numero_cama." não foi encontrada", 404);
		}

		return [
			'id' => (int)$obCama->id,
			'setor' => $obCama->setor,
			'numero_cama' => $obCama->numero_cama,
			'status_ocupacao' => $obCama->status_ocupacao
		];

	}


	public static function buscaCamaPeloId($request,$id){
		

		$obCama = EntityCamas::getCamaById($id);
		
		if(!$obCama instanceof EntityCamas){
			throw new \Exception("A cama de ID ".$id." não foi encontrada", 404);
		}

		return [
			'id' => (int)$obCama->id,
			'setor' => $obCama->setor,
			'numero_cama' => $obCama->numero_cama,
			'status_ocupacao' => $obCama->status_ocupacao
		];

	}

	public static function cadNovaCama($request){
		//POST VARS
		$postVars = $request->getPostVars();
		
		//VALIDA OS CAMPOSS OBRIGATORIOS
		if(!isset($postVars['setor'])){
			throw new \Exception("As informações setor, numero_cama e status_ocupacao são informações obrigatórias",400);
		}
		if(!isset($postVars['numero_cama'])){
			throw new \Exception("As informações setor, numero_cama e status_ocupacao são informações obrigatórias",400);
		}
		if(!isset($postVars['status_ocupacao'])){
			throw new \Exception("As informações setor, numero_cama e status_ocupacao são informações obrigatórias",400);
		}

		//BUSCA CAMA PELO numero_cama
		$obCama = EntityCamas::getCamaByNumber($postVars['numero_cama']);

		if($obCama instanceof EntityCamas){
			throw new \Exception("Esse numero de cama já existe e não pode ser repetido",400);
		}


		$obCama = new EntityCamas;
// Atribui os valores ao objeto correto

		$obCama->setor       = filter_var($postVars['setor'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obCama->numero_cama            = filter_var($postVars['numero_cama'] ?? '', FILTER_SANITIZE_NUMBER_INT);
		$obCama->status_ocupacao   = filter_var($postVars['status_ocupacao'] ?? '', FILTER_SANITIZE_NUMBER_INT);

		$obCama->cadastrar();

		//RETORNA OS DETALHES DO CADASTRADO
		return [
			'id' => (int)$obCama->id,
			'setor' => $obCama->setor,
			'numero_cama' => $obCama->numero_cama,
			'status_ocupacao' => $obCama->status_ocupacao
		];
	}

	public static function editCama($request,$id){
		//POST VARS
		$postVars = $request->getPostVars();


	    //VALIDA OS CAMPOSS OBRIGATORIOS
		if(!isset($postVars['setor'])){
			throw new \Exception("As informações setor, numero_cama e status_ocupacao são informações obrigatórias",400);
		}
		if(!isset($postVars['numero_cama'])){
			throw new \Exception("As informações setor, numero_cama e status_ocupacao são informações obrigatórias",400);
		}
		if(!isset($postVars['status_ocupacao'])){
			throw new \Exception("As informações setor, numero_cama e status_ocupacao são informações obrigatórias",400);
		}

		//BUSCA O REGISTRO
		$obCama = EntityCamas::getCamaById($id);

		//VALIDA A INSTANCIA
		if(!$obCama instanceof EntityCamas){
			throw new \Exception("Registro não encontrado", 404);
		}

		//ATUALIZA O REGISTRO
		$obCama->setor       = filter_var($postVars['setor'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obCama->numero_cama            = filter_var($postVars['numero_cama'] ?? '', FILTER_SANITIZE_NUMBER_INT);
		$obCama->status_ocupacao   = filter_var($postVars['status_ocupacao'] ?? '', FILTER_SANITIZE_NUMBER_INT);

		$obCama->atualizar();

		//RETORNA OS DETALHES
		return [
			'id' => (int)$obCama->id,
			'setor' => $obCama->setor,
			'numero_cama' => $obCama->numero_cama,
			'status_ocupacao' => $obCama->status_ocupacao
		];
	}

	public static function deleteCama($request,$id){

		//BUSCA O REGISTRO
		$obCama = EntityCamas::getCamaById($id);

		//VALIDA A INSTANCIA
		if(!$obCama instanceof EntityCamas){
			throw new \Exception("O registro ".$id." não foi encontrado", 404);
		}

		//EXCLUI O REGISTRO
		$obCama->excluir();

		//RETORNA OS DETALHES DA ATUALIZAÇÃO ATUALIZADA

		return [
			'sucesso' => true
		];
	}




}