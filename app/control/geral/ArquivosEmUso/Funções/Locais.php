<?php

use Adianti\Wrapper\BootstrapFormBuilder;

class Locais extends TPage{
    private $form;
    private $datagrid;
    private $filter_criteria;
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'local';
    private static $primaryKey = 'id';
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private $limit = 20;
    private static $formName = 'form_Local';
    const TABLENAME = 'locais';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';
    


    public function __construct($param){
        parent::__construct();

        $this->form = new BootstrapFormBuilder(self::$formName);

        $this->form->setFormTitle("Listagem de Locais");

        $criteria_CentrodeCusto_id = new TCriteria();
        $criteria_local = new TCriteria();

        $Local = new TEntry('Local');
            $Local->setSize('35%');


        $row1 = $this->form->addFields([new TLabel("Local:", null, '14px', null)],[$Local]);
        $row1->layout = ['col-sm-12'];
        


        $this->datagrid = new TDataGrid;
        $this->datagrid->disableHtmlConversion();
        $this->datagrid->setId(__CLASS__.'_datagrid');

        $this->datagrid_form = new TForm('datagrid_'.self::$formName);
        $this->datagrid_form->onsubmit = 'return false';

        $this->datagrid = new BootstrapDatagridWrapper($this->datagrid);
        $this->filter_criteria = new TCriteria;

        $this->datagrid->style = 'width: 100%';
        $this->datagrid->setHeight(320);

        $column_CodigodoPatrimonio = new TDataGridColumn('id', "ID ", 'left');
        $column_Descricao = new TDataGridColumn('Descricao', 'Descricao', 'left');
        $column_Local = new TDataGridColumn('Local', 'Local', 'left');
        $column_CentrodeCusto = new TDataGridColumn('CentrodeCusto_id', 'Centro de Custo', 'left');
        $column_responsavel = new TDataGridColumn('responsavel', 'Responsavel', 'left');
        $column_chapa = new TDataGridColumn('chapa', 'Chapa', 'left');
        
        //Sorter
        $order_CodigodoPatrimonio = new TAction(array($this, 'onReload'));
        $order_CodigodoPatrimonio->setParameter('order', 'id');
        $column_CodigodoPatrimonio->setAction($order_CodigodoPatrimonio);

        //Colunas
        $grid1 = $this->datagrid->addColumn($column_CodigodoPatrimonio);
        $grid2 = $this->datagrid->addColumn($column_Local);
        $grid3 = $this->datagrid->addColumn($column_Descricao);
        $grid4 = $this->datagrid->addColumn($column_CentrodeCusto);
        $grid5 = $this->datagrid->addColumn($column_responsavel);
        $grid6 = $this->datagrid->addColumn($column_chapa);
        
        //Actions

        $action_onEdit = new TDataGridAction(array('LocalForm', 'onEdit'));
        $action_onEdit->setUseButton(false);
        $action_onEdit->setButtonClass('btn btn-default btn-sm');
        $action_onEdit->setLabel("Editar");
        $action_onEdit->setImage('far:edit #478fca');
        $action_onEdit->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onEdit);


        $action_onDelete = new TDataGridAction(array('Locais', 'onDelete'));
        $action_onDelete->setUseButton(false);
        $action_onDelete->setButtonClass('btn btn-default btn-sm');
        $action_onDelete->setLabel("Excluir");
        $action_onDelete->setImage('far:trash-alt #dd5a43');
        $action_onDelete->setField(self::$primaryKey);

        $this->datagrid->addAction($action_onDelete);

        $this->datagrid->createModel();


        //Botões
        $btn_onsearch = $this->form->addAction("Buscar", new TAction([$this, 'onSearch']), 'fas:search #ffffff');
        $this->btn_onsearch = $btn_onsearch;
        $btn_onsearch->addStyleClass('btn-primary'); 

        $btn_onexportcsv = $this->form->addAction("Exportar como CSV", new TAction([$this, 'onExportCsv']), 'far:file-alt #000000');
        $this->btn_onexportcsv = $btn_onexportcsv;

        $btn_onshow = $this->form->addAction("Cadastrar", new TAction(['LocalForm', 'onShow']), 'fas:plus #69aa46');
        $this->btn_onshow = $btn_onshow;

            


        //Panel
        $panel = new TPanelGroup();
        $panel->datagrid = 'datagrid-container';
        $this->datagridPanel = $panel;
        $this->datagrid_form->add($this->datagrid);
        $panel->add($this->datagrid_form);

        //Container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
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
                $object = new Local($key, FALSE); 

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


    public function onReload($param = null) {
        try {
            TTransaction::open(self::$database);
            
            // Limpa os critérios de filtro
            $this->filter_criteria = new TCriteria;
    
            // Recupera os filtros da sessão
            if ($filters = TSession::getValue(__CLASS__.'_filters')) {
                foreach ($filters as $filter) {
                    $this->filter_criteria->add($filter);
                }
            }
    
            // Verifica se foi passado um parâmetro de ordem
            if (isset($param['order'])) {
                $order = $param['order'];
                // Alterna a direção da ordenação
                if (isset($param['direction']) && $param['direction'] == 'asc') {
                    $this->filter_criteria->setProperty('order', $order . ' DESC');
                    $param['direction'] = 'desc'; // Muda para decrescente na próxima chamada
                } else {
                    $this->filter_criteria->setProperty('order', $order . ' ASC');
                    $param['direction'] = 'asc'; // Muda para crescente na próxima chamada
                }
            }
    
            // Cria a nova consulta
            $repository = new TRepository('local');
            $objects = $repository->load($this->filter_criteria);
    
            // Limpa o DataGrid
            $this->datagrid->clear();
    
            // Preenche o DataGrid com os dados
            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }
            TTransaction::close();
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }
    
    

    public function onSearch($param = null) {
        // Obtem os dados do formulário
        $data = $this->form->getData();
        $filters = [];
    
        // Limpa os dados de filtro na sessão
        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);
    
        // Verifica se o campo 'Local' foi preenchido
        if (isset($data->Local) && ((is_scalar($data->Local) && $data->Local !== '') || (is_array($data->Local) && !empty($data->Local)))) {
            // Cria o filtro para o campo 'Local'
            $filters[] = new TFilter('Local', 'like', "%{$data->Local}%");
        }
    
        // Adiciona os filtros à sessão
        if ($filters) {
            TSession::setValue(__CLASS__.'_filters', $filters);
        }
    
        // Preenche o formulário com os dados
        $this->form->setData($data);
    
        // Chama o método onReload para atualizar o DataGrid
        $this->onReload(['offset' => 0, 'first_page' => 1]);
    }
    
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
  

    public static function manageRow($id)
    {
        $list = new self([]);

        $openTransaction = TTransaction::getDatabase() != self::$database ? true : false;

        if($openTransaction)
        {
            TTransaction::open(self::$database);    
        }

        $object = new Local($id);

        $row = $list->datagrid->addItem($object);
        $row->id = "row_{$object->id}";

        if($openTransaction)
        {
            TTransaction::close();    
        }

        TDataGrid::replaceRowById(__CLASS__.'_datagrid', $row->id, $row);
    }

} 