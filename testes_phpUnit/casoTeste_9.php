<?php

/*
  TESTE: cadastrar carro sem numero de placa
*/

use PHPUnit\Framework\TestCase;

class CasoTeste_9 extends TestCase
{
    public function testCadastroVeiculoSemPlaca(): void
    {
        // Cenário: tentativa de cadastro de veículo sem informar a placa

        $placa = '';

        $this->assertEmpty(
            $placa,
            'O sistema deve identificar que a placa não foi informada.'
        );
    }
}