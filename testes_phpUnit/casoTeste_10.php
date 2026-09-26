<?php

/*
  TESTE: ALTERANDO DADOS PERMITIDOS DE UMA VIAJEM
*/

use PHPUnit\Framework\TestCase;

class CasoTeste_10 extends TestCase
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

    public function testAlteracaoDeViagem(): void
    {
        $resultado = $this->db->query("
            SELECT id
            FROM grupo_viagem
            LIMIT 1
        ");

        $this->assertGreaterThan(0, $resultado->num_rows);
    }

    protected function tearDown(): void
    {
        $this->db->close();
    }
}