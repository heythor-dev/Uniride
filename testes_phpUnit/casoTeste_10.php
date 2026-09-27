<?php

/*
  TESTE: alterar título de uma viagem
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
            "",
            "Uniride",
            3306
        );

        $this->assertNull(
            $this->db->connect_error,
            "Não foi possível conectar ao banco."
        );
    }

    public function testAlteracaoDeTituloViagem(): void
    {
        $idUsuario = 1;
        $idViagem = 7;
        $novoTitulo = "Titulo alterado pelo teste";

        $resultado = $this->db->query("
            SELECT id, usuario_id, titulo
            FROM grupo_viagem
            WHERE id = $idViagem
        ");

        $this->assertGreaterThan(
            0,
            $resultado->num_rows,
            "A viagem informada não existe."
        );

        $viagem = $resultado->fetch_assoc();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['usuario_id'] = $idUsuario;

        $_SESSION['usuario'] = [
            [
                'id_usuario' => $idUsuario
            ]
        ];

        $_GET['id'] = $idViagem;

        $_POST = [
            'titulo' => $novoTitulo,
            'descricao' => $viagem['titulo'],
            'pontoPartida' => 'Ponto de partida teste',
            'pontoChegada' => 'Ponto de chegada teste',
            'tipoRecorrencia' => 'avulsa',
            'dataHora' => '2026-12-01 10:00:00'
        ];

        ob_start();

        include __DIR__ . '/../perfilUsuario/php/alterarViagem.php';

        $resposta = ob_get_clean();

        $dados = json_decode($resposta, true);

        $this->assertIsArray(
            $dados,
            "A alteração deve retornar uma resposta JSON."
        );

        $this->assertSame(
            'ok',
            $dados['status'],
            "O sistema deve permitir alterar o título da viagem."
        );
    }

    protected function tearDown(): void
    {
        $this->db->close();
    }
}