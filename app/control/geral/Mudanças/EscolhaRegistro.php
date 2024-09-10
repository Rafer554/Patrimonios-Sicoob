<?php

use Adianti\Widget\Wrapper\TDBCombo;


class EscolhaRegistro extends TPage{
		
   private $form; // form
   private $datagrid; // listing
   private $pageNavigation;
   private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
   private static $formName = 'formReport_IdentificadorDeLocal';
	public $dropdown;
		use Adianti\Base\AdiantiStandardListTrait;
		use Adianti\Base\AdiantiFileSaveTrait;
	
	public function __construct( $param ){
		
		parent::__construct();
		
		//Cria o formulário
		$this->form = new BootstrapFormBuilder(self::$formName);
		//Titulo
		$this->form->setFormTitle('Escolha o local para registro:');
		
		//Declara os criterios
		$criteria_patrimonioId = new TCriteria();
		$criteria_fk_patrimonioId_id = new TCriteria();
        $criteria_localAntigo = new TCriteria();
        $criteria_patrimonioId = new TCriteria();
        $criteria_localAtual = new TCriteria();

        $criteria_fk_patrimonioId_id->setProperty('order', 'descricao asc');
		
		
		//Cria o dropDown
		  $dropdown = new TDBCombo('Local', 'controlepatrimonio', 'local', 'id', '{Descricao}', 'Local', $criteria_patrimonioId);
			$dropdown->setSize('70%');
			$dropdown->setChangeAction(new TAction([$this, 'onChangeDropdown']));
		
		//Insere o mesmo no formulário
		$row1 = $this->form->addFields([new TLabel("Local:", null, '14px', null)], [$dropdown]);
		
		//require input in Web Form
		$dropdown->addValidation("Local", new TRequiredValidator()); 
		
		//Reseta o WebForm 
		$this->form->setData( TSession::getValue(__CLASS__.'_filter_data') );
		
		
		//Declaração dos botões     
		$btnAbrirCamera = $this->form->addAction("<i class='fa-solid fa-camera-retro'></i> Ler código", new TAction([$this, 'Redirecionar']), '#000000');
        $this->btnAbrirCamera = $btnAbrirCamera;
		
		$btnManualForm = $this->form->addAction("<i class='fa-solid fa-file-lines'></i> Registrar manualmente", new TAction([ $this, 'Redirecionar2']), '#000000');
		$this->btnManualForm = $btnManualForm;
		
		
		
		//Declara e insere o container do form principal na página
		$container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        $container->add(TBreadCrumb::create(["Geral","Leitor de código patrimonial"]));
        $container->add($this->form);
		
		parent::add($container);


    }
	//função que salva
	 public function onSave($param = null) 
    {
        try{
			
			TTransaction::open(self::$formName);
			$data = $this->form->getData('controlepatrimonio'); 
			$data->store();
		
			
			TTransaction::close();
			
		}catch(Exception $e){
					
			new TMessage('error', $e->GetMessage());
			 	
		}
    }
	
	//limpa o form
	 public function onClear( $param ){
        $this->form->clear(true);
		 
		 TSession::setValue('selected_local', null);
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
	//Esse cara aqui vai enteder quando o dropDown Mudar
	public static function onChangeDropdown($param){
    if (isset($param['Local'])) {
        $selected_value = $param['Local'];

      
        TSession::setValue('selected_local', $selected_value);
    }
}
	//Esse vai redirecionar para a página e puxar com ele o que mudou no dropDown no campo de 'localAtual'
	public function Redirecionar($param)
{
    $selected_local = TSession::getValue('selected_local');

    if (!$selected_local) {
        new TMessage('error', 'Por favor, selecione um local antes de continuar.');
        return;
    }

    try {
        //Redireciona para a página com o local selecionado
        AdiantiCoreApplication::loadPage('AbrirCamera', 'onShow', ['localAtual' => $selected_local]);

        //Limpa o valor da sessão após o redirecionamento
        TSession::setValue('selected_local', null);
    } catch (Exception $e) {
        new TMessage('error', $e->getMessage());
    }
}
		//Mesma coisa do de cima mas com o botão dois
public function Redirecionar2($param)
{
    $selected_local = TSession::getValue('selected_local');

    if (!$selected_local) {
        new TMessage('error', 'Por favor, selecione um local antes de continuar.');
        return;
    }

    try {
        // Redireciona para a página com o local selecionado
        AdiantiCoreApplication::loadPage('ChamarCadastroMovimentacao', 'onShowCardMov', ['localAtual' => $selected_local]);

        // Limpa o valor da sessão após o redirecionamento
        TSession::setValue('selected_local', null);
    } catch (Exception $e) {
        new TMessage('error', $e->getMessage());
    	}
	}

//Redirecionadores, simplesmente estão aqui pra puxar as páginas.
public function onShowCadMov($param){
		
	}
public function onShowBack($param){
		
	}
public function onLerCodigo($param){
		
	}
public function onShow($param ){
	
    }
}