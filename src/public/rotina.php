<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Model\Rotina;
$rotina = new Rotina();
$token = $rotina->gerarToken();

$dadosValidados = [];
try {
    //Valida os dados enviados
    $dadosValidados = $rotina->validacao($_POST);
} catch (\Exception $e) {
    echo json_encode(['status' => 'false', 'msg' => $e->getMessage()]);
}

$numero = $rotina->extrairNumeroEndereco($dadosValidados['endereco']);
if (empty($numero)) {
    echo json_encode(['status' => 'false', 'msg' => 'O endere&ccedil;o enviado n&atilde;o contem n&uacute;mero!']);
    return false;
}

try {
    $send = $rotina->sendEmail($dadosValidados['email'], $token);
} catch (\Exception $e) {
    echo json_encode(['status' => 'false', 'msg' => $e->getMessage()]);
}

$cookie = $rotina->gravarCookie($token);
$rotina->gravarSession($token);
$ip = $rotina->getIp();

echo json_encode(['status' => 'true' ,'numero' => $numero, 'ip' => $ip]);