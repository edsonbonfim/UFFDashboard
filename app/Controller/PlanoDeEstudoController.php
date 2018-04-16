<?php

namespace App\Controller;

use Pizza\Controller;

use App\Model\Disciplinas;

class PlanoDeEstudoController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!isset($_SESSION['login']) || $_SESSION['login'] != 'success') {
            header('Location: /');
            exit;
        }
    }

    private function loop(): array
    {
        $loop = [];

        for($i = 0; $i < 19; $i++) {
            $loop[$i] = $i;
        }

        return $loop;
    }

    public function index()
    {
        $disciplinas = Disciplinas::find_by_matricula($_SESSION['uff']->matricula);

        $this->view->loop($this->loop());
        $this->view->disciplinas($disciplinas->attributes);

        $this->view->render('plano-de-estudo');
    }
}
