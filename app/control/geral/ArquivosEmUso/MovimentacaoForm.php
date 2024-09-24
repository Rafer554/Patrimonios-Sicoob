<?php

class MovimentacaoForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'movimentacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_Movimentacao';

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
        $this->form->setFormTitle("Cadastro de movimentacao");

        $criteria_localAntigo = new TCriteria();
        $criteria_patrimonioId = new TCriteria();

        $id = new TEntry('id');
        $localAntigo = new TDBCombo('localAntigo', 'controlepatrimonio', 'Local', 'id', '{Descricao}','id asc' , $criteria_localAntigo );
        $patrimonioId = new TDBCombo('patrimonioId', 'controlepatrimonio', 'Patrimonio', 'id', '{CodigodoPatrimonio}','CodigodoPatrimonio asc' , $criteria_patrimonioId );
        $dataInspecao = new TDate('dataInspecao');
        $Descricao = new THtmlEditor('Descricao');
        $imagem = new TFile('imagem');

        $localAntigo->addValidation("LocalAntigo", new TRequiredValidator()); 
        $patrimonioId->addValidation("PatrimonioId", new TRequiredValidator()); 
        $dataInspecao->addValidation("DataInspecao", new TRequiredValidator()); 

        $id->setEditable(false);
        $dataInspecao->setMask('dd/mm/yyyy');
        $dataInspecao->setDatabaseMask('yyyy-mm-dd');
        $imagem->enableFileHandling();
        $imagem->setAllowedExtensions(["jpeg","png","gif","jpg"]);
        $id->setSize(100);
        $imagem->setSize('70%');
        $dataInspecao->setSize(110);
        $localAntigo->setSize('70%');
        $patrimonioId->setSize('70%');
        $Descricao->setSize('70%', 110);

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null)],[$id]);
        $row2 = $this->form->addFields([new TLabel("LocalAntigo:", '#ff0000', '14px', null)],[$localAntigo]);
        $row3 = $this->form->addFields([new TLabel("PatrimonioId:", '#ff0000', '14px', null)],[$patrimonioId]);
        $row4 = $this->form->addFields([new TLabel("DataInspecao:", '#ff0000', '14px', null)],[$dataInspecao]);
        $row5 = $this->form->addFields([new TLabel("Descricao:", null, '14px', null)],[$Descricao]);
        $row6 = $this->form->addFields([new TLabel("Imagem:", null, '14px', null)],[$imagem]);

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
            $container->add(TBreadCrumb::create(["Geral","Cadastro de movimentacao"]));
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

            $object = new Movimentacao(); // create an empty object 

            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data

            $imagem_dir = 'imagens'; 

            $object->store(); // save the object 

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

                $object = new Movimentacao($key); // instantiates the Active Record 

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

