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

    
    function escolherCaminho(Request $request){

      if($request->submitbutton=='csv'){
        return $this->exportarCsv($request);

      }

      if($request->submitbutton=='limpar'){
        return $this->limparTabela($request);

      }

      if($request->submitbutton=='buscar'){
        return $this->mandarDadoView($request);

      }  
    }

    function exportarCsv(Request $request){

        if($request->input('mensagemCepVazio')!='' || $request->input('mensagemTabelaVazia')!='' ){
            $MensagemCepVazio="Nao e possivel exportar tabela vazia";
            return view('ceps.index',["MensagemCepVazio"=>$MensagemCepVazio]);
               
        }

        $cepsBusca = $request->input('cepsTabela'); 
        $ceps = explode(";", $cepsBusca);

        $informacoesCep=array();
        foreach ($ceps as $cepBusca) {
            if (!$this->validarCep($cepBusca)){
                $informacoesCep[]=array("cep"=>'Cep '.$cepBusca.' Invalido',"logradouro"=>'-',"complemento"=>'-',"bairro"=>'-',"localidade"=>'-',"uf"=>'-',"ibge"=>'-',"gia"=>'-',"ddd"=>'-',"siafi"=>'-');

            }
            else{
                $response = file_get_contents("https://viacep.com.br/ws/".$cepBusca."/json/");
                $cep = json_decode($response, true);
                if ( ( !( empty($cep['cep']) ) )  ) {
                    $informacoesCep[]=array("cep"=>$cep['cep'],
                                            "logradouro"=>$cep['logradouro'],
                                            "complemento"=>$cep['complemento'],
                                            "bairro"=>$cep['bairro'],
                                            "localidade"=>$cep['localidade'],
                                            "uf"=>$cep['uf'],
                                            "ibge"=>$cep['ibge'],
                                            "gia"=>$cep['gia'],
                                            "ddd"=>$cep['ddd'],
                                            "siafi"=>$cep['siafi'] );

                }else{
                    $informacoesCep[]=array("cep"=>'Cep '.$cepBusca.' Nao Encontrado',"logradouro"=>'-',"complemento"=>'-',"bairro"=>'-',"localidade"=>'-',"uf"=>'-',"ibge"=>'-',"gia"=>'-',"ddd"=>'-',"siafi"=>'-');;
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

            fputcsv($arquivo, array($linhaCep['cep'],$linhaCep['logradouro'],$linhaCep['complemento'],$linhaCep['bairro'],$linhaCep['localidade'],$linhaCep['uf'],$linhaCep['ibge'],$linhaCep['gia'],$linhaCep['ddd'],$linhaCep['siafi']), ',');
            
        }

       
        fclose($arquivo);



        //fazer o download
        return Response::download($filename, "ceps.csv", $headers);
    }

    function limparTabela(Request $request){
        $ceps=array();
        $CepsTabela="";
        return view('ceps.index', ["ceps"=>$ceps],["CepsTabela"=>$CepsTabela]);

    }

    function mandarDadoView(Request $request){
        if($request->input('cepBusca')==''){
            $MensagemCepVazio="Digite um Cep";
            return view('ceps.index',["MensagemCepVazio"=>$MensagemCepVazio]);
               
        }
        $MensagemCepVazio="";
        $cepsBusca = $request->input('cepBusca');  
        $cepsBuscaArray = explode(";", $cepsBusca);

        $ceps=array();
        foreach ($cepsBuscaArray as $cepBusca) {
            if (!$this->validarCep($cepBusca)){
                $ceps[]=array("cep"=>'Cep '.$cepBusca.' Invalido',"logradouro"=>'-',"complemento"=>'-',"bairro"=>'-',"localidade"=>'-',"uf"=>'-',"ibge"=>'-',"gia"=>'-',"ddd"=>'-',"siafi"=>'-');

            }
            else{
                $response = file_get_contents("https://viacep.com.br/ws/".$cepBusca."/json/");
                $cep = json_decode($response, true);
                if ( ( !( empty($cep['cep']) ) )  ) {
                    $ceps[]=array("cep"=>$cep['cep'],
                                            "logradouro"=>$cep['logradouro'],
                                            "complemento"=>$cep['complemento'],
                                            "bairro"=>$cep['bairro'],
                                            "localidade"=>$cep['localidade'],
                                            "uf"=>$cep['uf'],
                                            "ibge"=>$cep['ibge'],
                                            "gia"=>$cep['gia'],
                                            "ddd"=>$cep['ddd'],
                                            "siafi"=>$cep['siafi'] );

                }else{
                    $ceps[]=array("cep"=>'Cep '.$cepBusca.' Nao Encontrado',"logradouro"=>'-',"complemento"=>'-',"bairro"=>'-',"localidade"=>'-',"uf"=>'-',"ibge"=>'-',"gia"=>'-',"ddd"=>'-',"siafi"=>'-');
                }
            }

        }
        

        
        $CepsTabela=$cepsBusca;
        return view('ceps.index', ["ceps"=>$ceps],["CepsTabela"=>$CepsTabela]);
    }
    
}
