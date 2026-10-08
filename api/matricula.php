<?php
// MATRÍCULA: liga aluno, plano e instrutor (tabela matricula)
require 'Validador.php';
$v = new Validador();

$v->obrigatorios([
    'cpf_aluno' => 'CPF do aluno', 'plano' => 'Plano', 'instrutor' => 'Instrutor',
    'inicio' => 'Data de início', 'pagamento' => 'Forma de pagamento', 'vencimento' => 'Dia de vencimento',
]);

// Planos cadastrados (simula a tabela plano): meses e valor total
$planos = [
    'mensal' => ['Mensal', 1, 119.90],
    'trimestral' => ['Trimestral', 3, 329.70],
    'semestral' => ['Semestral', 6, 599.40],
    'anual' => ['Anual', 12, 1078.80],
];

if ($v->texto('cpf_aluno') !== '' && !$v->cpfValido($v->texto('cpf_aluno'))) {
    $v->erro('CPF do aluno inválido.');
}
$plano = $v->texto('plano');
if ($plano !== '' && !isset($planos[$plano])) {
    $v->erro('Plano inexistente.');
}
$formas = ['pix', 'cartao', 'boleto'];
if ($v->texto('pagamento') !== '' && !in_array($v->texto('pagamento'), $formas)) {
    $v->erro('Forma de pagamento inválida.');
}
$vencimento = (int) $v->texto('vencimento');
if ($v->texto('vencimento') !== '' && !in_array($vencimento, [5, 10, 15, 20])) {
    $v->erro('Dia de vencimento deve ser 5, 10, 15 ou 20.');
}
$inicio = $v->data('inicio');
$hoje = new DateTime('today');
if ($v->texto('inicio') !== '' && (!$inicio || $inicio < $hoje)) {
    $v->erro('A data de início não pode ser no passado.');
}

if ($v->temErros()) {
    $v->responder('', []);
}

// Lógica adicional: data de término, desconto no PIX e parcelamento
[$nomePlano, $meses, $valor] = $planos[$plano];
$termino = (clone $inicio)->modify("+$meses months")->modify('-1 day');

switch ($v->texto('pagamento')) {
    case 'pix':
        $valor *= 0.95;
        $condicao = 'À vista no PIX (5% de desconto)';
        break;
    case 'cartao':
        $condicao = "$meses x de " . Validador::reais($valor / $meses) . ' no cartão';
        break;
    default:
        $condicao = "$meses boleto(s) de " . Validador::reais($valor / $meses);
}

$v->responder('Matrícula realizada (validação OK)', [
    'Plano' => $nomePlano,
    'Instrutor' => $v->texto('instrutor'),
    'Vigência' => $inicio->format('d/m/Y') . ' a ' . $termino->format('d/m/Y'),
    'Valor final' => Validador::reais($valor),
    'Pagamento' => $condicao,
    'Vencimento' => "todo dia $vencimento",
]);
