<x-layout title="Buscar Ceps">
   <form action="/ceps/escolher" method="post">
      @csrf
      <span>Para buscar mais de um cep, separe-os por ; ex:(00000-000;00000000;00000-000)</span>
      <br>
      <label for="cepBusca" >CEP:</label>
      <input style="width:180px" type="text" name="cepBusca" id="cepBusca" value="" placeholder="NNNNNNNN ou NNNNN-NNN">
      <button id="buscar" name="submitbutton"   type="submit" value="buscar" >Clique para buscar o cep</button>
      <button id="exportar" name="submitbutton" type="submit" value="csv">Clique para exportar CSV da tabela </button>
      <button id="limpar" name="submitbutton" type="submit" value="limpar">Limpar Tabela</button>
      <div id="Cep"> 
         @csrf
         @if(isset($ceps) && (sizeof($ceps)==0))
            <input type="hidden" id="mensagemTabelaVazia" name="mensagemTabelaVazia" value="tabelavazia">
         @endif
         @if(isset($MensagemCepVazio)) 
            {{$MensagemCepVazio}}
            <input type="hidden" id="mensagemCepVazio" name="mensagemCepVazio" value="{{$MensagemCepVazio}}">
         @endif
         @if(isset($ceps))   
            <table>
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
               </tr>

                  @foreach ($ceps as $cep )
                  
                     <tr class='tabelaBorda' >
                        <td class='tabelaBorda' > {{ $cep['cep'] }} </td>
                        <td class='tabelaBorda' > {{ $cep['logradouro'] }} </td>
                        <td class='tabelaBorda' > {{ $cep['complemento'] }} </td>
                        <td class='tabelaBorda' > {{ $cep['bairro'] }} </td>
                        <td class='tabelaBorda' > {{ $cep['localidade'] }} </td>
                        <td class='tabelaBorda' > {{ $cep['uf'] }} </td>
                        <td class='tabelaBorda' > {{ $cep['ibge'] }} </td>
                        <td class='tabelaBorda' > {{ $cep['gia'] }} </td>
                        <td class='tabelaBorda' > {{ $cep['ddd'] }} </td>
                        <td class='tabelaBorda' > {{ $cep['siafi'] }} </td>
                      </tr>
                   @endforeach
               </table>
               @if(isset($CepsTabela))
                 <input type="hidden" id="cepsTabela" name="cepsTabela" value="{{$CepsTabela}}">
               @endif
         @endif
      </div>
   </form>
</x-layout>





 
