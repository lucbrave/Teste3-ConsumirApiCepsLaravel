   <x-layout title="Cep">
  
  
   <form action="/ceps/consultar" method="post">
      @csrf
      CEP: <input type="text" name="cepBusca" id="cepBusca" value="">
      <button id="buscar" name="submitbutton"   type="submit" value="busca" >Clique para buscar o cep</button>
      <button id="exportar" name="submitbutton" type="submit" value="csv">Clique exportar CSV</button>
      <button id="limpar" name="submitbutton" type="submit" value="limpar">Limpar Tabela</button>
   </form>
   
   

   <div id="Cep">    
   </div>
   <div id="Endereco">
   </div>

  

 </x-layout>





 
