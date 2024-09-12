<?php

class MovimentacaodepreciacaoList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $loaded;
    private $filter_criteria;
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'Movimentacaodepreciacao';
    private static $primaryKey = 'id';
    private static $formName = 'formList_Movimentacaodepreciacao';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct($param = null)
    {
        parent::__construct();

        if(!empty($param['target_container']))
        {
            $this->adianti_target_container = $param['target_container'];
        }

        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Listagem movimentacao depreciação");
        $this->limit = 20;

        $criteria_patrimonioId = new TCriteria();

        $id = new TEntry('id');
        $patrimonioId = new TDBCombo('patrimonioId', 'controlepatrimonio', 'Patrimonio', 'id', '{CodigodoPatrimonio}','CodigodoPatrimonio asc' , $criteria_patrimonioId );
        $dataDepreciacao = new TEntry('dataDepreciacao');
        $valor = new TNumeric('valor', '2', ',', '.' );


        $id->setSize(100);
        $valor->setSize('70%');
        $patrimonioId->setSize('70%');
        $dataDepreciacao->setSize('70%');

        $row1 = $this->form->addFields([new TLabel("Id:", null, '14px', null, '100%'),$id]);
        $row1->layout = ['col-sm-12'];

        $row2 = $this->form->addFields([new TLabel("PatrimonioId:", null, '14px', null, '100%'),$patrimonioId]);
        $row2->layout = ['col-sm-12'];

        $row3 = $this->form->addFields([new TLabel("DataDepreciacao:", null, '14px', null, '100%'),$dataDepreciacao]);
        $row3->layout = ['col-sm-12'];

        $row4 = $this->form->addFields([new TLabel("Valor:", null, '14px', null, '100%'),$valor]);
        $row4->layout = ['col-sm-12'];

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        $btn_onexportcsv = $this->form->addAction("Exportar como CSV", new TAction([$this, 'onExportCsv']), 'far:file-alt #000000');
        $this->btn_onexportcsv = $btn_onexportcsv;

        $btn_onshow = $this->form->addAction("Cadastrar", new TAction(['MovimentacaodepreciacaoForm', 'onShow']), 'fas:plus #69aa46');
        $this->btn_onshow = $btn_onshow;

        $btn_onaction = $this->form->addAction("Rodar Depreciassão", new TAction([$this, 'onAction']), 'far:circle #000000');
        $this->btn_onaction = $btn_onaction;

        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_id = new TDataGridColumn('id', "Id", 'center' , '70px');
        $column_fk_patrimonioId_CodigodoPatrimonio = new TDataGridColumn('fk_patrimonioId->CodigodoPatrimonio', "PatrimonioId", 'left');
        $column_dataDepreciacao = new TDataGridColumn('dataDepreciacao', "DataDepreciacao", 'left');
        $column_valor = new TDataGridColumn('valor', "Valor", 'left');

        $order_id = new TAction(array($this, 'onReload'));
        $order_id->setParameter('order', 'id');
        $column_id->setAction($order_id);

        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_fk_patrimonioId_CodigodoPatrimonio);
        $this->datagrid->addColumn($column_dataDepreciacao);
        $this->datagrid->addColumn($column_valor);

        $action_onEdit = new TDataGridAction(array('MovimentacaodepreciacaoForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);

        $action_onDelete = new TDataGridAction(array('MovimentacaodepreciacaoList', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('far:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->enableCounters();
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        $panel->addFooter($this->pageNavigation);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        if(empty($param['target_container']))
        {
            $container->add(TBreadCrumb::create(["Geral","Movimentacaodepreciacaos"]));
        }
        $container->add($this->form);
        $container->add($panel);

        parent::add($container);

    }

    public function onDelete($param = null) 
    { 
        if(isset($param['delete']) && $param['delete'] == 1)
        {
            try
            {
                // get the paramseter $key
                $key = $param['key'];
                // open a transaction with database
                TTransaction::open(self::$database);

                // instantiates object
                $object = new Movimentacaodepreciacao($key, FALSE); 

                // deletes the object from the database
                $object->delete();

                // close the transaction
                TTransaction::close();

                // reload the listing
                $this->onReload( $param );
                // shows the success message
                new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
            }
            catch (Exception $e) // in case of exception
            {
                // shows the exception error message
                new TMessage('error', $e->getMessage());
                // undo all pending operations
                TTransaction::rollback();
            }
        }
        else
        {
            // define the delete action
            $action = new TAction(array($this, 'onDelete'));
            $action->setParameters($param); // pass the key paramseter ahead
            $action->setParameter('delete', 1);
            // shows a dialog to the user
            new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);   
        }
    }
    public function onExportCsv($param = null) 
    {
        try
        {
            $this->onSearch();

            TTransaction::open(self::$database); // open a transaction
            $repository = new TRepository(self::$activeRecord); // creates a repository for Customer
            $criteria = $this->filter_criteria;

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            $records = $repository->load($criteria); // load the objects according to criteria
            if ($records)
            {
                $file = 'tmp/'.uniqid().'.csv';
                $handle = fopen($file, 'w');
                $columns = $this->datagrid->getColumns();

                $csvColumns = [];
                foreach($columns as $column)
                {
                    $csvColumns[] = $column->getLabel();
                }
                fputcsv($handle, $csvColumns, ';');

                foreach ($records as $record)
                {
                    $csvColumns = [];
                    foreach($columns as $column)
                    {
                        $name = $column->getName();
                        $csvColumns[] = $record->{$name};
                    }
                    fputcsv($handle, $csvColumns, ';');
                }
                fclose($handle);

                TPage::openFile($file);
            }
            else
            {
                new TMessage('info', _t('No records found'));       
            }

            TTransaction::close(); // close the transaction
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    public function onAction($param = null) 
    {
        try 
        {

        $form1 = new TQuickForm('input_form');
        $form1->style = 'padding:20px';

        $datadepreciacaoVerifica = new TDate('data');

       $form1->addQuickField('datadepreciacaoVerifica', $datadepreciacaoVerifica);

        $form1->addQuickAction('Gerar', new TAction(array($this, 'onConfirm')), 'fa:save green');

        // show the input dialog
        new TInputDialog('Data de Depreciação', $form1);

          /*   TTransaction::open('controlepatrimonio');
            $criterio= new TCriteria;
            $criterio->add(new TFilter('ativo','=',1));
            $criterio->add(new TFilter('ValorAtual','>',0));

            $repositorio= new TRepository('Patrimonio');
            $resultados=$repositorio->load($criterio);

            foreach ( $resultados as $result) {
                $depreciassao= new Movimentacaodepreciacao;
                $depreciassao->patrimonioId=$result->id;
                $depreciassao->dataDepreciacao=date('Y-m-d');
                if($result->ValorAtual-($result->ValorOriginal*$result->Grupo->valorDepreciacao)<0)
                    $depreciassao->valor=0;
                else
                    $depreciassao->valor=$result->ValorAtual-($result->ValorOriginal*$result->Grupo->valorDepreciacao);
                $depreciassao->store();
                $result->ValorAtual=$depreciassao->valor;
                $result->store();
                 new TMessage('mensage','Depreciação Gerada com Sucesso');
                // code...
            }
            //$patrimonio->descricao='TesteControle';
            TTransaction::close();
            */

        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage());    
        }
    }

    /**
     * Register the filter in the session
     */
    public function onSearch($param = null)
    {
        $data = $this->form->getData();
        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('id', '=', $data->id);// create the filter 
        }

        if (isset($data->patrimonioId) AND ( (is_scalar($data->patrimonioId) AND $data->patrimonioId !== '') OR (is_array($data->patrimonioId) AND (!empty($data->patrimonioId)) )) )
        {

            $filters[] = new TFilter('patrimonioId', '=', $data->patrimonioId);// create the filter 
        }

        if (isset($data->dataDepreciacao) AND ( (is_scalar($data->dataDepreciacao) AND $data->dataDepreciacao !== '') OR (is_array($data->dataDepreciacao) AND (!empty($data->dataDepreciacao)) )) )
        {

            $filters[] = new TFilter('dataDepreciacao', '=', $data->dataDepreciacao);// create the filter 
        }

        if (isset($data->valor) AND ( (is_scalar($data->valor) AND $data->valor !== '') OR (is_array($data->valor) AND (!empty($data->valor)) )) )
        {

            $filters[] = new TFilter('valor', '=', $data->valor);// create the filter 
        }

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);
        TSession::setValue(__CLASS__.'_filters', $filters);

        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }

    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'controlepatrimonio'
            TTransaction::open(self::$database);

            // creates a repository for Movimentacaodepreciacao
            $repository = new TRepository(self::$activeRecord);

            $criteria = clone $this->filter_criteria;

            if (empty($param['order']))
            {
                $param['order'] = 'id';    
            }

            if (empty($param['direction']))
            {
                $param['direction'] = 'desc';
            }

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $this->limit);

            if($filters = TSession::getValue(__CLASS__.'_filters'))
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {

                    $row = $this->datagrid->addItem($object);
                    $row->id = "row_{$object->id}";

                }
            }

            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($this->limit); // limit

            // close the transaction
            TTransaction::close();
            $this->loaded = true;

            return $objects;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }

    public function onShow($param = null)
    {

    }

    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  $this->showMethods))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }

    public static function manageRow($id)
    {
        $list = new self([]);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Movimentacaodepreciacao($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

  public  function onConfirm( $param )
    {
        $valor=$param['data'];

        /*$dados=json_encode($param);

        $obj = json_decode($dados);

        foreach($obj as $key=>$value){

        if($key=='data')
                $valor=$value;
        }*/

        try {
            TTransaction::open('controlepatrimonio');
            $criterio= new TCriteria;
            $criterio->add(new TFilter('ativo','=',1));
            $criterio->add(new TFilter('ValorAtual','>',0));
            $criterio->add(new TFilter('dataentrada','<=',$valor));

            $repositorio= new TRepository('Patrimonio');
            $resultados=$repositorio->load($criterio);

            foreach ( $resultados as $result) {
                $depreciassao= new Movimentacaodepreciacao;
                $depreciassao->patrimonioId=$result->id;
                $depreciassao->dataDepreciacao=$valor;
                if($result->ValorAtual-($result->ValorOriginal*$result->Grupo->valorDepreciacao)<0)
                    $depreciassao->valor=0;
                else
                    $depreciassao->valor=$result->ValorAtual-($result->ValorOriginal*$result->Grupo->valorDepreciacao);
                $depreciassao->store();
                $result->ValorAtual=$depreciassao->valor;
                $result->store();
                 new TMessage('info','Depreciação Gerada com Sucesso');
                // code...
            }
            //$patrimonio->descricao='TesteControle';
            TTransaction::close();

        } catch (Exception  $ex) {
            new TMessage('error', $ex);
        }
    }

}

