<?php

/*
  TESTE: impedir cadastro de placa duplicada
*/

use PHPUnit\Framework\TestCase;

class CasoTeste_9 extends TestCase
{
    public function testCadastroDePlacaDuplicada(): void
    {
        $placa = "ABC1D23";

        $_POST = [
            'placa' => 'ABC1D23',
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'ano' => '2025',
            'cor' => 'Preto',
            'renavam' => '123456789',
            'capacidade' => '5',
            'gastoCombustivel' => '10',
            'categoria' => 'Sedan'
        ];

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['usuario'] = [
            [
                'id_usuario' => 5
            ]
        ];

        ob_start();

        include __DIR__ . '/../perfilUsuario/php/novoCarro.php';

        $resposta = ob_get_clean();

        $dados = json_decode($resposta, true);

        $this->assertIsArray(
            $dados,
            'O cadastro deve retornar uma resposta JSON.'
        );

        $this->assertSame(
            'nok',
            $dados['status'],
            'O sistema deve impedir o cadastro de uma placa duplicada.'
        );
    }
}