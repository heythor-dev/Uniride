<?php

/*
  TESTE: usuário tentando excluir viagem de outro usuário
*/

use PHPUnit\Framework\TestCase;

class CasoTeste_15 extends TestCase
{
    private mysqli $db;

    protected function setUp(): void
    {
        $this->db = new mysqli(
            "localhost",
            "root",
            "",
            "Uniride",
            3306
        );

        $this->assertNull(
            $this->db->connect_error,
            "Não foi possível conectar ao banco."
        );
    }

    public function testUsuarioNaoPodeExcluirViagemDeOutroUsuario(): void
    {
        $idUsuario = 1;
        $idViagem = 3;

        $resultado = $this->db->query("
            SELECT id, usuario_id
            FROM grupo_viagem
            WHERE id = $idViagem
        ");

        $this->assertGreaterThan(
            0,
            $resultado->num_rows,
            "A viagem informada não existe."
        );

        $viagem = $resultado->fetch_assoc();

        $this->assertNotSame(
            $idUsuario,
            (int) $viagem['usuario_id'],
            "A viagem deve pertencer a outro usuário."
        );

        $_GET['id'] = $idViagem;

        ob_start();

        include __DIR__ . '/../perfilUsuario/php/excluirViagem.php';

        $resposta = ob_get_clean();

        $dados = json_decode($resposta, true);

        $this->assertIsArray(
            $dados,
            "A exclusão deve retornar uma resposta JSON."
        );

        $this->assertSame(
            'nok',
            $dados['status'],
            "O sistema não deve permitir excluir uma viagem de outro usuário."
        );
    }

    protected function tearDown(): void
    {
        $this->db->close();
    }
}