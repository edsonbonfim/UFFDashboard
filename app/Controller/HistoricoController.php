<?php

namespace App\Controller;

use Pizza\Controller;

class HistoricoController extends Controller
{
    public $uff;

    public function __construct()
    {
        parent::__construct();

        if (!isset($_SESSION['login']) || $_SESSION['login'] != 'success') {
            header('Location: /');
            exit;
        }

        $this->uff = $_SESSION['uff'];
        $this->parseNome();
    }

    public function parseNome()
    {
        $nome = $this->uff->nome;

        preg_match('/([\w]+)\s([\w]+).*?/is', $nome, $match);
        
        $this->uff->nome = "{$match[1]} {$match[2]}";
    }
    
    public function index()
    {
        $this->view->uff($this->uff);
        $this->view->render('historico');
    }
}
