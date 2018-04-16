<?php

namespace App\Controller;

use Pizza\Controller;

use App\Model\Alunos;
use App\Model\Disciplinas;

class AuthController extends Controller
{
    public function logout()
    {
        unset($_SESSION['uff']);
        unset($_SESSION['login']);
        unset($_SESSION);
        session_destroy();
        header('Location: /');
        exit;
    }

    private function check($cpf, $senha)
    {
        if (empty($cpf)) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Não iremos conseguir sem o CPF :(';
            header('Location: /');
            exit;
        }

        if (empty($senha)) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Não iremos conseguir sem a senha :(';
            header('Location: /');
            exit;
        }

        if (!is_numeric($cpf)) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Por favor, o CPF só deve conter numeros.';
            header('Location: /');
            exit;
        }

        $cpf = preg_replace( '/[^0-9]/is', '', $cpf);

        if (strlen($cpf) != 11) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Um CPF é composto por 11 numeros.';
            header('Location: /');
            exit;
        }

        if (preg_match('/(\d)\1{10}/', $cpf)) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Tentando nos enganar com esse CPF?';
            header('Location: /');
            exit;
        }

        // Calculo para validar o CPF
        for ($i = 9; $i < 11; $i++) {
            for ($k = 0, $c = 0; $c < $i; $c++) {
                $k += $cpf{$c} * (($i + 1) - $c);
            }
            $k = ((10 * $k) % 11) % 10;
            if ($cpf{$c} != $k) {
                $_SESSION['login'] = 'failed';
                $_SESSION['message'] = 'CPF inválido.';
                header('Location: /');
                exit;
            }
        }
    }

    public function login($request): void
    {
        $cpf   = $request->getArgs('iduff');
        $senha = $request->getArgs('senha');

        $this->check($cpf, $senha);
        $this->auth($cpf, $senha);
    }

    public function auth($cpf, $senha): void
    {
        $aluno = Alunos::find_by_cpf_and_senha($cpf, $senha);

        if (!$aluno)
        {
            $this->scraper($cpf, $senha);
        }

        $_SESSION['login'] = 'success';
        $_SESSION['uff'] = (object) $aluno->attributes;
        header('Location: /');
        exit;
    }

    public function scraper(string $cpf, string $senha): void
    {
        // Chamada externa para scraping da pagina da UFF
        exec("./UFF '{$cpf}' '{$senha}'", $output, $return_var);

        // Connection timed out
        if ($return_var == 110) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Tempo de conexão esgotado.';
            header('Location: /');
            exit;
        }

        if (count($output) == 0) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Ocorreu um erro, tente novamente.';
            header('Location: /');
            exit;
        }

        $uff = json_decode($output[0]);

        if ($uff->status == -1) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'CPF ou senha inválidos.';
            header('Location: /');
            exit;
        }

        $aluno = new Alunos;

        $aluno->nome = $uff->nome;
        $aluno->matricula = $uff->matricula;
        $aluno->cpf = $cpf;
        $aluno->senha = $senha;
        $aluno->foto = $uff->foto;

        $aluno->save();

        foreach ($uff->plano_de_estudo as $plano) {
            $disciplina = new Disciplinas;
            $disciplina->matricula = $uff->matricula;

            foreach ($plano as $key => $value) {
                $disciplina->$key = $value;

            }

            $disciplina->save();
        }

        $this->auth($cpf, $senha);
    }
}
