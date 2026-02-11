<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use \App\Session\User\Login as SessionUser;

// REDERIZA A VIEW DA HOME DO PAINEL

class Home extends Page{

	public static function index($request){

         $dados = self::getInfo();

		// CONTEÚDO DA HOME
            $content = View::render('admin/modules/home/index',[

            'clientes-cadastrados' => $dados['qtt_clientes_cadastrados'],
             'visible' => $dados['visible']

         ]);


		// RETORNA A PÁGINA COMPLETA

        return parent::getPanel('Dashboard', $content, 'Dashboard',$request);

    }


    public static function getInfo(){

		// DADOS DO ADMIN

        $obUserLoged = SessionUser::getUserLogedData(); 

        if($obUserLoged['usuario']['nivel'] == 'Admin' or $obUserLoged['usuario']['nivel'] == 'Financeiro'){

            $visible = '';

        } else {

            $visible = 'd-none';

        }


        $id_admin = $obUserLoged['usuario']['id_admin'];


        // QUANTIDADE TOTAL DE CLIENTES CADASTRADOS

        $qtt_clientes_cadastrados = EntityUser::getUser('id_admin = ' . (int)$id_admin .' AND NIVEL = "Cliente"',null,null,'COUNT(*) as qtd')->fetchObject()->qtd;


        $data = [

         "qtt_clientes_cadastrados" => $qtt_clientes_cadastrados,
         "visible" => $visible

     ];


     //return self::getData();
     return $data;

 }



}