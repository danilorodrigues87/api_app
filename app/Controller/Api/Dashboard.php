<?php 

namespace App\Controller\Api;
use \App\Model\Entity\Hospedagens;
use \App\Model\Entity\Camas;
use \App\Model\Db\Pagination;

class Dashboard extends Api{


	private static function getHospedagemItens($request, &$obPagination) {

    // 1. DEFINIÇÃO DOS CAMPOS E DO JOIN
    $fields = 'hospedagens.*, camas.numero_cama';
    $innerJoin = ' INNER JOIN camas ON hospedagens.cama_id = camas.id';
    
    // 2. FILTRO DE DATA (Últimos 3 dias)
    $where = 'hospedagens.checkin_data >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)';

    // QUANTIDADE TOTAL DE REGISTROS (Note que aqui o Join pode ser necessário se o WHERE filtrar por algo da cama)
    $quantidadeTotal = Hospedagens::getHospedagens($where, null, null, 'COUNT(*) as qtd', $innerJoin)->fetchObject()->qtd;

    // PAGINA ATUAL
    $queryParams = $request->getQueryParams();
    $paginaAtual = $queryParams['page'] ?? 1;

    // INSTANCIA DE PAGINAÇÃO
    $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);

    // RESULTADOS DA PAGINA (Passando o Join e os Fields específicos)
    $results = Hospedagens::getHospedagens($where, 'hospedagens.id DESC', $obPagination->getLimit(), $fields, $innerJoin);

    $itens = [];
    // RENDERIZA O ITEM
    while ($obHosp = $results->fetchObject(Hospedagens::class)) {
        $itens[] = [
            'id'                => (int)$obHosp->id,
            'membro_id'         => (int)$obHosp->membro_id,
            'numero_cama'       => $obHosp->numero_cama, 
            'checkin_data'      => $obHosp->checkin_data,
            'status'            => $obHosp->status
        
        ];
    }

    return $itens;
}

private static function getCamasLivres(){

$where = 'status_ocupacao = 0';

$camasLivres = Camas::getCamas($where, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

return $camasLivres;

}

private static function getHospedesAtivos(){

// Note as aspas simples ' ' envolvendo o valor checkin
$where = "status = 'checkin'"; 
$qntAtivos = Hospedagens::getHospedagens($where, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

return $qntAtivos;

}

private static function getRezervasHoje(){

	// Busca exatamente o dia de hoje (00:00:00 até 23:59:59)
$where = 'DATE(hospedagens.checkin_data) = CURDATE()';

$qntHospedagensHoje = Hospedagens::getHospedagens($where, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
return $qntHospedagensHoje;

}

	public static function dashboardData($request){

		return [
			'hospedagens_recentes' => self::getHospedagemItens($request,$obPagination),
			'rezervas-hoje' => self::getRezervasHoje(),
			'hospedes-ativos' => self::getHospedesAtivos(),
			'camas-livres' => self::getCamasLivres(),
			'paginacao' => parent::getPagination($request,$obPagination)
		];
	}

	
}