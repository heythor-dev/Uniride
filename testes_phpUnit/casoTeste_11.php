<?php

/*
  TESTE: enviar solicitação para participar de uma viagem
*/

use PHPUnit\Framework\TestCase;

class CasoTeste_11 extends TestCase
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

    public function testSolicitacaoDeViagem(): void
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

        $dadosResposta = json_decode($resposta, true);

        $this->assertIsArray(
            $dadosResposta,
            "A solicitação deve retornar uma resposta JSON."
        );

        if ($dadosResposta['status'] !== 'ok') {
            $this->fail(
                "Status recebido: " .
                ($dadosResposta['status'] ?? 'null') .
                " | Mensagem: " .
                ($dadosResposta['mensagem'] ?? 'Sem mensagem.')
            );
        }
    }

    protected function tearDown(): void
    {
        $this->db->close();
    }
}