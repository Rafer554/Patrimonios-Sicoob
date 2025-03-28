<?php

class LocalForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'local';
    private static $primaryKey = 'id';
    private static $formName = 'form_Local';

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
        $this->form->setFormTitle("Cadastro de local");

        $criteria_CentrodeCusto_id = new TCriteria();

        $id = new TEntry('id');
        $Descricao = new TEntry('Descricao');
        $CentrodeCusto_id = new TDBCombo('CentrodeCusto_id', 'controlepatrimonio', 'Centrodecusto', 'id', '{CentroCusto}','CentroCusto asc' , $criteria_CentrodeCusto_id );
        $Local = new TEntry('Local');
        $responsavel = new TEntry('responsavel');
        $chapa = new TEntry('chapa');

        $CentrodeCusto_id->addValidation("CentrodeCusto id", new TRequiredValidator()); 

        $id->setEditable(false);
        $id->setSize(100);
        $Local->setSize('70%');
        $chapa->setSize('70%');
        $Descricao->setSize('70%');
        $responsavel->setSize('70%');
        $CentrodeCusto_id->setSize('70%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id]);
        $row1->layout = ['col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Descrição:", null, '14px', null, '100%'),$Descricao]);
        $row2->layout = ['col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("Centro de Custo", '#ff0000', '14px', null, '100%'),$CentrodeCusto_id]);
        $row3->layout = ['col-sm-12'];

        $row4 = $this->form->addFields([new TLabel("Local:", null, '14px', null, '100%'),$Local]);
        $row4->layout = ['col-sm-12'];

        $row5 = $this->form->addFields([new TLabel("Responsavel:", null, '14px', null, '100%'),$responsavel]);
        $row5->layout = ['col-sm-12'];

        $row6 = $this->form->addFields([new TLabel("Chapa:", null, '14px', null, '100%'),$chapa]);
        $row6->layout = ['col-sm-12'];

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
            $container->add(TBreadCrumb::create(["Geral","Cadastro de local"]));
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

            $object = new Local(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $object->store(); // save the object 

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

                $object = new Local($key); // instantiates the Active Record 

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

