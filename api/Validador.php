<?php
/**
 * Classe usada por todos os endpoints: lê o JSON enviado pelo JavaScript,
 * guarda os erros encontrados e devolve a resposta em JSON.
 */
class Validador
{
    private array $dados;
    private array $erros = [];

    public function __construct()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'mensagens' => ['Use o método POST.']]);
            exit;
        }

        // O fetch envia JSON no corpo da requisição; por isso lemos php://input
        $json = json_decode(file_get_contents('php://input'), true);
        $this->dados = is_array($json) ? $json : [];
    }

    public function texto(string $campo): string
    {
        return trim((string) ($this->dados[$campo] ?? ''));
    }

    public function numero(string $campo): ?float
    {
        $valor = str_replace(',', '.', $this->texto($campo));
        return is_numeric($valor) ? (float) $valor : null;
    }

    public function data(string $campo): ?DateTime
    {
        $d = DateTime::createFromFormat('!Y-m-d', $this->texto($campo));
        return $d ?: null;
    }

    public function obrigatorios(array $campos): void
    {
        foreach ($campos as $campo => $rotulo) {
            if ($this->texto($campo) === '') {
                $this->erro("Preencha o campo $rotulo.");
            }
        }
    }

    public function erro(string $mensagem): void
    {
        $this->erros[] = $mensagem;
    }

    public function temErros(): bool
    {
        return count($this->erros) > 0;
    }

    public function cpfValido(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);
        if (strlen($cpf) != 11 || count(array_unique(str_split($cpf))) == 1) {
            return false;
        }
        foreach ([9, 10] as $posicao) {
            $soma = 0;
            for ($i = 0; $i < $posicao; $i++) {
                $soma += $cpf[$i] * ($posicao + 1 - $i);
            }
            $resto = $soma % 11;
            $digito = $resto < 2 ? 0 : 11 - $resto;
            if ($cpf[$posicao] != $digito) {
                return false;
            }
        }
        return true;
    }

    public static function reais(float $valor): string
    {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }

    /** Envia a resposta final: erros (se houver) ou o resumo de sucesso. */
    public function responder(string $titulo, array $resumo): void
    {
        if ($this->temErros()) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'mensagens' => $this->erros], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['ok' => true, 'titulo' => $titulo, 'resumo' => $resumo], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
}
