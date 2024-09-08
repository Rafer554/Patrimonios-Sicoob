<?php

class TesteControle extends TPage
{
    public function __construct($param)
    {
        parent::__construct();
     $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro Teste");
        
    }
    
    // função executa ao clicar no item de menu
    public function onShow($param = null)
    
    {
        try {
            TTransaction::open(self::$database);
            $patrimonio = new Patrimonio;
            //$patrimonio->descricao='TesteControle';
            TTransaction::close();
            
        } catch (Exception $e ) {
            
            new TMessage('error', $e->getMessage());
        }
        
    }
}
