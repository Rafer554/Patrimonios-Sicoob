<?php

class Teste2 extends TPage
{
    public function __construct($param)
    {
        parent::__construct();
    }
    
    // função executa ao clicar no item de menu
    public function onShow($param = null)
    {
        try {
            $inicio = new DateTime('1995-01-01');
            $fim = new DateTime('2019-06-01');
            $intevalo = new DateInterval('P1M');
            $periodo = new DatePeriod($inicio,$intevalo,$fim);
            foreach($periodo as $dataGerada){
                new TMessage('info',$dataGerada->format('Y-m-d'));
                
            TTransaction::open('controlepatrimonio');
            $criterio= new TCriteria;
            $criterio->add(new TFilter('ativo','=',1));
            $criterio->add(new TFilter('ValorAtual','>',0));
            $criterio->add(new TFilter('dataentrada','<=',$dataGerada->format('Y-m-d')));

            $repositorio= new TRepository('Patrimonio');
            $resultados=$repositorio->load($criterio);

            foreach ( $resultados as $result) {
                $depreciassao= new Movimentacaodepreciacao;
                $depreciassao->patrimonioId=$result->id;
                $depreciassao->dataDepreciacao=$dataGerada->format('Y-m-d');
                if($result->ValorAtual-($result->ValorOriginal*$result->Grupo->valorDepreciacao)<0)
                    $depreciassao->valor=0;
                else
                    $depreciassao->valor=$result->ValorAtual-($result->ValorOriginal*$result->Grupo->valorDepreciacao);
                $depreciassao->store();
                $result->ValorAtual=$depreciassao->valor;
                $result->store();
                
                // code...
            }
            //$patrimonio->descricao='TesteControle';
            TTransaction::close();
            new TMessage('info','Depreciação Gerada com Sucesso');
        
            }
            
        } catch (Exception  $ex) {
            new TMessage('error', $ex);
        
        }
         
        }
        
    
}
