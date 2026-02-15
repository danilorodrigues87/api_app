<?php

namespace App\Model\Entity;
use App\Model\Db\Database;

class Membros{

	public 
	$id,
	$nome_completo,
	$telefone,
	$cidade_residencia,
	$ministerio,
	$admin_pertencente,
	$data_chegada,
	$dias_estadia,
	$observacoes_medicas,
	$codigo_barras,
	$created_at;

	//RETORNA COM BASE NO ID
	public static function getMembroById($id){

		return self::getMembros('id = '.$id)->fetchObject(self::class);

	}

	//RETORNA COM BASE NO ID
	public static function getMembroByCode($code){

		return self::getMembros('codigo_barras = '.$code)->fetchObject(self::class);

	}

	//ENVIA PARA O BANCO
	public function cadastrar(){
		
		//INSERIR OS DADOS PARA O BANCO DE DADOS
		$obDatabase = new Database('membros');
		$this->id = $obDatabase->insert([

			'nome_completo' => $this->nome_completo,
			'telefone' => $this->telefone,
			'cidade_residencia' => $this->cidade_residencia,
			'ministerio' => $this->ministerio,
			'admin_pertencente' => $this->admin_pertencente,
			'data_chegada' => $this->data_chegada,
			'dias_estadia' => $this->dias_estadia,
			'observacoes_medicas' => $this->observacoes_medicas,
			'codigo_barras' => $this->codigo_barras

		]);
		
		return true;
	} 

	//RETORNA A INFORMAÇÃO
	public static function getMembros(
		$where = null,
		$order = null,
		$limit = null,
		$fields = '*',
		$innerJoin = null,
		$group = null
	){
		return (new Database('membros'))->select(
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
		return (new Database('membros'))->update('id = '.$this->id,[
			'nome_completo' => $this->nome_completo,
			'telefone' => $this->telefone,
			'cidade_residencia' => $this->cidade_residencia,
			'ministerio' => $this->ministerio,
			'admin_pertencente' => $this->admin_pertencente,
			'data_chegada' => $this->data_chegada,
			'dias_estadia' => $this->dias_estadia,
			'observacoes_medicas' => $this->observacoes_medicas,
			'codigo_barras' => $this->codigo_barras
		]);

	}

	//EXCLUI DO BANCO DE DADOS
	public function excluir(){

		return (new Database('membros'))->delete('id = '.$this->id);

	}

}