<?php
    require_once __DIR__ . '/conexao.php'
;
    $propriedade = $_POST['propriedade'];
    $safra = $_POST['safra'];

    $arquivo = $_FILES['arquivo'];
    // print_r($arquivo);
    if($arquivo != NULL) {
        $nomeFinal = time().'.csv';
        if(move_uploaded_file($arquivo['tmp_name'][0], $nomeFinal)) {

            $handle = fopen($nomeFinal, "r");

            $header = fgetcsv($handle, 1000, ";");

            while ($row = fgetcsv($handle, 1000, ";")) {
                $nota[] = array_combine($header, $row); 
            }
            // echo "<pre>";
            //     print_r($nota);
            // echo "</pre >";
            
            foreach($nota as $linha){
                // print_r($linha);
                $dataFormat = explode(' ', $linha['data']);
                $data1 = explode('/', $dataFormat[0]);
                $dataFinal = $data1[2]."-".$data1[0]."-".$data1[1];
                
                $talhao = $linha['idTalhao'];
                $nome = $linha['talhao'];
                $maquina = $linha['maquina'];
                $perda2m = $linha['Perda 2m'];
                $perda30m = $linha['Perda 30'];
                $perdaTotal = $linha['Perda Total'];
                $obs = $linha['Obs'];


                $sqlInsert = $conn->query("INSERT INTO dados_milho(id_usuario, data_hora, id_safra, id_propriedade, id_talhao, id_maquina, perda_2m, perda_30m, perda_total, obs) VALUES (17, '$dataFinal', '$safra', '$propriedade', '$talhao', '$maquina', '$perda2m', '$perda30m', '$perdaTotal', '$obs')");
                // echo "INSERT INTO dados_milho(id_usuario, data_hora, id_safra, id_propriedade, id_talhao, id_maquina, perda_2m, perda_30m, perda_total, obs) VALUES (17, '$dataFinal', '$safra', '$propriedade', '$talhao', '$maquina', '$perda2m', '$perda30m', '$perdaTotal', '$obs')";



            }
            
            fclose($handle);    

        }
    }
    header("Location: index.php");

?>