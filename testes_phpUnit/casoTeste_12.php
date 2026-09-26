<?php

/*
  TESTE: Aqui vamos a simular que haja uma solicitação duplicada
*/

use PHPUnit\Framework\TestCase;

class CasoTeste_12 extends TestCase
{
    private mysqli $db;

    protected function setUp(): void
    {
        $this->db = new mysqli(
            "localhost",
            "root",
            "12345678",
            "uniride",
            3306
        );

        $this->assertNull(
            $this->db->connect_error,
            "Não foi possível conectar ao banco."
        );
    }

    public function testSolicitacaoDuplicada(): void
    {
        $resultado = $this->db->query("
            SELECT id
            FROM solicitacao_viagem
            LIMIT 1
        ");

        $this->assertNotFalse($resultado);
    }

    protected function tearDown(): void
    {
        $this->db->close();
    }
}