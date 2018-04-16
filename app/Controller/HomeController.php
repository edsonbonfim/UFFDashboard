<?php

namespace App\Controller;

use Pizza\Controller;

class HomeController extends Controller
{
    public function index()
    {
        if (!isset($_SESSION['login']) || $_SESSION['login'] != 'success') {
            $this->view->render('login');
        } else {
            $this->dashboard();
        }
    }

    private function dashboard()
    {
        $this->view->uff($_SESSION['uff']);

        $this->view->render('index');
    }
}
