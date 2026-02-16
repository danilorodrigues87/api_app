<?php

namespace App\Model\Entity;
use App\Model\Db\Database;

class Setores{

	public 
	$id,
	$nome_setor;

	//RETORNA COM BASE NO ID
	public static function getSetorById($id){

		return self::getSetores('id = '.$id)->fetchObject(self::class);

	}


	//ENVIA PARA O BANCO
	public function cadastrar(){
		
		//INSERIR OS DADOS PARA O BANCO DE DADOS
		$obDatabase = new Database('setores');
		$this->id = $obDatabase->insert([

			'nome_setor' => $this->nome_setor

		]);
		
		return true;
	} 

	//RETORNA A INFORMAÇÃO
	public static function getSetores(
		$where = null,
		$order = null,
		$limit = null,
		$fields = '*',
		$innerJoin = null,
		$group = null
	){
		return (new Database('setores'))->select(
			$where,
			$order,
			$limit,
			$fields,
			$innerJoin,
			$group
		);
	}



	//ATUALIZA NO BANCO
	public function atualizar(){

		//ATUALIZA OS DADOS PARA O BANCO DE DADOS
		return (new Database('setores'))->update('id = '.$this->id,[
			'nome_setor' => $this->nome_setor
		]);

	}


	//EXCLUI DO BANCO DE DADOS
	public function excluir(){

		return (new Database('setores'))->delete('id = '.$this->id);

	}

}