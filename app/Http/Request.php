<?php

namespace App\Http;

class Request {

    private $router;        // Instância do Router
    private $httpMethod;    // Método HTTP da requisição
    private $uri;           // URI da página
    private $queryParams = [];   // Parâmetros da URL
    private $postVars = [];      // Variáveis do POST da página
    private $headers = [];       // Cabeçalhos
    private $fileVars = [];      // Variáveis de arquivos (FILES)

    // CONSTRUTOR DA CLASSE
    public function __construct($router){
        $this->router = $router;
        $this->queryParams = $_GET ?? [];
        $this->setHeaders();
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
        $this->setPostVars();
        $this->setFileVars();
    }

    private function setHeaders(){
        $headers = [];
        if (function_exists('getallheaders')) {
            $headers = getallheaders() ?: [];
        }
        if (empty($headers)) {
            foreach ($_SERVER as $key => $value) {
                if (strpos($key, 'HTTP_') !== 0) {
                    continue;
                }
                $name = str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($key, 5))));
                $headers[$name] = $value;
            }
        }
        $normalized = [];
        foreach ($headers as $key => $value) {
            $normalized[strtolower($key)] = $value;
        }
        $this->headers = $normalized;
    }

    // DEFINE AS VARIÁVEIS DO POST
    private function setPostVars(){
        // VERIFICA O MÉTODO DA REQUISIÇÃO
        if($this->httpMethod == 'GET') return false;

        // POST PADRÃO
        $this->postVars = $_POST ?? [];

        // POST JSON
        $inputRow = file_get_contents('php://input');
        $this->postVars = (strlen($inputRow) && empty($_POST)) ? json_decode($inputRow, true) : $this->postVars;
    }

    // DEFINE AS VARIÁVEIS DE ARQUIVOS
    private function setFileVars(){
        // VERIFICA SE EXISTEM ARQUIVOS SENDO ENVIADOS
        $this->fileVars = $_FILES ?? [];
    }

    private function setUri(){
        // URI COMPLETA COM (GETS)
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';
        // REMOVE OS GETS DA URI
        $xUri = explode('?', $this->uri);
        $this->uri = $xUri[0];
    }

    // RECUPERA O MÉTODO HTTP
    public function getHttpMethod(){
        return $this->httpMethod;
    }

    // RETORNA A URI
    public function getUri(){
        return $this->uri;
    }

    // RETORNA OS HEADERS
    public function getHeaders(){
        return $this->headers;
    }

    // RETORNA OS PARÂMETROS
    public function getQueryParams(){
        return $this->queryParams;
    }

    // RETORNA AS VARIÁVEIS POST
    public function getPostVars(){
        return $this->postVars;
    }

    // RETORNA AS VARIÁVEIS DE ARQUIVOS
    public function getFileVars(){
        return $this->fileVars;
    }

    // RETORNA UMA INSTÂNCIA DO ROUTER
    public function getRouter(){
        return $this->router;
    }

}
