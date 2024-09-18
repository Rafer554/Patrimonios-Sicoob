<?php

class CadastraMovimentacaoForm extends TPage
{
    protected $form;
    private $formFields = [];
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'Movimentacao';
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
        $this->form->setFormTitle("CadastraMovimentação");

        $criteria_fk_patrimonioId_id = new TCriteria();
        $criteria_localAntigo = new TCriteria();
        $criteria_patrimonioId = new TCriteria();
        $criteria_localAtual = new TCriteria();

        $criteria_fk_patrimonioId_id->setProperty('order', 'descricao asc');

        $fk_patrimonioId_id = new TDBSeekButton('fk_patrimonioId_id', 'controlepatrimonio', self::$formName, 'Patrimonio', 'CodigodoPatrimonio', 'fk_patrimonioId_id', 'fk_patrimonioId_id_display', $criteria_fk_patrimonioId_id);
        $fk_patrimonioId_id_display = new TEntry('fk_patrimonioId_id_display');
        $pesquisaPatrimonioId = new TEntry('pesquisaPatrimonioId');
        $localAntigo = new TDBUniqueSearch('localAntigo', 'controlepatrimonio', 'Local', 'id', 'id','id asc' , $criteria_localAntigo );
        $patrimonioId = new TDBUniqueSearch('patrimonioId', 'controlepatrimonio', 'Patrimonio', 'id', 'CodigodoPatrimonio','CodigodoPatrimonio asc' , $criteria_patrimonioId );
        $dataInspecao = new TDate('dataInspecao');
        $localAtual = new TDBUniqueSearch('localAtual', 'controlepatrimonio', 'Local', 'id', 'Descricao','id asc' , $criteria_localAtual );
        $Descricao = new THtmlEditor('Descricao');
        $imagem = new TFile('imagem');

        $fk_patrimonioId_id->setExitAction(new TAction([$this,'alteraDados']));
        $pesquisaPatrimonioId->setExitAction(new TAction([$this,'PesquisaPatrimonio']));

        $localAntigo->addValidation("LocalAntigo", new TRequiredValidator()); 
        $patrimonioId->addValidation("PatrimonioId", new TRequiredValidator()); 
        $dataInspecao->addValidation("DataInspecao", new TRequiredValidator()); 

        $fk_patrimonioId_id->setDisplayMask('{descricao}');
        $fk_patrimonioId_id->setAuxiliar($fk_patrimonioId_id_display);
        $fk_patrimonioId_id_display->setEditable(false);
        $dataInspecao->setDatabaseMask('yyyy-mm-dd');
        $imagem->enableFileHandling();
        $localAtual->setMinLength(2);
        $localAntigo->setMinLength(2);
        $patrimonioId->setMinLength(2);

        $localAtual->setMask('{Descricao}');
        $localAntigo->setMask('{Descricao}');
        $dataInspecao->setMask('dd/mm/yyyy');
        $patrimonioId->setMask('{CodigodoPatrimonio}');

        $imagem->setSize('70%');
        $dataInspecao->setSize(110);
        $localAtual->setSize('70%');
        $localAntigo->setSize('70%');
        $patrimonioId->setSize('70%');
        $Descricao->setSize('70%', 110);
        $fk_patrimonioId_id->setSize(70);
        $pesquisaPatrimonioId->setSize('70%');
        $fk_patrimonioId_id_display->setSize(200);

        $row1 = $this->form->addFields([new TLabel("Patrimônio:", null, '14px', null)],[$fk_patrimonioId_id]);
        $row2 = $this->form->addFields([new TLabel("Código Patrimônio:", null, '14px', null)],[$pesquisaPatrimonioId]);
        $row3 = $this->form->addFields([new TLabel("------------------------------------------------------------------------------------------------------------------------------------------------", null, '14px', null, '100%')],[]);
        $row4 = $this->form->addFields([new TLabel("LocalAntigo:", '#ff0000', '14px', null)],[$localAntigo]);
        $row5 = $this->form->addFields([new TLabel("PatrimonioId:", '#ff0000', '14px', null)],[$patrimonioId]);
        $row6 = $this->form->addFields([new TLabel("DataInspecao:", '#ff0000', '14px', null)],[$dataInspecao]);
        $row7 = $this->form->addFields([new TLabel("Local Atual:", null, '14px', null)],[$localAtual]);
        $row8 = $this->form->addFields([new TLabel("Descricao:", null, '14px', null)],[$Descricao]);
        $row9 = $this->form->addFields([new TLabel("Imagem:", null, '14px', null)],[$imagem]);

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
            $container->add(TBreadCrumb::create(["Geral","CadastraMovimentação"]));
        }
        $container->add($this->form);

        parent::add($container);

    }

    public static function alteraDados($param = null) 
    {
        try 
        {
            //code here
              $data = $param['fk_patrimonioId_id']; 
            //  new TMessage('info',$data);
            TTransaction::open('controlepatrimonio');

           $patrimoniotemp= new Patrimonio($data);
           $obj = new StdClass;

           $obj->{'patrimonioId'}=$patrimoniotemp->id;
            $obj->{'localAntigo'}=$patrimoniotemp->Local_id;
            $obj->{'localAtual'}=$patrimoniotemp->Local_id;
            $obj->{'dataInspecao'}=date('d/m/Y');

        TForm::sendData('form_Movimentacao', $obj);

          // $DataInspecao=date('Y-m_d');
           //$patrimonioId=$patrimoniotemp->CodigodoPatrimonio;
          //  new TMessage('info', $patrimoniotemp->descricao);

            TTransaction::close();

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    public static function PesquisaPatrimonio($param = null) 
    {
        try 
        {
            //code here
         //code here
              $data = $param['pesquisaPatrimonioId']; 

            TTransaction::open('controlepatrimonio');
            $criterio= new TCriteria;
            $criterio->add(new TFilter('CodigodoPatrimonio','=',$data));

            $repositorio= new TRepository('Patrimonio');
            $resultados=$repositorio->load($criterio);

            foreach ($resultados as $result) {
                 $obj = new StdClass;

           $obj->{'patrimonioId'}=$result->id;
            $obj->{'localAntigo'}=$result->Local_id;
            $obj->{'localAtual'}=$result->Local_id;
            $obj->{'dataInspecao'}=date('d/m/Y');
            }

         //  $patrimoniotemp= new Patrimonio($data);

        TForm::sendData('form_Movimentacao', $obj);
        TTransaction::close();

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
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
             $patrimonio=new Patrimonio($object->patrimonioId);
             $patrimonio->Local_id=$param['localAtual'];
             $patrimonio->store();

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

                                $object->fk_patrimonioId_id = $object->fk_patrimonioId->id;

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

