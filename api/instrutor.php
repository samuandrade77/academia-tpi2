<?php
// Cadastro de INSTRUTOR (tabela instrutor)
require 'Validador.php';
$v = new Validador();

$v->obrigatorios([
    'nome' => 'Nome', 'cref' => 'CREF', 'especialidade' => 'Especialidade',
    'turno' => 'Turno', 'contratacao' => 'Data de contratação', 'email' => 'E-mail',
]);

// CREF no formato 012345-G/MG (6 dígitos, categoria G ou P, UF)
$cref = strtoupper(str_replace(' ', '', $v->texto('cref')));
if ($cref !== '' && !preg_match('/^\d{6}-[GP]\/[A-Z]{2}$/', $cref)) {
    $v->erro('CREF deve estar no formato 012345-G/MG.');
}
if ($v->texto('email') !== '' && !filter_var($v->texto('email'), FILTER_VALIDATE_EMAIL)) {
    $v->erro('E-mail inválido.');
}

// Lógica adicional: horas semanais por turno
$horasPorTurno = ['Manhã' => 30, 'Tarde' => 30, 'Noite' => 24, 'Integral' => 44];
$turno = $v->texto('turno');
if ($turno !== '' && !array_key_exists($turno, $horasPorTurno)) {
    $v->erro('Turno inválido.');
}

$contratacao = $v->data('contratacao');
if ($v->texto('contratacao') !== '' && (!$contratacao || $contratacao > new DateTime())) {
    $v->erro('Data de contratação inválida ou no futuro.');
}

if ($v->temErros()) {
    $v->responder('', []);
}

$meses = $contratacao->diff(new DateTime())->y * 12 + $contratacao->diff(new DateTime())->m;
$categoria = substr($cref, 7, 1) === 'G' ? 'Graduado' : 'Provisionado';

$v->responder('Instrutor cadastrado (validação OK)', [
    'Nome' => $v->texto('nome'),
    'CREF' => $cref,
    'Categoria' => $categoria,
    'Estado do registro' => substr($cref, -2),
    'Carga semanal' => $horasPorTurno[$turno] . ' h',
    'Tempo de casa' => "$meses meses",
    'Pode supervisionar estagiários' => $meses >= 12 && $categoria === 'Graduado' ? 'Sim' : 'Não',
]);
