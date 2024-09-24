<?php

class MovimentacaodepreciacaoForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'movimentacaodepreciacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_Movimentacaodepreciacao';

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
        $this->form->setFormTitle("Cadastro de movimentacaodepreciacao");

        $criteria_patrimonioId = new TCriteria();

        $id = new TEntry('id');
        $patrimonioId = new TDBCombo('patrimonioId', 'controlepatrimonio', 'Patrimonio', 'id', '{CodigodoPatrimonio}','CodigodoPatrimonio asc' , $criteria_patrimonioId );
        $dataDepreciacao = new TEntry('dataDepreciacao');
        $valor = new TNumeric('valor', '9', ',', '.' );

        $patrimonioId->addValidation("PatrimonioId", new TRequiredValidator()); 
        $dataDepreciacao->addValidation("DataDepreciacao", new TRequiredValidator()); 
        $valor->addValidation("Valor", new TRequiredValidator()); 

        $id->setEditable(false);
        $id->setSize(100);
        $valor->setSize('70%');
        $patrimonioId->setSize('70%');
        $dataDepreciacao->setSize('70%');

        $row1 = $this->form->addFields([new TLabel("ID:", null, '14px', null, '100%'),$id]);
        $row1->layout = ['col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("Patrimonio:", '#ff0000', '14px', null, '100%'),$patrimonioId]);
        $row2->layout = ['col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("Data da Depreciacao:", '#ff0000', '14px', null, '100%'),$dataDepreciacao]);
        $row3->layout = ['col-sm-12'];

        $row4 = $this->form->addFields([new TLabel("Valor:", '#ff0000', '14px', null, '100%'),$valor]);
        $row4->layout = ['col-sm-12'];

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
            $container->add(TBreadCrumb::create(["Geral","Cadastro de movimentacaodepreciacao"]));
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

            $object = new Movimentacaodepreciacao(); // create an empty object 

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

                $object = new Movimentacaodepreciacao($key); // instantiates the Active Record 

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

