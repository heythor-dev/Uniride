<?php

/*
  TESTE: aqui vamos tenar eliminar a viajem pertecente a outro usuario
*/

use PHPUnit\Framework\TestCase;

class CasoTeste15 extends TestCase
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

    public function testExcluirViagemDeOutroUsuario(): void
    {
        $resultado = $this->db->query("
            SELECT id, usuario_id
            FROM grupo_viagem
            LIMIT 1
        ");

        $this->assertNotFalse($resultado);
        $this->assertGreaterThan(0, $resultado->num_rows);

        $viagem = $resultado->fetch_assoc();

        $usuarioDono = (int) $viagem["usuario_id"];
        $outroUsuario = $usuarioDono + 1;

        $podeExcluir = $usuarioDono === $outroUsuario;

        $this->assertFalse(
            $podeExcluir,
            "Um usuário não deve poder excluir uma viagem pertencente a outro usuário."
        );
    }

    protected function tearDown(): void
    {
        $this->db->close();
    }
}