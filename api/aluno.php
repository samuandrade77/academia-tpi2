<?php
// Cadastro de ALUNO (tabela aluno)
require 'Validador.php';
$v = new Validador();

$v->obrigatorios([
    'nome' => 'Nome', 'cpf' => 'CPF', 'nascimento' => 'Data de nascimento',
    'telefone' => 'Telefone', 'email' => 'E-mail', 'objetivo' => 'Objetivo',
]);

if ($v->texto('cpf') !== '' && !$v->cpfValido($v->texto('cpf'))) {
    $v->erro('CPF inválido.');
}
if ($v->texto('email') !== '' && !filter_var($v->texto('email'), FILTER_VALIDATE_EMAIL)) {
    $v->erro('E-mail inválido.');
}
$fone = preg_replace('/\D/', '', $v->texto('telefone'));
if ($fone !== '' && strlen($fone) < 10) {
    $v->erro('Telefone precisa ter DDD + número.');
}
$objetivos = ['Emagrecimento', 'Hipertrofia', 'Condicionamento', 'Saúde'];
if ($v->texto('objetivo') !== '' && !in_array($v->texto('objetivo'), $objetivos)) {
    $v->erro('Objetivo inválido.');
}

$nascimento = $v->data('nascimento');
$idade = 0;
if ($v->texto('nascimento') !== '' && !$nascimento) {
    $v->erro('Data de nascimento inválida.');
} elseif ($nascimento) {
    $idade = $nascimento->diff(new DateTime())->y;
    if ($idade < 12 || $idade > 100) {
        $v->erro('A academia atende alunos de 12 a 100 anos.');
    }
    // Lógica adicional: menor de 18 precisa de responsável
    if ($idade < 18 && $v->texto('responsavel') === '') {
        $v->erro('Aluno menor de 18 anos: informe o nome do responsável.');
    }
}

// Lógica adicional: faixa etária para montar turmas
if ($idade < 18) {
    $faixa = 'Teen';
} elseif ($idade < 60) {
    $faixa = 'Adulto';
} else {
    $faixa = 'Melhor idade (acompanhamento reforçado)';
}

$v->responder('Aluno cadastrado (validação OK)', [
    'Nome' => ucwords(strtolower($v->texto('nome'))),
    'Idade' => "$idade anos",
    'Faixa' => $faixa,
    'Objetivo' => $v->texto('objetivo'),
    'Responsável' => $v->texto('responsavel') ?: 'Não se aplica',
]);
