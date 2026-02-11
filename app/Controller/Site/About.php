<?php

namespace App\Controller\Site;

use \App\Utils\View;

class About{


	//RETORNA A RENDERIZAÇÃO DA PÁGINA
	public static function index($request){

		//CONTEUDO DA PAGINA
		$content = View::render('pages/about',[]);

		//TOP E MENU DA PAGINA
		$top_menu = View::render('pages/menu',[
			
		]);

		//RETORNA A PÁGINA COMPLETA
		return View::render('pages/page',[
			'title' => 'CTI - Sobre',
			'content' => $content,
			'top-menu' => $top_menu
		]);
	}

}