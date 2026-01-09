<?php

$id = $_GET['id'];
require_once '../dompdf/autoload.inc.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;

// instantiate and use the dompdf class
$dompdf = new Dompdf();

ob_start();
if($id == 1){
	require __DIR__ . "/perda-colheita.php";
}
// }elseif ($id == 2) {
// 	require __DIR__ . "/relatorios/historico.php";

// }
$dompdf->loadHtml(ob_get_clean());

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream("relatorio ".date('d-m-Y H-i-s').".pdf", ["Attachment"=>False]);
?>

