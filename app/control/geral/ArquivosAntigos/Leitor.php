<?php

class Leitor extends TPage

{
    //
   
    //
    public function __construct()
    {
        parent::__construct();
        
		
    }

    
    // função executa ao clicar no item de menu
    public function onShow($param)
		
		
    {
        try {
			
        if($_GET["patrimonio"]){
   // new TMessage('info',$_GET['patrimonio']);
            TTransaction::open('controlepatrimonio');
            
            ///
            $criterio= new TCriteria;
            $criterio->add(new TFilter('CodigodoPatrimonio','=',$_GET['patrimonio']));

            $repositorio= new TRepository('Patrimonio'); 
            $resultados=$repositorio->load($criterio);
               $movimentacao= new Movimentacao();

            foreach ($resultados as $result) {
            $movimentacao->localAntigo=$result->Local_id;
            $movimentacao->dataInspecao=date('Y-m-d');
            $movimentacao->patrimonioId=$result->id;
            }
            
            //
           
          
            $movimentacao->store();
            
            TTransaction::close();
            new TMessage('info',$result->descricao);
            
        }
        
        } catch (Exception $ex) {
            new TMessage('error',$ex);
            
        }
    
        
 ?>       
<!DOCTYPE html>
<html >



<body>
	
    <div id="resultado">
    
    </div>
    <div id="camera" style="width=100px; height=100px"></div>

    <script src="quaggamin.js"></script>

    <script>
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#camera')    // Or '#yourElement' (optional)
            },
            decoder: {
                //readers: ["code_128_reader"]
                readers:["i2of5_reader"]
            }
        }, function (err) {
            if (errd) {
                console.log(err);
                return
            }
            console.log("Initialization finished. Ready to start");
            Quagga.start();
        });
        Quagga.onDetected(function (data) {
            console.log(data.codeResult.code);
            document.querySelector('#resultado').innerText = data.codeResult.code;
			window.location.href = "index.php?class=Leitor&method=onShow&patrimonio=" + data.codeResult.code; 
            
        });
    </script>

</body>

</html>

<?php


       
	}
}