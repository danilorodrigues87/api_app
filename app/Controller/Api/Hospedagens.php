<?php 

namespace App\Controller\Api;
use \App\Model\Entity\Hospedagens as EntityHosp;
use \App\Model\Entity\Camas;
use \App\Model\Db\Pagination;
use \App\Common\DateTimeHelper;

class Hospedagens extends Api{

	private static function getHospedagemItens($request,&$obPagination){

		// 1. DEFINIÇÃO DOS CAMPOS (Adicionado membros.nome)
    $fields = 'hospedagens.*, camas.numero_cama, membros.nome_completo as nome_membro,membros.cidade_residencia as cidade';
    
    // 2. DEFINIÇÃO DOS JOINS (Adicionado Join de membros)
    $innerJoin = ' INNER JOIN camas ON hospedagens.cama_id = camas.id';
    $innerJoin .= ' INNER JOIN membros ON hospedagens.membro_id = membros.id';
    
    $where = "status = 'checkin' OR status = 'pendende' ";

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
        $itens[] = [
            'id'              => (int)$obHosp->id,
            'nome_membro'     => $obHosp->nome_membro, // Novo campo adicionado
            'numero_cama'     => $obHosp->numero_cama, 
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

public static function cadNovaHospedagem($request) {
    $postVars = $request->getPostVars();

    // 1. VALIDAÇÃO DE CAMPOS OBRIGATÓRIOS GERAIS
    $requiredFields = ['membro_id', 'operador_id', 'tipo_local'];
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
        	empty($postVars['anfitriao_telefone'])) {
            throw new \Exception("Informe os dados do anfitrião (Nome e Telefone e endereço).", 400);

    }

    // 3. INSTÂNCIA E SANEAMENTO
    $obHosp = new EntityHosp;
    $obHosp->membro_id         = filter_var($postVars['membro_id'], FILTER_SANITIZE_NUMBER_INT);
    $obHosp->operador_id       = filter_var($postVars['operador_id'], FILTER_SANITIZE_NUMBER_INT);
    $obHosp->tipo_local        = filter_var($tipoLocal, FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->checkin_data      = $checkin_data;
    $obHosp->checkout_data     = $checkout_data ?? null;
    $obHosp->dias_estadia      = filter_var($postVars['dias_estadia'], FILTER_SANITIZE_NUMBER_INT);
    $obHosp->cama_id           = $camaId;
    $obHosp->status            = filter_var($postVars['status'] ?? 'Pendente', FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->anfitriao_nome     = filter_var($postVars['anfitriao_nome'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->anfitriao_telefone = filter_var($postVars['anfitriao_telefone'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $obHosp->anfitriao_endereco = filter_var($postVars['anfitriao_endereco'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

    // 4. PERSISTÊNCIA
    if (!$obHosp->cadastrar()) {
        throw new \Exception("Erro ao registrar a hospedagem no banco de dados.", 500);
    }

    // 5. ATUALIZAÇÃO DE STATUS (Ex: Ocupar a cama)
    // Removi o 'return' que estava antes desta linha para que o código continue
    $sucessoStatus = self::statusHospedagem($obHosp->id);
    
    if (!$sucessoStatus) {
        // Logar erro interno, mas talvez não travar o retorno se a hospedagem já foi criada
        // throw new \Exception("Hospedagem criada, mas falha ao atualizar status.", 500);
    }

    // 6. RETORNO DOS DADOS
    return [
        'id'                 => (int)$obHosp->id,
        'membro_id'          => (int)$obHosp->membro_id,
        'tipo_local'         => $obHosp->tipo_local,
        'cama_id'            => $obHosp->cama_id,
        'checkin_data'       => $obHosp->checkin_data,
        'status'             => $obHosp->status,
        'anfitriao_nome'     => $obHosp->anfitriao_nome
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