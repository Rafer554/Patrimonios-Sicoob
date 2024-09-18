<?php

use Adianti\Wrapper\BootstrapFormBuilder;

class Locais extends TPage{
    protected $form;
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'Local';
    private static $primaryKey = 'id';
    private static $formName = 'form_Local';


    public function __construct($param){
        parent::__construct();

        $this->form = new BootstrapFormBuilder(self::$formName);

        $this->form->setFormTitle("Listagem de Locais");

        $criteria_CentrodeCusto_id = new TCriteria();

        $id = new TEntry('id');
            $id->setSize(100);

        $descricao = new TEntry('descricao');
            $descricao->setSize(100);
            

        $row1 = $this->form->addFields([new TLabel("ID:", null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel("Descrição:", null, '14px', null)], [$descricao]);
        $row2 ->layout = ['col-sm-12'];
        
        
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add($this->form);

        parent::add($container);

    }


}