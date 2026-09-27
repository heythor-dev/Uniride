<?php

/*
  TESTE DE STRESS:
  Simula vários usuários realizando solicitações ao mesmo tempo.
*/

use PHPUnit\Framework\TestCase;

class CasoTeste_13 extends TestCase
{
    public function testVariasSolicitacoesSimultaneas(): void
    {
        $quantidadeUsuarios = 300;

        $arquivoTemporario = tempnam(sys_get_temp_dir(), 'uniride_stress_');

        file_put_contents($arquivoTemporario, <<<'PHP'
<?php

$db = new mysqli(
    "localhost",
    "root",
    "",
    "Uniride",
    3306
);

if ($db->connect_error) {
    exit(1);
}

$resultado = $db->query("
    SELECT COUNT(*)
    FROM solicitacao_viagem
");

$db->close();

if ($resultado === false) {
    exit(1);
}

exit(0);
PHP
        );

        $processos = [];

        $inicio = microtime(true);

        for ($i = 0; $i < $quantidadeUsuarios; $i++) {
            $processos[$i] = proc_open(
                [
                    PHP_BINARY,
                    $arquivoTemporario
                ],
                [
                    0 => ['pipe', 'r'],
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w']
                ],
                $pipes
            );

            $this->assertIsResource(
                $processos[$i],
                "Não foi possível iniciar o usuário simultâneo " . ($i + 1)
            );

            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);
        }

        $falhas = 0;

        foreach ($processos as $processo) {
            $codigo = proc_close($processo);

            if ($codigo !== 0) {
                $falhas++;
            }
        }

        $tempo = microtime(true) - $inicio;

        unlink($arquivoTemporario);

        $this->assertSame(
            0,
            $falhas,
            "$falhas usuários apresentaram erro durante o teste."
        );

        $this->assertSame(
            $quantidadeUsuarios,
            count($processos)
        );

        echo PHP_EOL;
        echo "Usuários simultâneos: $quantidadeUsuarios" . PHP_EOL;
        echo "Falhas: $falhas" . PHP_EOL;
        echo "Tempo total: " . round($tempo, 4) . " segundos" . PHP_EOL;
    }
}