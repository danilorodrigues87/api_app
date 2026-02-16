<?php

namespace App\Model\Entity;
use App\Model\Db\Database;

class Hospedagens{

	public $id,
	$membro_id,
	$operador_id,
	$tipo_local,
	$cama_id,
	$numero_cama,
	$nome_membro,
	$cidade,
	$anfitriao_nome,
	$anfitriao_telefone,
	$anfitriao_endereco,
	$dias_estadia,
	$checkin_data,
	$checkout_data,
	$status;

	//RETORNA COM BASE NO ID
	public static function getHospedagemById($id){

		return self::getHospedagens('id = '.$id)->fetchObject(self::class);

	}

	//ENVIA PARA O BANCO
	public function cadastrar(){
		
		//INSERIR OS DADOS PARA O BANCO DE DADOS
		$obDatabase = new Database('hospedagens');
		$this->id = $obDatabase->insert([

			'membro_id' => $this->membro_id,
			'operador_id' => $this->operador_id,
			'tipo_local' => $this->tipo_local,
			'cama_id' => $this->cama_id,
			'anfitriao_nome' => $this->anfitriao_nome,
			'anfitriao_telefone' => $this->anfitriao_telefone,
			'anfitriao_endereco' => $this->anfitriao_endereco,
			'dias_estadia' => $this->dias_estadia,
			'checkin_data' => $this->checkin_data,
			'checkout_data' => $this->checkout_data,
			'status' => $this->status

		]);
		
		return true;
	} 

	//RETORNA A INFORMAÇÃO
	public static function getHospedagens(
		$where = null,
		$order = null,
		$limit = null,
		$fields = '*',
		$innerJoin = null,
		$group = null
	){
		return (new Database('hospedagens'))->select(
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
		return (new Database('hospedagens'))->update('id = '.$this->id,[
			'membro_id' => $this->membro_id,
			'operador_id' => $this->operador_id,
			'tipo_local' => $this->tipo_local,
			'cama_id' => $this->cama_id,
			'anfitriao_nome' => $this->anfitriao_nome,
			'anfitriao_telefone' => $this->anfitriao_telefone,
			'anfitriao_endereco' => $this->anfitriao_endereco,
			'dias_estadia' => $this->dias_estadia,
			'checkin_data' => $this->checkin_data,
			'checkout_data' => $this->checkout_data,
			'status' => $this->status
		]);

	}

	//EXCLUI DO BANCO DE DADOS
	public function atualizaStatus(){

		return (new Database('hospedagens'))->update('id = '.$this->id,[
			'status' => $this->status
		]);
	}



}