<?php
namespace App\Controller\Site;
use \App\Utils\View;

class About extends Page{


	//RETORNA A RENDERIZAÇÃO DA PÁGINA
	public static function index($request){

		// RETORNA A BASE DA PAGINA
		$content = View::render('site/modules/sobre',[]);

		// RETORNA A PÁGINA COMPLETA
        return parent::getPanel('Sobre', $content, 'sobre',$request);

	}

}