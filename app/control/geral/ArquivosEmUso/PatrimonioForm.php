<?php

class PatrimonioForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'patrimonio';
    private static $primaryKey = 'id';
    private static $formName = 'form_Patrimonio';

    use Adianti\Base\AdiantiFileSaveTrait;

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

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        // define the form title
        $this->form->setFormTitle("Cadastro de patrimonio");

        $criteria_Local_id = new TCriteria();
        $criteria_Grupo_id = new TCriteria();
        $criteria_tido_baixa_id = new TCriteria();

        $id = new TEntry('id');
        $CodigodoPatrimonio = new TEntry('CodigodoPatrimonio');
        $ativo = new TRadioGroup('ativo');
        $descricao = new TEntry('descricao');
        $responsavel = new TEntry('responsavel');
        $chapa = new TEntry('chapa');
        $Local_id = new TDBCombo('Local_id', 'controlepatrimonio', 'Local', 'id', '{Descricao}','id asc' , $criteria_Local_id );
        $Grupo_id = new TDBCombo('Grupo_id', 'controlepatrimonio', 'Grupo', 'id', '{id}','id asc' , $criteria_Grupo_id );
        $tido_baixa_id = new TDBCombo('tido_baixa_id', 'controlepatrimonio', 'TipoBaixa', 'id', '{Descricao}','id asc' , $criteria_tido_baixa_id );
        $DataEntrada = new TDate('DataEntrada');
        $ValorOriginal = new TNumeric('ValorOriginal', '2', ',', '.' );
        $ValorAtual = new TNumeric('ValorAtual', '2', ',', '.' );
        $imagem = new TFile('imagem');
        $visualizacaoImagem = new TImage('');

        $descricao->addValidation("Descrição", new TRequiredValidator()); 
        $Local_id->addValidation("Local id", new TRequiredValidator()); 
        $Grupo_id->addValidation("Grupo id", new TRequiredValidator()); 

        $id->setEditable(false);
        $ativo->addItems(["1"=>"Sim","2"=>"Não"]);
        $ativo->setLayout('horizontal');
        $ativo->setBooleanMode();
        $DataEntrada->setMask('dd/mm/yyyy');
        $DataEntrada->setDatabaseMask('yyyy-mm-dd');
        $imagem->enableFileHandling();
        $imagem->setAllowedExtensions(["jpg","jpeg","png","gif"]);
        $id->setSize(100);
        $ativo->setSize(80);
        $chapa->setSize('70%');
        $imagem->setSize('70%');
        $Local_id->setSize('70%');
        $Grupo_id->setSize('70%');
        $descricao->setSize('70%');
        $DataEntrada->setSize(490);
        $ValorAtual->setSize('70%');
        $responsavel->setSize('70%');
        $tido_baixa_id->setSize('70%');
        $ValorOriginal->setSize('69%');
        $CodigodoPatrimonio->setSize('70%');

        $visualizacaoImagem->width = '180px';
        $visualizacaoImagem->height = '180px';

        $this->visualizacaoImagem = $visualizacaoImagem;

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id]);
        $row1->layout = ['col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Código do Patrimônio:", null, '14px', null, '100%'),$CodigodoPatrimonio]);
        $row2->layout = ['col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("Ativo:", null, '14px', null, '100%'),$ativo]);
        $row3->layout = ['col-sm-12'];

        $row4 = $this->form->addFields([new TLabel("Descrição:", '#ff0000', '14px', null, '100%'),$descricao]);
        $row4->layout = ['col-sm-12'];

        $row5 = $this->form->addFields([new TLabel("Responsavel:", '#131111', '14px', null, '100%'),$responsavel]);
        $row5->layout = ['col-sm-12'];

        $row6 = $this->form->addFields([new TLabel("Chapa:", null, '14px', null, '100%'),$chapa]);
        $row6->layout = ['col-sm-12'];

        $row7 = $this->form->addFields([new TLabel("Local id:", '#ff0000', '14px', null, '100%'),$Local_id]);
        $row7->layout = ['col-sm-12'];

        $row8 = $this->form->addFields([new TLabel("Grupo id:", '#ff0000', '14px', null, '100%'),$Grupo_id]);
        $row8->layout = ['col-sm-12'];

        $row9 = $this->form->addFields([new TLabel("Tipo Baixa :", null, '14px', null),new TLabel(" ", null, '14px', null),$tido_baixa_id]);
        $row9->layout = ['col-sm-6'];

        $row10 = $this->form->addFields([new TLabel("Data Entrada:", null, '14px', null),$DataEntrada]);
        $row10->layout = ['col-sm-3'];

        $row11 = $this->form->addFields([new TLabel("ValorOriginal:", null, '14px', null, '100%'),$ValorOriginal]);
        $row11->layout = ['col-sm-12'];

        $row12 = $this->form->addFields([new TLabel("ValorAtual:", null, '14px', null, '100%'),$ValorAtual]);
        $row12->layout = ['col-sm-12'];

        $row13 = $this->form->addFields([new TLabel("Imagem:", null, '14px', null, '100%'),$imagem]);
        $row13->layout = ['col-sm-12'];

        $row14 = $this->form->addFields([$visualizacaoImagem]);
        $row14->layout = ['col-sm-6'];

        // create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'far:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Geral","Cadastro de patrimonio"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public function onSave($param = null) 
    {
        try
        {
            TTransaction::open(self::$database); // open a transaction

            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/

            $messageAction = null;

            $this->form->validate(); // validate form data

            $object = new Patrimonio(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

                if($object->ativo==true)
                     $object->ativo=1;
                else {
                    $object->ativo=0;
                }

            $imagem_dir = 'imagens';  

            $object->store(); // save the object 

            $this->visualizacaoImagem->src = $object->imagem;
            $this->saveFile($object, $data, 'imagem', $imagem_dir); 

            // get the generated {PRIMARY_KEY}
            $data->id = $object->id; 

            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction

            /**
            // To define an action to be executed on the message close event:
            $messageAction = new TAction(['className', 'methodName']);
            **/

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $messageAction);

        }
        catch (Exception $e) // in case of exception
        {
            //</catchAutoCode> 

            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open(self::$database); // open a transaction

                $object = new Patrimonio($key); // instantiates the Active Record 

                $this->visualizacaoImagem->src = $object->imagem; 

                $this->form->setData($object); // fill the form 

                TTransaction::close(); // close the transaction 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }

    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(true);

    }

    public function onShow($param = null)
    {

    } 

    public static function getFormName()
    {
        return self::$formName;
    }

}

