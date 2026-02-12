<?php

namespace App\Model\Entity;
use App\Model\Db\Database;

class User{

	public $id,
	$nome,
	$email,
	$cargo,
	$rec_senha,
	$senha;


	//RETORNA UM USUÁRIO COM BASE NO EMAIL
	public static function getUserByEmail($email){
		return (new Database('perfis_operadores'))->select('email = "'.$email.'"')->fetchObject(self::class);
	}

	//RETORNA UM USUÁRIO COM BASE NO CÓDIGO DE RECUPERAÇÃO DE SENHA
	public static function getUserByCode($recCode){
		return (new Database('perfis_operadores'))->select('recCode = "'.$recCode.'"')->fetchObject(self::class);
	}	

	//RETORNA UM DEPOIMENTO COM BASE NO ID
	public static function getUserById($id){

		return self::getUser('id = '.$id)->fetchObject(self::class);

	}

	//ENVIA A MENSAGEM PARA O BANCO
	public function cadastrar(){
		
		//INSERIR OS DADOS PARA O BANCO DE DADOS
		$obDatabase = new Database('perfis_operadores');
		$this->id = $obDatabase->insert([
			'nome' => $this->nome,
			'email' => $this->email,
			'senha' => $this->senha,
			'cargo' => $this->cargo

		]);
		
		return true;
	} 

	//RETORNA DEPOIMENTOS
	public static function getUser($where = null,$order = null,$limit = null,$fields = '*'){

		return (new Database('perfis_operadores'))->select($where,$order,$limit,$fields);
	}

	//ATUALIZA A MENSAGEM NO BANCO
	public function atualizar(){

		//ATUALIZA OS DADOS PARA O BANCO DE DADOS
		return (new Database('perfis_operadores'))->update('id = '.$this->id,[
			'nome' => $this->nome,
			'email' => $this->email,
			'senha' => $this->senha,
			'cargo' => $this->cargo
		]);

	}


	//ATUALIZA A MENSAGEM NO BANCO
	public function termoAceito(){

		//ATUALIZA OS DADOS PARA O BANCO DE DADOS
		return (new Database('perfis_operadores'))->update('id = '.$this->id,[
			'termos_uso' => $this->termos_uso
		]);

	}

	//EXCLUI DO BANCO DE DADOS
	public function excluir(){

		return (new Database('perfis_operadores'))->delete('id = '.$this->id);

	}


	//RESETAR SENHA DO USUÁRIO
	public function resetSenha(){

		//ATUALIZA OS DADOS PARA O BANCO DE DADOS
		return (new Database('perfis_operadores'))->update('id = '.$this->id,[
			'senha' => $this->senha
		]);

	}

	//ENVIA O CODIGO DE RECUPERAÇÃO PARA O BANCO DE DADOS
	public function setRecCode(){

		//ATUALIZA OS DADOS PARA O BANCO DE DADOS
		return (new Database('perfis_operadoress'))->update('id = '.$this->id,[
			'recCode' => $this->code
		]);

	}

}