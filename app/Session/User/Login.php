<?php

namespace App\Session\User;

use \App\Model\Entity\Empresas;

class Login {


/*  remover no futuro casa não de problemas
    // Declaração das propriedades para evitar criação dinâmica
    public $id;
    public $id_admin;
    public $nome;
    public $email;
    public $nivel;
    public $termos_uso;
    public $acesso;
*/
    //INICIA A SESSÃO
    private static function init() {
        //VERIFICA SE A SESSÃO NÃO ESTÁ ATIVA
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    //CRIA O LOGIN DO USUÁRIO
    public static function login($obUser): bool {
        self::init();

        if (!isset($obUser->id, $obUser->email)) {
            return false;
        }

        //DEFINE A SESSÃO DO USUÁRIO
        $_SESSION['usuario-mvc-1'] = [
            'id'    => $obUser->id,
            'id_admin' => $obUser->id_admin,
            'nome'  => $obUser->nome,
            'email' => $obUser->email,
            'nivel' => $obUser->nivel,
            'termos_uso' => $obUser->termos_uso,
            'acesso' => json_decode($obUser->acesso, true) ?? []
        ];

        //SUCESSO
        return true;
    }

    // VERIFICA SE O USUÁRIO ESTÁ LOGADO E SE É UM ADMIN
    public static function isUserLogged() {
        // INICIA A SESSÃO
        self::init();

        // RETORNA A VERIFICAÇÃO
        return isset($_SESSION['usuario-mvc-1']);
    }

    public static function getUserLogedData(): ?array {
    self::init();

    if (!self::isUserLogged()) {
        return null;
    }

    if (!isset($_SESSION['usuario-mvc-1']['id_admin'])) {
        return null;
    }

    $dadosEmpresa = Empresas::getEmpresaById(
        $_SESSION['usuario-mvc-1']['id_admin']
    );

    return [
        'usuario' => $_SESSION['usuario-mvc-1'],
        'empresa' => $dadosEmpresa ? (array) $dadosEmpresa : null
    ];
}


    //EXECUTA O LOGOUT
    public static function logout() {
        //INICIA A SESSÃO
        self::init();

        //DESLOGA O USUÁRIO
        unset($_SESSION['usuario-mvc-1']);

        //SUCESSO
        return true;
    }

}
