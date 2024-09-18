<?php

use Adianti\Widget\Wrapper\TDBCombo;

class MovimentacaodepreciacaoReport extends TPage
{
    private $form; // form
    private $loaded;
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'Movimentacaodepreciacao';
    private static $primaryKey = 'id';
    private static $formName = 'formReport_Movimentacaodepreciacao';

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
		
	
        // creates the form
        $this->form = new BootstrapFormBuilder(self::$formName);

        // define the form title
        $this->form->setFormTitle("Relatório depreciação ");

        $criteria_patrimonioId = new TCriteria();
        $criteria_id = new TCriteria();

        $patrimonioId = new TDBCombo('patrimonioId', 'controlepatrimonio', 'Patrimonio', 'id', '{CodigodoPatrimonio}','CodigodoPatrimonio asc' , $criteria_patrimonioId );
        $dataDepreciacao = new TDate('dataDepreciacao');
        $id = new TDBCombo('id', 'controlepatrimonio', 'Patrimonio', 'id', 'CodigodoPatrimonio asc');

        $dataDepreciacao->setMask('dd/mm/yyyy');
        $dataDepreciacao->setDatabaseMask('yyyy-mm-dd');
        $id->setSize('70%');
        $patrimonioId->setSize('70%');
        $dataDepreciacao->setSize('70%');

        $row1 = $this->form->addFields([new TLabel("PatrimonioId:", null, '14px', null)],[$patrimonioId]);
        $row2 = $this->form->addFields([new TLabel("DataDepreciacao:", null, '14px', null)],[$dataDepreciacao]);
        $row3 = $this->form->addFields([new TLabel("Rótulo:", null, '14px', null)],[$id]);

        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );

        $btn_ongeneratehtml = $this->form->addAction("Gerar HTML", new TAction([$this, 'onGenerateHtml']), 'fas:code #ffffff');
        $this->btn_ongeneratehtml = $btn_ongeneratehtml;
        $btn_ongeneratehtml->addStyleClass('btn-primary'); 

        $btn_ongeneratepdf = $this->form->addAction("Gerar PDF", new TAction([$this, 'onGeneratePdf']), 'far:file-pdf #d44734');
        $this->btn_ongeneratepdf = $btn_ongeneratepdf;

        $btn_ongeneratexls = $this->form->addAction("Gerar XLS", new TAction([$this, 'onGenerateXls']), 'far:file-excel #00a65a');
        $this->btn_ongeneratexls = $btn_ongeneratexls;

        $btn_ongeneratertf = $this->form->addAction("Gerar RTF", new TAction([$this, 'onGenerateRtf']), 'far:file-alt #324bcc');
        $this->btn_ongeneratertf = $btn_ongeneratertf;

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(TBreadCrumb::create(["Geral","Relatório depreciação "]));
        $container->add($this->form);

        parent::add($container);

    }

    public function onGenerateHtml($param = null) 
    {
        $this->onGenerate('html');
    }
    public function onGeneratePdf($param = null) 
    {
        $this->onGenerate('pdf');
    }
    public function onGenerateXls($param = null) 
    {
        $this->onGenerate('xls');
    }
    public function onGenerateRtf($param = null) 
    {
        $this->onGenerate('rtf');
    }

    /**
     * Register the filter in the session
     */
    public function getFilters()
    {
        // get the search form data
        $data = $this->form->getData();

        $filters = [];

        TSession::setValue(__CLASS__.'_filter_data', NULL);
        TSession::setValue(__CLASS__.'_filters', NULL);

        if (isset($data->patrimonioId) AND ( (is_scalar($data->patrimonioId) AND $data->patrimonioId !== '') OR (is_array($data->patrimonioId) AND (!empty($data->patrimonioId)) )) )
        {

            $filters[] = new TFilter('patrimonioId', '=', $data->patrimonioId);// create the filter 
        }
        if (isset($data->dataDepreciacao) AND ( (is_scalar($data->dataDepreciacao) AND $data->dataDepreciacao !== '') OR (is_array($data->dataDepreciacao) AND (!empty($data->dataDepreciacao)) )) )
        {

            $filters[] = new TFilter('dataDepreciacao', '=', $data->dataDepreciacao);// create the filter 
        }
        if (isset($data->id) AND ( (is_scalar($data->id) AND $data->id !== '') OR (is_array($data->id) AND (!empty($data->id)) )) )
        {

            $filters[] = new TFilter('patrimonioId', '=', $data->id);// create the filter 
        }

        // fill the form with data again
        $this->form->setData($data);

        // keep the search data in the session
        TSession::setValue(__CLASS__.'_filter_data', $data);

        return $filters;
    }

    public function onGenerate($format)
    {
        try
        {
            $filters = $this->getFilters();
            // open a transaction with database 'controlepatrimonio'
            TTransaction::open(self::$database);
            $param = [];
            // creates a repository for Movimentacaodepreciacao
            $repository = new TRepository(self::$activeRecord);
            // creates a criteria
            $criteria = new TCriteria;

            $criteria->setProperties($param);

            if ($filters)
            {
                foreach ($filters as $filter) 
                {
                    $criteria->add($filter);       
                }
            }

            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);

            if ($objects)
            {
                $widths = array(200,200,200,200,200);
                $reportExtension = 'pdf';
                switch ($format)
                {
                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        $reportExtension = 'html';
                        break;
                    case 'xls':
                        $tr = new TTableWriterXLS($widths);
                        $reportExtension = 'xls';
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths, 'P', 'A4');
                        $reportExtension = 'pdf';
                        break;
                    case 'htmlPdf':
                        $reportExtension = 'pdf';
                        $tr = new BTableWriterHtmlPDF($widths, 'P', 'A4');
                        break;
                    case 'rtf':
                        if (!class_exists('PHPRtfLite_Autoloader'))
                        {
                            PHPRtfLite::registerAutoloader();
                        }
                        $reportExtension = 'rtf';
                        $tr = new TTableWriterRTF($widths, 'P', 'A4');
                        break;
                }

                if (!empty($tr))
                {
                    // create the document styles
                    $tr->addStyle('title', 'Helvetica', '10', 'B',   '#000000', '#dbdbdb');
                    $tr->addStyle('datap', 'Arial', '10', '',    '#333333', '#f0f0f0');
                    $tr->addStyle('datai', 'Arial', '10', '',    '#333333', '#ffffff');
                    $tr->addStyle('header', 'Helvetica', '16', 'B',   '#5a5a5a', '#6B6B6B');
                    $tr->addStyle('footer', 'Helvetica', '10', 'B',  '#5a5a5a', '#A3A3A3');
                    $tr->addStyle('break', 'Helvetica', '10', 'B',  '#ffffff', '#9a9a9a');
                    $tr->addStyle('total', 'Helvetica', '10', 'I',  '#000000', '#c7c7c7');
                    $tr->addStyle('breakTotal', 'Helvetica', '10', 'I',  '#000000', '#c6c8d0');

                    // add titles row
                    $tr->addRow();
                    $tr->addCell("Patrimônio ", 'left', 'title');
                    $tr->addCell("Descrição ", 'left', 'title');
                    $tr->addCell("DataDepreciacao", 'left', 'title');
                    $tr->addCell("Valor", 'left', 'title');
                    $tr->addCell("Centro de custo ", 'left', 'title');

                    $grandTotal = [];
                    $breakTotal = [];
                    $breakValue = null;
                    $firstRow = true;

                    // controls the background filling
                    $colour = false;                
                    foreach ($objects as $object)
                    {
                        $style = $colour ? 'datap' : 'datai';

                        $firstRow = false;

                        $object->valor = call_user_func(function($value, $object, $row) 
                        {
                            if(!$value)
                            {
                                $value = 0;
                            }

                            if(is_numeric($value))
                            {
                                return "R$ " . number_format($value, 2, ",", ".");
                            }
                            else
                            {
                                return $value;
                            }
                        }, $object->valor, $object, null);

                        $tr->addRow();

                        $tr->addCell($object->fk_patrimonioId->CodigodoPatrimonio, 'left', $style);
                        $tr->addCell($object->fk_patrimonioId->descricao, 'left', $style);
                        $tr->addCell($object->dataDepreciacao, 'left', $style);
                        $tr->addCell($object->valor, 'left', $style);
						
                        
                        $colour = !$colour;

                    }

                    $file = 'report_'.uniqid().".{$reportExtension}";
                    // stores the file
                    if (!file_exists("app/output/{$file}") || is_writable("app/output/{$file}"))
                    {
                        $tr->save("app/output/{$file}");
                    }
                    else
                    {
                        throw new Exception(_t('Permission denied') . ': ' . "app/output/{$file}");
                    }

                    parent::openFile("app/output/{$file}");

                    // shows the success message
                    new TMessage('info', _t('Report generated. Please, enable popups'));
                }
            }
            else
            {
                new TMessage('error', _t('No records found'));
            }

            // close the transaction
            TTransaction::close();
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


}

