<?php 

namespace App\Controller\Api;
use \App\Model\Entity\Hospedagens as EntityHosp;
use \App\Model\Entity\Membros as EntityMembros;
use \App\Model\Entity\Camas;
use \App\Model\Db\Pagination;
use \App\Common\DateTimeHelper;

class Hospedagens extends Api{

	private static function getHospedagemItens($request,&$obPagination){

		// 1. DEFINIÇÃO DOS CAMPOS (Adicionado membros.nome)
		$fields = 'hospedagens.*, membros.nome_completo as nome_membro,membros.cidade_residencia as cidade';

		$innerJoin = ' INNER JOIN membros ON hospedagens.membro_id = membros.id';

		$where = "status = 'checkin' OR status = 'pendente' ";

    // QUANTIDADE TOTAL DE REGISTROS
		$quantidadeTotal = EntityHosp::getHospedagens($where, null, null, 'COUNT(*) as qtd', $innerJoin)->fetchObject()->qtd;

    // PAGINA ATUAL
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;

    // INSTANCIA DE PAGINAÇÃO
		$obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

    // RESULTADOS DA PAGINA
		$results = EntityHosp::getHospedagens($where, 'hospedagens.id DESC', $obPagination->getLimit(), $fields, $innerJoin);


		$itens = [];
    // RENDERIZA O ITEM
		while ($obHosp = $results->fetchObject(EntityHosp::class)) {

			$obCama = Camas::getCamaById($obHosp->cama_id);
			if(!$obCama instanceof Camas){
				$numero_cama = 'S/N';
			} else {
				$numero_cama = $obCama->numero_cama;
			}

			$itens[] = [
				'id'              => (int)$obHosp->id,
            'nome_membro'     => $obHosp->nome_membro, // Novo campo adicionado
            'numero_cama'     => $numero_cama, 
            'cidade'          => $obHosp->cidade, 
            'checkin_data'    => date('d/m/Y H:i', strtotime($obHosp->checkin_data)),
            'status'          => $obHosp->status
        ];
    }

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

	$obMembro = EntityMembros::getMembroById((int)$obHosp->membro_id);

	if(!$obMembro instanceof EntityMembros){
		throw new \Exception("O Membro não foi encontrado", 404);
	}

	if($obHosp->tipo_local == "Alojamento"){

		$obCama = Camas::getCamaById($obHosp->cama_id);
		if(!$obCama instanceof Camas){
			throw new \Exception("A cama de ID ".$obHosp->cama_id." não foi encontrada", 404);
		}

	} 



// Helper simples para formatar datas com segurança
	$formatDate = function($date) {
		return (!empty($date) && strtotime($date)) 
		? date('d/m/Y H:i', strtotime($date)) 
		: 'N/D';
	};

	$membro = [
		'id'                => (int)($obMembro->id ?? 0),
		'nome_completo'     => trim($obMembro->nome_completo ?? ''),
		'telefone'          => $obMembro->telefone ?? '',
		'cidade_residencia' => $obMembro->cidade_residencia ?? '',
		'ministerio'        => $obMembro->ministerio ?? '',
		'admin_pertencente' => $obMembro->admin_pertencente ?? '',
		'codigo_barras'     => $obMembro->codigo_barras ?? ''
	];

	$hospedagem = [
		'id'                => (int)($obHosp->id ?? 0),
		'tipo_local'        => $obHosp->tipo_local ?? 'Não definido',
		'numero_cama'       => $obCama->numero_cama ?? 'S/N', 
		'dias_estadia'      => (int)($obHosp->dias_estadia ?? 0),
		'anfitriao_nome'    => trim($obHosp->anfitriao_nome ?? 'Não informado'),
		'anfitriao_telefone'=> $obHosp->anfitriao_telefone ?? 'N/A',
		'anfitriao_endereco'=> $obHosp->anfitriao_endereco ?? 'N/A',
		'obs_medicas'       => $obHosp->obs_medicas ?? 'Nenhuma',

    // Melhor tratar a data antes ou garantir que a função aceite null
		'checkin_data'      => isset($obHosp->checkin_data) ? $formatDate($obHosp->checkin_data) : 'Data não definida',
		'checkout_data'     => isset($obHosp->checkout_data) ? $formatDate($obHosp->checkout_data) : 'Data não definida',

		'status'            => $obHosp->status ?? 'pendente'
	];

	$dados = [

		'hospedagem' => $hospedagem,
		'membro' => $membro

	];

	return $dados;

}

public static function cadNovaHospedagem($request) {
	$postVars = $request->getPostVars();

    // 1. VALIDAÇÃO DE CAMPOS OBRIGATÓRIOS GERAIS
	$requiredFields = ['membro_id', 'operador_id', 'tipo_local', status];
	foreach ($requiredFields as $field) {
		if (empty($postVars[$field])) {
			throw new \Exception("Campo obrigatório não preenchido", 400);
		}
	}

	if(empty($postVars['checkin_data'])){
		throw new \Exception("Data de chegada não foi informada", 400);
	}

	$checkin_data = DateTimeHelper::dataEn($postVars['checkin_data']);
	$checkout_data = '';
	if(!empty($postVars['checkout_data'])){
		$checkout_data = DateTimeHelper::dataEn($postVars['checkout_data']);
	}

	if(empty($postVars['dias_estadia'])){
		throw new \Exception("Dias de estadia não foi informado", 400);
	}

	if(empty($postVars['tipo_local'])){
		throw new \Exception("Selecione o tipo de hospedagem", 400);
	}

	if(EntityHosp::getHospedagemByMemeberId($postVars['membro_id'])){
		throw new \Exception("O membro já tem uma hospedagem ativa", 400);
	}

	$tipoLocal = $postVars['tipo_local'];
	$camaId = null;

    // 2. LÓGICA ESPECÍFICA POR TIPO DE LOCAL
	if ($tipoLocal === 'Alojamento') {
		if (empty($postVars['numero_cama'])) {
			throw new \Exception("Selecione o número da cama para alojamento.", 400);
		}

		$obCama = Camas::getCamaByNumber((int)$postVars['numero_cama']);
		$camaArray = (array)$obCama;

		if (empty($camaArray)) {
			throw new \Exception("Cama não encontrada.", 404);
		}

		if ($camaArray['status_ocupacao']) {
			throw new \Exception("A cama selecionada já está ocupada.", 400);
		}

		$camaId = (int)$camaArray['id'];

	} elseif ($tipoLocal === 'Casa de um irmão') {
        // Validação para casa de anfitrião
		if (empty($postVars['anfitriao_nome']) || 
			empty($postVars['anfitriao_telefone']) || 
			empty($postVars['anfitriao_endereco'])) {
			throw new \Exception("Informe os dados do anfitrião (Nome e Telefone e endereço).", 400);
        } // <-- Faltava fechar este IF de validação
    } // <-- Faltava fechar este ELSEIF

    // 3. INSTÂNCIA E SANEAMENTO
    $obHosp = new EntityHosp;
    $obHosp->membro_id         = filter_var($postVars['membro_id'], FILTER_SANITIZE_NUMBER_INT);
    $obHosp->operador_id       = filter_var($postVars['operador_id'], FILTER_SANITIZE_NUMBER_INT);
    $obHosp->tipo_local        = filter_var($tipoLocal, FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->checkin_data      = $checkin_data;
    $obHosp->checkout_data     = $checkout_data ?: null;
    $obHosp->obs_medicas       = filter_var($postVars['obs_medicas'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->dias_estadia      = filter_var($postVars['dias_estadia'], FILTER_SANITIZE_NUMBER_INT);
    $obHosp->cama_id           = $camaId;
    $obHosp->status            = filter_var($postVars['status'] ?? 'Pendente', FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->anfitriao_nome    = filter_var($postVars['anfitriao_nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->anfitriao_telefone = filter_var($postVars['anfitriao_telefone'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->anfitriao_endereco = filter_var($postVars['anfitriao_endereco'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

    // 4. PERSISTÊNCIA
    if (!$obHosp->cadastrar()) {
    	throw new \Exception("Erro ao registrar a hospedagem no banco de dados.", 500);
    }

    // 5. ATUALIZAÇÃO DE STATUS (Ex: Ocupar a cama)
    $res = self::statusHospedagem((int)$obHosp->id);
    if(!$res){
    	throw new \Exception("Erro na atualização no numero da cama.", 400);
    }
    
    // 6. RETORNO DOS DADOS
    return [
    	'id'                => (int)$obHosp->id,
    	'membro_id'         => (int)$obHosp->membro_id,
    	'tipo_local'        => $obHosp->tipo_local,
    	'cama_id'           => $obHosp->cama_id,
    	'checkin_data'      => $obHosp->checkin_data,
    	'status'            => $obHosp->status,
    	'anfitriao_nome'    => $obHosp->anfitriao_nome
    ];
}

public static function setEditHospedagem($request, $id) {

    // 1. VALIDAÇÃO DO ID
	if (empty($id)) {
		throw new \Exception("Erro ao identificar o registro. Se isso persistir, contate o suporte.", 400);
	}

    // Busca a hospedagem existente no banco
    $obHosp = EntityHosp::getHospedagemById($id); // Certifique-se que esse método existe
    if (!$obHosp instanceof EntityHosp) {
    	throw new \Exception("Hospedagem não encontrada.", 404);
    }

    $postVars = $request->getPostVars();

    // 2. VALIDAÇÃO DE CAMPOS OBRIGATÓRIOS
    $requiredFields = ['membro_id', 'operador_id', 'tipo_local', 'status', 'checkin_data', 'dias_estadia'];
    foreach ($requiredFields as $field) {
    	if (empty($postVars[$field])) {
    		throw new \Exception("O campo " . str_replace('_', ' ', $field) . " é obrigatório.", 400);
    	}
    }

   // 4. TRATAMENTO DE DATAS (Direto no fluxo)
    $checkin_timestamp = isset($postVars['checkin_data']) ? strtotime(str_replace('/', '-', $postVars['checkin_data'])) : false;
    $checkin_data = $checkin_timestamp ? date('Y-m-d H:i:s', $checkin_timestamp) : null;

    $checkout_timestamp = !empty($postVars['checkout_data']) ? strtotime(str_replace('/', '-', $postVars['checkout_data'])) : false;
    $checkout_data = $checkout_timestamp ? date('Y-m-d H:i:s', $checkout_timestamp) : null;

// Validação caso a data seja obrigatória e venha inválida
    if (!$checkin_data) {
    	throw new \Exception("Data de check-in inválida ou não informada.", 400);
    }

    $tipoLocal = $postVars['tipo_local'];
    $camaId = null;

    // 5. LÓGICA POR TIPO DE LOCAL
    if ($tipoLocal === 'Alojamento') {


    	if (empty($postVars['numero_cama'])) {
    		throw new \Exception("Selecione o número da cama para alojamento.", 400);
    	}

    	$obCama = Camas::getCamaByNumber((int)$postVars['numero_cama']);
    	if (!$obCama) {
    		throw new \Exception("Cama não encontrada.", 404);
    	}


    	if($postVars['cama_atual'] != $postVars['numero_cama']){
    		$res = self::statusHospedagem((int)$obHosp->id);
    		if(!$res){
    			throw new \Exception("Erro na atualização no numero da cama.", 400);
    		}
    	}


    	$camaId = (int)$obCama->id;

    } elseif ($tipoLocal === 'Casa de um irmão') {
    	if (empty($postVars['anfitriao_nome']) || empty($postVars['anfitriao_telefone']) || empty($postVars['anfitriao_endereco'])) {
    		throw new \Exception("Informe os dados completos do anfitrião (Nome, Telefone e Endereço).", 400);
    	}

    	if(!empty($postVars['cama_atual'])){
    		$res = self::statusHospedagem((int)$obHosp->id);
    		if(!$res){
    			throw new \Exception("Erro na atualização no numero da cama.", 400);
    		}
    	}


    }

    // 6. ATUALIZAÇÃO DO OBJETO (Saneamento)
    $obHosp->membro_id         = (int)$postVars['membro_id'];
    $obHosp->operador_id       = (int)$postVars['operador_id'];
    $obHosp->tipo_local        = filter_var($tipoLocal, FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->obs_medicas       = filter_var($postVars['obs_medicas'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->checkin_data      = $checkin_data;
    $obHosp->checkout_data     = $checkout_data;
    $obHosp->dias_estadia      = (int)$postVars['dias_estadia'];
    $obHosp->cama_id           = $camaId;
    $obHosp->status            = filter_var($postVars['status'], FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->anfitriao_nome    = filter_var($postVars['anfitriao_nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->anfitriao_telefone = filter_var($postVars['anfitriao_telefone'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->anfitriao_endereco = filter_var($postVars['anfitriao_endereco'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

    // Salva as alterações
    $obHosp->atualizar();

    $res = self::statusHospedagem((int)$obHosp->id);
    if(!$res){
    	throw new \Exception("Erro na atualização no numero da cama.", 400);
    }

    // 7. RETORNO
    return [
    	'id'                 => (int)$obHosp->id,
    	'membro_id'          => (int)$obHosp->membro_id,
    	'tipo_local'         => $obHosp->tipo_local,
    	'cama_id'            => $obHosp->cama_id,
    	'dias_estadia'       => (int)$obHosp->dias_estadia,
        'obs_medicas'        => $obHosp->obs_medicas, // Removido (int)
        'anfitriao_nome'     => $obHosp->anfitriao_nome,
        'checkin_data'       => $obHosp->checkin_data,
        'status'             => $obHosp->status
    ];
}


public static function checkOut($request,$id){

	// 1. VALIDAÇÃO DO ID
	if (empty($id)) {
		throw new \Exception("Erro ao identificar o registro. Se isso persistir, contate o suporte.", 400);
	}

    // Busca a hospedagem existente no banco
    $obHosp = EntityHosp::getHospedagemById($id); // Certifique-se que esse método existe
    if (!$obHosp instanceof EntityHosp) {
    	throw new \Exception("Hospedagem não encontrada.", 404);
    }
    if($obHosp->status == 'checkout'){
    	throw new \Exception("Esse membro já fez check-out.", 404);

    }

    $postVars = $request->getPostVars();

     $checkout_timestamp = !empty($postVars['checkout_data']) ? strtotime(str_replace('/', '-', $postVars['checkout_data'])) : false;
    $checkout_data = $checkout_timestamp ? date('Y-m-d H:i:s', $checkout_timestamp) : null;

     if (empty($checkout_data)) {
    	throw new \Exception("Erro no registro do Check-Out.", 404);
    }

    $obHosp->checkout_data = $checkout_data;
    $obHosp->status = 'checkout';

    $obHosp->checkOutHospedagem();

    $res = self::statusHospedagem((int)$obHosp->id);
    if(!$res){
    	throw new \Exception("Erro na atualização no numero da cama.", 400);
    }

    return true;


}




private static function statusHospedagem($id){

		//BUSCA O REGISTRO
	$obHosp = EntityHosp::getHospedagemById($id);

		//VALIDA A INSTANCIA
	if(!$obHosp instanceof EntityHosp){
		throw new \Exception("O registro ".$id." não foi encontrado", 404);
	}

	$dadosCama = (array)Camas::getCamaById($obHosp->cama_id);
 
	$status = $dadosCama['status_ocupacao'];
	$obCama = new Camas();
	$obCama->id =  $obHosp->cama_id;

	if(!$status){
		$obCama->status_ocupacao = 1;
	} else {
		$obCama->status_ocupacao = 0;
	}

	$obCama->atualizaStatusOcupado();

	return [
		'sucesso' => true
	];
}


}