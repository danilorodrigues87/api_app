<?php 

namespace App\Controller\Api;
use \App\Model\Entity\Hospedagens as EntityHosp;
use \App\Model\Entity\Camas;
use \App\Model\Db\Pagination;

class Hospedagens extends Api{

	private static function getHospedagemItens($request,&$obPagination){
		$itens = [];

		//QUANTIDADE TOTAL DE REGISTROS
		$quantidadeTotal = EntityHosp::getHospedagens(null,null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

		//PAGINA ATUAL
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;

		//INSTANCIA DE PAGINAÇÃO
		$obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);

		//RESULTADOS DA PAGINA
		$results = EntityHosp::getHospedagens(null,'id DESC', $obPagination->getLimit());

		//REDERIZA O ITEM
		while ($obHosp = $results->fetchObject(EntityHosp::class)) {
			$itens[] = [

			'id' => (int)$obHosp->id,
			'membro_id' => (int)$obHosp->membro_id,
			'operador_id' => (int)$obHosp->operador_id,
			'tipo_local' => $obHosp->tipo_local,
			'cama_id' => (int)$obHosp->cama_id,
			'dias_estadia' => (int)$obHosp->dias_estadia,
			'anfitriao_nome' => $obHosp->anfitriao_nome,
			'anfitriao_telefone' => $obHosp->anfitriao_telefone,
			'anfitriao_endereco' => $obHosp->anfitriao_endereco,
			'checkin_data' => $obHosp->checkin_data,
			'checkout_data' => $obHosp->checkout_data,
			'status' => $obHosp->status
			];
		}

		//RETORNA OS DEPOIMENTOS
		return $itens;
	}

	public static function getHospedagens($request){

		return [
			'hospedagens' => self::getHospedagemItens($request,$obPagination),
			'paginacao' => parent::getPagination($request,$obPagination)
		];
	}

	public static function getHospedagemPeloId($request,$id){
		
		if(!is_numeric($id)){
			throw new \Exception("O id '".$id."' não é válido", 400);
		}
		$obHosp = EntityHosp::getHospedagemById($id);
		
		if(!$obHosp instanceof EntityHosp){
			throw new \Exception("O registro ".$id." não foi encontrado", 404);
		}

		return [
			'id' => (int)$obHosp->id,
			'membro_id' => (int)$obHosp->membro_id,
			'operador_id' => (int)$obHosp->operador_id,
			'tipo_local' => $obHosp->tipo_local,
			'cama_id' => (int)$obHosp->cama_id,
			'dias_estadia' => (int)$obHosp->dias_estadia,
			'anfitriao_nome' => $obHosp->anfitriao_nome,
			'anfitriao_telefone' => $obHosp->anfitriao_telefone,
			'anfitriao_endereco' => $obHosp->anfitriao_endereco,
			'checkin_data' => $obHosp->checkin_data,
			'checkout_data' => $obHosp->checkout_data,
			'status' => $obHosp->status
		];

	}

	public static function cadNovaHospedagem($request){
		//POST VARS
		$postVars = $request->getPostVars();
		
		//VALIDA OS CAMPOSS OBRIGATORIOS
		if(!isset($postVars['membro_id']) 
			or !isset($postVars['operador_id']) 
			or !isset($postVars['tipo_local'])
		){
			throw new \Exception("As informações de Membro, operador, dias estadia e tipo de local são obrigatórios",400);
		}

		//NOVO DEPOIMENTO
		$obHosp = new EntityHosp;
		$obHosp->checkin_data = $postVars['checkin_data'];
		$obHosp->checkout_data = $postVars['checkout_data'];
		$obHosp->status = filter_var($postVars['status'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obHosp->membro_id = filter_var($postVars['membro_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
		$obHosp->operador_id = filter_var($postVars['operador_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
		$obHosp->tipo_local = filter_var($postVars['tipo_local'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obHosp->cama_id = filter_var($postVars['cama_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
		$obHosp->dias_estadia = filter_var($postVars['dias_estadia'] ?? '', FILTER_SANITIZE_NUMBER_INT);
		$obHosp->anfitriao_nome = filter_var($postVars['anfitriao_nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obHosp->anfitriao_telefone = filter_var($postVars['anfitriao_telefone'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obHosp->anfitriao_endereco = filter_var($postVars['anfitriao_endereco'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obHosp->cadastrar();

		//RETORNA OS DETALHES DO DEPOIMENTO CADASTRADO

		return [
			'id' => (int)$obHosp->id,
			'membro_id' => (int)$obHosp->membro_id,
			'operador_id' => (int)$obHosp->operador_id,
			'tipo_local' => $obHosp->tipo_local,
			'cama_id' => (int)$obHosp->cama_id,
			'dias_estadia' => (int)$obHosp->dias_estadia,
			'anfitriao_nome' => $obHosp->anfitriao_nome,
			'anfitriao_telefone' => $obHosp->anfitriao_telefone,
			'anfitriao_endereco' => $obHosp->anfitriao_endereco,
			'checkin_data' => $obHosp->checkin_data,
			'checkout_data' => $obHosp->checkout_data,
			'status' => $obHosp->status
		];
	}

	public static function editHospedagem($request,$id){
		//POST VARS
		$postVars = $request->getPostVars();
		
		//VALIDA OS CAMPOSS OBRIGATORIOS
		if(!isset($postVars['membro_id']) 
			or !isset($postVars['operador_id']) 
			or !isset($postVars['tipo_local'])
		){
			throw new \Exception("As informações de Membro, operador, dias estadia  e tipo de local são obrigatórios",400);
		}


		//BUSCA O REGISTRO
		$obHosp = EntityHosp::getHospedagemById($id);

		//VALIDA A INSTANCIA
		if(!$obHosp instanceof EntityHosp){
			throw new \Exception("O registro ".$id." não foi encontrada", 404);
		}

		//ATUALIZA O REGISTRO
		$obHosp = new EntityHosp;
		$obHosp->checkin_data = $postVars['checkin_data'];
		$obHosp->checkout_data = $postVars['checkout_data'];
		$obHosp->status = filter_var($postVars['status'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obHosp->membro_id = filter_var($postVars['membro_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
		$obHosp->operador_id = filter_var($postVars['operador_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
		$obHosp->tipo_local = filter_var($postVars['tipo_local'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obHosp->cama_id = filter_var($postVars['cama_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
		$obHosp->dias_estadia = filter_var($postVars['dias_estadia'] ?? '', FILTER_SANITIZE_NUMBER_INT);
		$obHosp->anfitriao_nome = filter_var($postVars['anfitriao_nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obHosp->anfitriao_telefone = filter_var($postVars['anfitriao_telefone'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obHosp->anfitriao_endereco = filter_var($postVars['anfitriao_endereco'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obTrilhas->atualizar();

		//RETORNA OS DETALHES
		return [
			'id' => (int)$obHosp->id,
			'membro_id' => (int)$obHosp->membro_id,
			'operador_id' => (int)$obHosp->operador_id,
			'tipo_local' => $obHosp->tipo_local,
			'cama_id' => (int)$obHosp->cama_id,
			'dias_estadia' => (int)$obHosp->dias_estadia,
			'anfitriao_nome' => $obHosp->anfitriao_nome,
			'anfitriao_telefone' => $obHosp->anfitriao_telefone,
			'anfitriao_endereco' => $obHosp->anfitriao_endereco,
			'checkin_data' => $obHosp->checkin_data,
			'checkout_data' => $obHosp->checkout_data,
			'status' => $obHosp->status
		];
	}

	public static function statusHospedagem($request,$id){

		//BUSCA O REGISTRO
		$obHosp = EntityHosp::getHospedagemById($id);

		//VALIDA A INSTANCIA
		if(!$obHosp instanceof EntityHosp){
			throw new \Exception("O registro ".$id." não foi encontrado", 404);
		}

		$status = filter_var($postVars['status'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$status_ocupacao = new Camas();

		if($status == 'checkin' or $status == 'pendente'){

			$status_ocupacao->status = 1;
		} else {
			$status_ocupacao->status = 0;
		}
		$status_ocupacao->atualizaStatusOcupado();

		
		//EXCLUI O REGISTRO
		$obHosp->status = filter_var($postVars['status'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
		$obHosp->atualizaStatus();

		//RETORNA OS DETALHES DA TRILHA ATUALIZADA

		return [
			'sucesso' => true
		];
	}


}