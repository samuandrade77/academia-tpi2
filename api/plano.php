<?php
// Cadastro de PLANO (tabela plano)
require 'Validador.php';
$v = new Validador();

$v->obrigatorios([
    'nome' => 'Nome do plano', 'duracao' => 'Duração', 'mensalidade' => 'Mensalidade',
    'desconto' => 'Desconto', 'acessos' => 'Acessos por semana',
]);

$duracao = (int) $v->texto('duracao');
if ($v->texto('duracao') !== '' && !in_array($duracao, [1, 3, 6, 12])) {
    $v->erro('Duração deve ser 1, 3, 6 ou 12 meses.');
}
$mensalidade = $v->numero('mensalidade');
if ($v->texto('mensalidade') !== '' && ($mensalidade === null || $mensalidade < 50)) {
    $v->erro('Mensalidade mínima é R$ 50,00.');
}
$desconto = $v->numero('desconto');
if ($v->texto('desconto') !== '' && ($desconto === null || $desconto < 0 || $desconto > 40)) {
    $v->erro('Desconto deve ficar entre 0% e 40%.');
}
$acessos = (int) $v->texto('acessos');
if ($v->texto('acessos') !== '' && ($acessos < 1 || $acessos > 7)) {
    $v->erro('Acessos por semana: de 1 a 7.');
}
// Regra de negócio: plano mensal não tem desconto
if ($duracao === 1 && $desconto > 0) {
    $v->erro('O plano mensal não pode ter desconto.');
}

if ($v->temErros()) {
    $v->responder('', []);
}

// Lógica adicional: valor total, economia e custo por treino
$semDesconto = $mensalidade * $duracao;
$total = $semDesconto * (1 - $desconto / 100);
$treinosNoPeriodo = $acessos * 4 * $duracao;

$v->responder('Plano cadastrado (validação OK)', [
    'Plano' => $v->texto('nome'),
    'Duração' => "$duracao mês(es)",
    'Valor total' => Validador::reais($total),
    'Economia' => Validador::reais($semDesconto - $total),
    'Valor mensal efetivo' => Validador::reais($total / $duracao),
    'Custo por treino' => Validador::reais($total / $treinosNoPeriodo),
]);
