<?php

namespace App\Http\Controllers;

use App\Models\Cep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;




class CepsController extends Controller
{
    public function index(){
    
        return view('ceps.index');
    }

    function validarCep($cep){
       
       $cepNumerico = preg_replace('/[^0-9]/', '', $cep);  
       if (strlen($cep)>9 || strlen($cepNumerico)!=8 || strlen($cep)<8 ) {
          return false;
       }
       else{
          if (strlen($cep)==8){
            return true;
          }
          else{
            if(stripos($cep,"-")==5){
                return true;
            }
            else{
               return false;  
            }
            
          }
       }
    }

    
    function conectarApi(Request $request){

      if($request->submitbutton=='csv'){
        return $this->exportarCsv($request);

      }
      if($request->submitbutton=='limpar'){
        return $this->limparTabela($request);

      }
      $cepsBusca = $request->input('cepBusca');

      
      
      $ceps = explode(";", $cepsBusca);
      
      

      $table= "<br>
      <table class='tabelaBorda'>
        <tr class='tabelaBorda' > 
            <td class='tabelaBorda' >CEP</td>
            <td class='tabelaBorda' >ENDERECO</td>
            <td class='tabelaBorda' >COMPLEMENTO</td>
            <td class='tabelaBorda' >BAIRRO</td>
            <td class='tabelaBorda' >CIDADE</td>
            <td class='tabelaBorda' >UF</td>
            <td class='tabelaBorda' >IBGE</td>
            <td class='tabelaBorda' >GIA</td>
            <td class='tabelaBorda' >DDD</td>
            <td class='tabelaBorda' >SIAFI</td>
        </tr>";


      foreach ($ceps as $cepBusca) {

        if (!$this->validarCep($cepBusca)){
            $table.=  "<tr class='tabelaBorda' >
                     <td class='tabelaBorda' >Cep $cepBusca Invalido</td>
                     <td class='tabelaBorda' >-</td>
                     <td class='tabelaBorda' >-</td>
                     <td class='tabelaBorda' >-</td>
                     <td class='tabelaBorda' >-</td>
                     <td class='tabelaBorda' >-</td>
                     <td class='tabelaBorda' >-</td>
                     <td class='tabelaBorda' >-</td>
                     <td class='tabelaBorda' >-</td>
                     <td class='tabelaBorda' >-</td>
                   </tr>";
        }
        else{
            //$response = Http::get('https://viacep.com.br/ws/'.$cepBusca.'/json/');
            $response = file_get_contents("https://viacep.com.br/ws/".$cepBusca."/json/");
            $cep = json_decode($response, true);
           
            if ( ( !( empty($cep['cep']) ) ) ) {          
               $table.=  "<tr class='tabelaBorda' >
                        <td class='tabelaBorda' >".$cep['cep']."</td>
                        <td class='tabelaBorda' >".$cep['logradouro']."</td>
                        <td class='tabelaBorda' >".$cep['complemento']."</td>
                        <td class='tabelaBorda' >".$cep['bairro']."</td>
                        <td class='tabelaBorda' >".$cep['localidade']."</td>
                        <td class='tabelaBorda' >".$cep['uf']."</td>
                        <td class='tabelaBorda' >".$cep['ibge']."</td>
                        <td class='tabelaBorda' >".$cep['gia']."</td>
                        <td class='tabelaBorda' >".$cep['ddd']."</td>
                        <td class='tabelaBorda' >".$cep['siafi']."</td>
                    </tr>";            
            }          
            else {
               $table.=  "<tr class='tabelaBorda' >
                       <td class='tabelaBorda' >Cep $cepBusca Nao Encontrado</td>
                       <td class='tabelaBorda' >-</td>
                       <td class='tabelaBorda' >-</td>
                       <td class='tabelaBorda' >-</td>
                       <td class='tabelaBorda' >-</td>
                       <td class='tabelaBorda' >-</td>
                       <td class='tabelaBorda' >-</td>
                       <td class='tabelaBorda' >-</td>
                       <td class='tabelaBorda' >-</td>
                       <td class='tabelaBorda' >-</td>
                    </tr>";
            }

                    
          }
      }
      $table.=  "</table>";


      $headers = array(
          'Content-Type' => 'text/csv'
        );        

        //criar o arquivo
        $filename = "cepsInterno.csv";
        $arquivo = fopen($filename, 'w');

        //adicionar primeira linha
        fputcsv($arquivo, [
            'cep',
            'logradouro',
            'complemento',
            'bairro',
            'localidade',
            'uf',
            'ibge',
            'gia',
            'ddd',
            'siafi'

        ]);

    echo view('ceps.index'); 
    echo $table;     
    echo '<script>document.getElementById("cepBusca").value="'.$cepsBusca.'";</script>';

     
      
    }

    function exportarCsv(Request $request){
        $cepsBusca = $request->input('cepBusca');  
        $ceps = explode(";", $cepsBusca);

       
        
        $informacoesCep=array();
        foreach ($ceps as $cepBusca) {
            if (!$this->validarCep($cepBusca)){
                $informacoesCep[]=array('Cep '.$cepBusca.' Invalido','-','-','-','-','-','-','-','-','-');

            }
            else{
                $response = file_get_contents("https://viacep.com.br/ws/".$cepBusca."/json/");
                $cep = json_decode($response, true);
                if ( ( !( empty($cep['cep']) ) )  ) {
                    $informacoesCep[]=array($cep['cep'],
                                            $cep['logradouro'],
                                            $cep['complemento'],
                                            $cep['bairro'],
                                            $cep['localidade'],
                                            $cep['uf'],
                                            $cep['ibge'],
                                            $cep['gia'],
                                            $cep['ddd'],
                                            $cep['siafi'] );

                }else{
                    $informacoesCep[]=array('Cep '.$cepBusca.' Nao Encontrado','-','-','-','-','-','-','-','-','-');
                }
            }

        }
   

        $headers = array(
          'Content-Type' => 'text/csv'
        );        

        //criar o arquivo
        $filename = "cepsInterno.csv";
        $arquivo = fopen($filename, 'w');

        //adicionar primeira linha
        fputcsv($arquivo, [
            'cep',
            'logradouro',
            'complemento',
            'bairro',
            'localidade',
            'uf',
            'ibge',
            'gia',
            'ddd',
            'siafi'

        ]);



        foreach ($informacoesCep as $linhaCep) {

            fputcsv($arquivo, array($linhaCep[0],$linhaCep[1],$linhaCep[2],$linhaCep[3],$linhaCep[4],$linhaCep[5],$linhaCep[6],$linhaCep[7],$linhaCep[8],$linhaCep[9]), ',');
            
        }

       
        fclose($arquivo);



        //fazer o download
        return Response::download($filename, "ceps.csv", $headers);
    }

    function limparTabela(Request $request){
        $cepsBusca = $request->input('cepBusca');  
        echo view('ceps.index');
        echo '<script>document.getElementById("cepBusca").value="'.$cepsBusca.'";</script>';
        $table= "<br>
        <table class='tabelaBorda'>
            <tr class='tabelaBorda' > 
            <td class='tabelaBorda' >CEP</td>
            <td class='tabelaBorda' >ENDERECO</td>
            <td class='tabelaBorda' >COMPLEMENTO</td>
            <td class='tabelaBorda' >BAIRRO</td>
            <td class='tabelaBorda' >CIDADE</td>
            <td class='tabelaBorda' >UF</td>
            <td class='tabelaBorda' >IBGE</td>
            <td class='tabelaBorda' >GIA</td>
            <td class='tabelaBorda' >DDD</td>
            <td class='tabelaBorda' >SIAFI</td>
        </tr>";
        echo $table;

    }
    
}
