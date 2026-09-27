<?php

/*
  TESTE: enviar solicitação duplicada para uma viagem
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
            "",
            "Uniride",
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
            FROM grupo_viagem
            WHERE usuario_id = 1
            LIMIT 1
        ");

        $this->assertGreaterThan(
            0,
            $resultado->num_rows,
            "Não existe uma viagem para realizar o teste."
        );

        $viagem = $resultado->fetch_assoc();

        $viagemId = (int) $viagem['id'];
        $passageiroId = 2;

        $this->db->query("
            DELETE FROM solicitacao_viagem
            WHERE viagem_id = $viagemId
            AND passageiro_id = $passageiroId
        ");

        $dados = json_encode([
            'viagem_id' => $viagemId,
            'solicitante_id' => $passageiroId,
            'tipo_vaga' => 'passageiro'
        ]);

        $primeiraResposta = $this->enviarSolicitacao($dados);

        $this->assertIsArray(
            $primeiraResposta,
            "A primeira solicitação deve retornar JSON."
        );

        $this->assertSame(
            'ok',
            $primeiraResposta['status'],
            "A primeira solicitação deve ser aceita."
        );

        $segundaResposta = $this->enviarSolicitacao($dados);

        $this->assertIsArray(
            $segundaResposta,
            "A segunda solicitação deve retornar JSON."
        );

        $this->assertSame(
            'erro',
            $segundaResposta['status'],
            "O sistema deve impedir uma solicitação duplicada."
        );

        $this->assertSame(
            'Você já possui uma solicitação ativa nesta carona!',
            $segundaResposta['mensagem'],
            "A segunda solicitação deve informar que já existe uma solicitação."
        );
    }

    private function enviarSolicitacao(string $dados): array
    {
        $ch = curl_init(
            'http://localhost/Uniride/php/postSolicitacao.php'
        );

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $dados,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($dados)
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);

        $resposta = curl_exec($ch);

        curl_close($ch);

        return json_decode($resposta, true);
    }

    protected function tearDown(): void
    {
        $this->db->close();
    }
}