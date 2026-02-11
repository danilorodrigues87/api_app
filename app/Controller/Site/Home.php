<?php

namespace App\Controller\Site;

use \App\Utils\View;

class Home extends Page{


	//RETORNA A RENDERIZAÇÃO DA PÁGINA
	public static function index($request){

		// RETORNA A BASE DA PAGINA
		$content = View::render('site/home',[]);

		// RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Home', $content, 'Home',$request);

	}

}