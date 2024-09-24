<?php

class LocalFormView extends TPage
{
    protected $form; // form
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'local';
    private static $primaryKey = 'id';
    private static $formName = 'formView_Local';

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        TTransaction::open(self::$database);
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setTagName('div');

        $Local = new Local($param['key']);
        // define the form title
        $this->form->setFormTitle("LeitorCodigo");

        $label1 = new TLabel("Id:", '', '12px', '');
        $text1 = new TTextDisplay($Local->id, '', '12px', '');
        $label2 = new TLabel("Descrição:", '', '12px', '');
        $text2 = new TTextDisplay($Local->Descricao, '', '12px', '');
        $label3 = new TLabel("CentrodeCusto id:", '', '12px', '');
        $text3 = new TTextDisplay($Local->CentrodeCusto->CentroCusto, '', '12px', '');
        $label4 = new TLabel("Local:", '', '12px', '');
        $text4 = new TTextDisplay($Local->Local, '', '12px', '');
        $label5 = new TLabel("Responsavel:", '', '12px', '');
        $text5 = new TTextDisplay($Local->responsavel, '', '12px', '');
        $label6 = new TLabel("Chapa:", '', '12px', '');
        $text6 = new TTextDisplay($Local->chapa, '', '12px', '');

        $row1 = $this->form->addFields([$label1],[$text1]);
        $row2 = $this->form->addFields([$label2],[$text2]);
        $row3 = $this->form->addFields([$label3],[$text3]);
        $row4 = $this->form->addFields([$label4],[$text4]);
        $row5 = $this->form->addFields([$label5],[$text5]);
        $row6 = $this->form->addFields([$label6],[$text6]);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Geral","LeitorCodigo"]));
        }
        $container->add($this->form);

        TTransaction::close();
        parent::add($container);

    }

    public function onShow($param = null)
    {     

    }

}

