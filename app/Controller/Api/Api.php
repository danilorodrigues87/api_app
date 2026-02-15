<?php 

namespace App\Controller\Api;

class Api{

	public static function getDetails($request){

		return [
			'nome' => 'API - APP HOSPEDAGEM CCB AF ',
			'versao' =>  'v1.0.0',
			'autor' => 'Danilo Rodrigues',
			'Descrição' => 'API criada para integração de aplicativo Hospedagem CCB AF, projeto desenvolvido sem fins lucrativos para ajudar em uma demanda da Organização Religioza Congração Crstã do Brasil da Cede de Alta Floresta, o App consiste em registrar as hospedagens de mebros da igreja com cadastro de Operdores que farão a gestão do aplicativo e suas funções, o app tambem conta com a função de Leitor de Codigo de Barras para a identificação dos irmãos previamente já cadastrados ou cadastrando na hora com o aplicativo, gestão e controle de dormitoesio e camas, informações importantes de cada mebmro hospedado.',
			'Desenvolvedor' => 'CTI Soluções - Empresa de Tecnologia especializada em desenvolvimento de Site, Sistemas e Aplicativos, tambem trabalhamos na area de educação, capacitando jovens e edultos no ramo da tecnologia preparando para o mercado de trabalho, conheça mas pelo nosso site www.ctieducacional.com.br | se tiver Para projetos e orçamentos entre em contato pelo whatsapp (15) 99846-4457'
		];
	}

	//RETORNA DETALHES PAGINAÇÃO
	protected static function getPagination($request,$obPagination){
		//QUERY PARAMS
		$queryParams = $request->getQueryParams();

		//PÁGINAS
		$pages = $obPagination->getPages();

		return [
			'paginaAtual' => isset($queryParams['page']) ? (int)$queryParams['page'] : 1,
			'quantidadePaginas' => !empty($pages) ? count($pages) : 1
		];
	}


}