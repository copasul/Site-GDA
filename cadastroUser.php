<?php
date_default_timezone_set('America/Campo_grande');
include __DIR__ . '/backend/conexao.php';

$nome = filter_input(INPUT_POST, 'nome', FILTER_VALIDATE_EMAIL);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
$tel = filter_input(INPUT_POST, 'tel', FILTER_SANITIZE_STRING);
$tipo = filter_input(INPUT_POST, 'tipo', FILTER_SANITIZE_STRING);

$relacao_fazenda[] = filter_input(INPUT_POST, 'relacao', FILTER_SANITIZE_STRING);




