<?php
// AVALIAÇÃO FÍSICA (tabela avaliacao_fisica)
require 'Validador.php';
$v = new Validador();

$v->obrigatorios([
    'cpf_aluno' => 'CPF do aluno', 'data' => 'Data da avaliação', 'peso' => 'Peso',
    'altura' => 'Altura', 'cintura' => 'Cintura', 'quadril' => 'Quadril', 'sexo' => 'Sexo',
]);

if ($v->texto('cpf_aluno') !== '' && !$v->cpfValido($v->texto('cpf_aluno'))) {
    $v->erro('CPF do aluno inválido.');
}
$data = $v->data('data');
if ($v->texto('data') !== '' && (!$data || $data > new DateTime())) {
    $v->erro('Data da avaliação inválida ou no futuro.');
}

// Faixas aceitáveis para cada medida: [mínimo, máximo, mensagem]
$limites = [
    'peso' => [30, 300, 'Peso deve ficar entre 30 e 300 kg.'],
    'altura' => [1.0, 2.3, 'Altura deve ficar entre 1,00 e 2,30 m.'],
    'cintura' => [40, 200, 'Cintura deve ficar entre 40 e 200 cm.'],
    'quadril' => [50, 200, 'Quadril deve ficar entre 50 e 200 cm.'],
];
$medidas = [];
foreach ($limites as $campo => [$min, $max, $mensagem]) {
    $medidas[$campo] = $v->numero($campo);
    if ($v->texto($campo) !== '' && ($medidas[$campo] === null || $medidas[$campo] < $min || $medidas[$campo] > $max)) {
        $v->erro($mensagem);
    }
}
if (!in_array($v->texto('sexo'), ['F', 'M', ''])) {
    $v->erro('Sexo inválido.');
}

if ($v->temErros()) {
    $v->responder('', []);
}

// Lógica adicional: IMC (classificação OMS) e relação cintura-quadril
$imc = $medidas['peso'] / ($medidas['altura'] ** 2);
if ($imc < 18.5) {
    $classe = 'Abaixo do peso';
} elseif ($imc < 25) {
    $classe = 'Peso normal';
} elseif ($imc < 30) {
    $classe = 'Sobrepeso';
} else {
    $classe = 'Obesidade';
}

$rcq = $medidas['cintura'] / $medidas['quadril'];
$limiteRcq = $v->texto('sexo') === 'F' ? 0.85 : 0.90;

$v->responder('Avaliação registrada (validação OK)', [
    'IMC' => number_format($imc, 1, ',', ''),
    'Classificação' => $classe,
    'Relação cintura-quadril' => number_format($rcq, 2, ',', ''),
    'Risco cardiovascular (RCQ)' => $rcq > $limiteRcq ? 'Elevado' : 'Adequado',
    'Próxima avaliação' => (clone $data)->modify('+90 days')->format('d/m/Y'),
]);
