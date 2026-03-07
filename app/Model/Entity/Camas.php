<?php

namespace App\Model\Entity;
use App\Model\Db\Database;

class Camas{

	public 
	$id,
	$setor_id,
	$nome_setor,
	$numero_cama,
	$status_ocupacao,
	$ativo;

	//RETORNA COM BASE NO ID
	public static function getCamaById($id){

		return self::getCamas('id = '.$id)->fetchObject(self::class);

	}

	//RETORNA COM BASE NO ID
	public static function getCamaByNumber($numero_cama){

		return self::getCamas('numero_cama = '.$numero_cama)->fetchObject(self::class);

	}

	//ENVIA PARA O BANCO
	public function cadastrar(){
		
		//INSERIR OS DADOS PARA O BANCO DE DADOS
		$obDatabase = new Database('camas');
		$this->id = $obDatabase->insert([

			'setor_id' => $this->setor_id,
			'numero_cama' => $this->numero_cama,
			'status_ocupacao' => $this->status_ocupacao,
			'ativo' => $this->ativo

		]);
		
		return true;
	} 

	//RETORNA A INFORMAÇÃO
	public static function getCamas(
		$where = null,
		$order = null,
		$limit = null,
		$fields = '*',
		$innerJoin = null,
		$group = null
	){
		return (new Database('camas'))->select(
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
		return (new Database('camas'))->update('id = '.$this->id,[
			'setor_id' => $this->setor_id,
			'numero_cama' => $this->numero_cama,
			'status_ocupacao' => $this->status_ocupacao,
			'ativo' => $this->ativo
		]);

	}

	//ATUALIZA NO BANCO
	public function atualizaStatusOcupado(){

		//ATUALIZA OS DADOS PARA O BANCO DE DADOS
		return (new Database('camas'))->update('id = '.$this->id,[
			'status_ocupacao' => $this->status_ocupacao
		]);

	}

	//ATUALIZA NO BANCO
	public function AtivaDesativar(){

		//ATUALIZA OS DADOS PARA O BANCO DE DADOS
		return (new Database('camas'))->update('id = '.$this->id,[
			'ativo' => $this->ativo
		]);

	}



}