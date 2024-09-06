<?php


class Visualizar extends TPage{
	
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
		
		if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }
		
		//Criando o Form:
		$this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle("Registrar movimentação");
		
		//Definindo os criterios:
        $criteriaprocura_patrimonio = new TCriteria();
        	$criteriaprocura_patrimonio->setProperty('order', 'descricao asc');
        
        $criteria_localAntigo = new TCriteria();
        $criteria_patrimonioId = new TCriteria();
        $criteria_localAtual = new TCriteria();
		$criteria_data = new TCriteria();
       
		//Encontra o patrimonio:
		$procura_patrimonio = new TDBSeekButton('procura_patrimonio', 'controlepatrimonio', self::$formName, 'Patrimonio', 'CodigodoPatrimonio', 'procura_patrimonio', 'procura_patrimonio_display', $criteriaprocura_patrimonio);
		$procura_patrimonio->setExitAction(new TAction([$this, 'alteraDados']));
        $procura_patrimonio->setDisplayMask('{descricao}');
        $procura_patrimonio->setId('procura_patrimonio');
        $procura_patrimonio->setSize(110);

		
		//Display que mostra o nome registrado ao patrimonio buscado:
        $procura_patrimonio_display = new TEntry('procura_patrimonio_display');
        $procura_patrimonio_display->setEditable(false);
        $procura_patrimonio_display->setSize(110);
        
		//Busca o ID vinculado ao patrimonio escolhido:
		$patrimonioId = new TDBUniqueSearch('patrimonioId', 'controlepatrimonio', 'Patrimonio', 'id', 'CodigodoPatrimonio', 'CodigodoPatrimonio asc', $criteria_patrimonioId);
        $patrimonioId->addValidation("PatrimonioId", new TRequiredValidator());
        $patrimonioId->setMinLength(2);
        $patrimonioId->setMask('{CodigodoPatrimonio}');
        $patrimonioId->setSize('40%');
		$patrimonioId->seteditable(false);
		
		//Mostra o patrimonio pesquisado
        $pesquisaPatrimonioId = new TEntry('pesquisaPatrimonioId');
        $pesquisaPatrimonioId->setEditable(false);
        $pesquisaPatrimonioId->setSize('8.9%');
		
		//Mostra o local atual do patrimonio:
		$localAtual = new TDBUniqueSearch('LocalAtual', 'controlepatrimonio', 'Local', 'id', 'id', 'id asc', $criteria_localAntigo);
        $localAtual->addValidation("LocalAtual", new TRequiredValidator());
        $localAtual->setMinLength(2);
        $localAtual->setMask('{Descricao}');
        $localAtual->setSize('40%');
        
		//Data registro:
		$dataRegistro = new TDBUniqueSearch('dataRegistro', 'controlepatrimonio', 'DataEntrada', 'id', 'CodigoPatrimonio', 'CodigoPatrimonio asc', $criteria_data);
		$dataRegistro->addValidation("dataRegistro", new TRequiredValidator());
        $dataRegistro->setMinLength(2);
        $dataRegistro->setMask('{dataRegistro}');
        $dataRegistro->setSize('40%'); 
		$dataRegistro->setEditable(false);
		
		
		
		
        $row1 = $this->form->addFields([new TLabel("Código Patrimônio:", null, '14px', null)], [$procura_patrimonio]);
        $row2 = $this->form->addFields([new TLabel("Nome:", null, '14px', null)], [$procura_patrimonio_display]);
		$row3 = $this->form->addFields([new TLabel("Data do Registro", null, '14px', 'null')], [$dataRegistro]);
		$row4 = $this->form->addFields([new TLabel("ID", null, '14px', 'null')], [$pesquisaPatrimonioId]);

		
			//Adicionando o estilo e o formulario na box principal da página
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(["Geral", "Registrar movimentação Patrimonial"]));
        }
        $container->add($this->form);

        parent::add($container);
		
	}
	public static function alteraDados($param) {
        try {
            $codigoPatrimonio = $param['procura_patrimonio']; 

            TTransaction::open('controlepatrimonio');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodigodoPatrimonio', '=', $codigoPatrimonio));

            $repository = new TRepository('Patrimonio');
            $results = $repository->load($criteria);

            if (!empty($results)) {
                $result = $results[0]; 

                $obj = new StdClass;
                $obj->{'pesquisaPatrimonioId'} = $result->id;
                $obj->{'procura_patrimonio_display'} = $result->descricao;
                $obj->{'patrimonioId'} = $result->id;
                $obj->{'localAntigo'} = $result->Local_id;
                $obj->{'dataInspecao'} = date('d/m/Y');
				

                TForm::sendData(self::$formName, $obj);  // Atualize os dados no formulário
            }

            TTransaction::close();
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());    
        }
    }
	
	public function onLerCodigo ($param){
		
	}
}