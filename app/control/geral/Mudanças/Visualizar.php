<?php

class Visualizar extends TPage {
    
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $showMethods = ['onReload', 'onSearch', 'onRefresh', 'onClearFilters'];
    private static $formName = 'formReport_IdentificadorDeLocal';
    public $dropdown;
    use Adianti\Base\AdiantiStandardListTrait;
    use Adianti\Base\AdiantiFileSaveTrait;

    public function __construct($param) {
        parent::__construct();
        
        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }
        
        // Criando o Form:
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle("Registrar movimentação");
        
        // Definindo os critérios:
        $criteriaprocura_patrimonio = new TCriteria();
        $criteriaprocura_patrimonio->setProperty('order', 'descricao asc');
        
        $criteria_localAntigo = new TCriteria();
        $criteria_patrimonioId = new TCriteria();
        $criteria_localAtual = new TCriteria();
        $criteria_data = new TCriteria();
        
        // Encontra o patrimônio:
        $procura_patrimonio = new TDBSeekButton('procura_patrimonio', 'controlepatrimonio', self::$formName, 'Patrimonio', 'CodigodoPatrimonio', 'procura_patrimonio', $criteriaprocura_patrimonio);
        $procura_patrimonio->setExitAction(new TAction([$this, 'alteraDados']));
        $procura_patrimonio->setDisplayMask('{descricao}');
        $procura_patrimonio->setId('procura_patrimonio');
        $procura_patrimonio->setSize(110);
        
        // Display que mostra o nome registrado ao patrimônio buscado:
        $procura_patrimonio_display = new TEntry('procura_patrimonio_display');
        $procura_patrimonio_display->setEditable(false);
        $procura_patrimonio_display->setSize(110);
		
		//Declaração do campo que puxará o patrimonio
		$codPatrimonio = new TEntry('codPatrimonio');
		$codPatrimonio->setEditable(false);
		$codPatrimonio->setSize(110);
        
		
		/*
        // Busca o ID vinculado ao patrimônio escolhido:
        $patrimonioId = new TDBUniqueSearch('patrimonioId', 'controlepatrimonio', 'Patrimonio', 'id', 'CodigodoPatrimonio', 'CodigodoPatrimonio asc', $criteria_patrimonioId);
        $patrimonioId->setMinLength(2);
        $patrimonioId->setMask('{CodigodoPatrimonio}');
        $patrimonioId->setSize('40%');
        $patrimonioId->seteditable(false);
        
        // Mostra o patrimônio pesquisado
        $pesquisaPatrimonioId = new TEntry('pesquisaPatrimonioId');
        $pesquisaPatrimonioId->setEditable(false);
        $pesquisaPatrimonioId->setSize('25%');
        
        // Mostra o local atual do patrimônio:
        $localAtual= new TDBUniqueSearch('localAtual', 'controlepatrimonio', 'Local', 'id', 'id', 'id asc', $criteria_localAntigo);
        $localAtual->addValidation("LocalAtual", new TRequiredValidator());
        $localAtual->setMinLength(2);
        $localAtual->setMask('{Descricao}');
        $localAtual->setSize('25%');
        $localAtual->setEditable(false);


        // Data registro:
        $dataRegistro = new TDate('dataRegistro');
		$dataRegistro->setMask('dd/mm/yyyy');
		$dataRegistro->setDatabaseMask('yyyy-mm-dd');
        $dataRegistro->setEditable(false);
        $dataRegistro->setSize('25%'); 
	
		
        	Retorno da imagem:
		$visualizacaoImagem = new TImage('');
		$visualizacaoImagem->width = '180px';
		$visualizacaoImagem->height = '180px';
			$this->visualizacaoImagem = $visualizacaoImagem; 
		
		//Declaração do campo que puxa o responsavel:
		$responsavel = new TEntry('responsavel');
		$responsavel ->setEditable(false);
		$responsavel ->setSize('25%');
		
		
		$responsavel_display = new TEntry('responsavel_display');
		$responsavel_display->setEditable(false);
        $responsavel_display->setSize('25%');
		
		
		//Declaração do campo que puxará o valor:
		$ValorOriginal = new TNumeric('ValorOriginal', '2', ',', '.' );
		$ValorOriginal->setEditable(false);
		$ValorOriginal->setSize('25%');
		
		//Declaração do campo que puxará o valor Atual:
		$ValorAtual = new TNumeric('ValorAtual', '2', ',', '.' );
		$ValorAtual->setEditable(false);
		$ValorAtual->setSize('25%'); */
		
		
		
		
		//Declaração das linhas:
        $row1 = $this->form->addFields([new TLabel("Código Patrimônio:", null, '14px', null)], [$procura_patrimonio]);
        $row2 = $this->form->addFields([new TLabel("Nome:", null, '14px', null)], [$procura_patrimonio_display]);
		$row3 = $this->form->addFields([new TLabel("Patrimônio:", null, '14px', null)], [$codPatrimonio]);
		
		
		
        /*$row3 = $this->form->addFields([new TLabel("ID:", null, '14px', null)], [$pesquisaPatrimonioId]);
        $row4 = $this->form->addFields([new TLabel("Local:", null, '14px', null)], [$localAtual]);
        $row5 = $this->form->addFields([new TLabel("Data do Registro:", null, '14px', null)], [$dataRegistro]);
        $row6 = $this->form->addFields([$visualizacaoImagem]);
           $row6->layout = ['col-sm-6']; 
		$row7 = $this->form->addFields([new TLabel("Responsavel:", null, '14px', null)], [$responsavel]);
		$row8 = $this->form->addFields([new TLabel("Chapa:", null, '14px', null)], [$responsavel_display]);
		$row9 = $this->form->addFields([new TLabel("Valor Original:", null, '14px', null)], [$ValorOriginal]);
		$row10 = $this->form->addFields([new TLabel("Valor Atual:", null, '14px', null)], [$ValorAtual]); */
		
		
		//Declaração dos botões
		$btnBack = $this->form->addAction("Voltar", new TAction(['LerCodigoQR', 'onShowBack']), 'fa-backward fa-fw #000000');
		
		
		
        // Adicionando o estilo e o formulário na box principal da página
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
			/*	
			//Formata a data
			$dataRegistro = new DateTime($result->DataEntrada);
            $dataRegistroFormatada = $dataRegistro->format('d/m/Y'); */
				
                $obj = new StdClass;
                $obj->{'procura_patrimonio_display'} = $result->descricao;
				$obj->{'codPatrimonio'} = $result->CodigodoPatrimonio;
                /*$obj->{'patrimonioId'} = $result->id;
				$obj->{'pesquisaPatrimonioId'} = $result->id;
                $obj->{'localAtual'} = $result->Local_id;
                $obj->{'dataRegistro'} = $dataRegistroFormatada;
				$obj->{'visualizacaoImagem'} = 'imagens/' . $result->imagem;
				$obj->{'responsavel'} = $result->responsavel;
				$obj->{'ValorOriginal'} = $result->ValorOriginal;
				$obj->{'ValorAtual'} = $result->ValorAtual;
				$obj->{'responsavel_display'} = $result->chapa;*/
				
                TForm::sendData(self::$formName, $obj);  
            }

            TTransaction::close();
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage());    
        }
    }
	
	public function onLerCodigo ($param){
		?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Leitor de Código de Barras</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.rawgit.com/serratus/quaggaJS/0420d5e0/dist/quagga.min.js"></script>
    <style>
        canvas.drawing, canvas.drawingBuffer {
            position: absolute;
            left: 0;
            top: 0;
        }
        #scanner-container {
            width: 100%;
            height: 100%;
            position: absolute;
            z-index: 9998;
			top:575px;
        }
        #btn {
            width: 150px;
            height: 50px;
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #32CD32;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            z-index: 9999;
        }
        #btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div id="scanner-container"></div>
    <button id="btn">Iniciar Leitura</button>

    <script>
        var _scannerIsRunning = false;

        function startScanner() {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#scanner-container'),
                    constraints: {
                        width: 480,
                        height: 320,
                        facingMode: "environment"
                    },
                },
                decoder: {
                    readers: [
                        "code_128_reader",
                        "ean_reader",
                        "ean_8_reader",
                        "code_39_reader",
                        "code_39_vin_reader",
                        "codabar_reader",
                        "upc_reader",
                        "upc_e_reader",
                        "i2of5_reader"
                    ]
                }
            }, function (err) {
                if (err) {
                    console.log(err);
                    return;
                }

                console.log("Initialization finished. Ready to start");
                Quagga.start();

                _scannerIsRunning = true;
            });

            Quagga.onDetected(function (result) {
                console.log("Barcode detected and processed : [" + result.codeResult.code + "]", result);

                var patrimonioInput = document.querySelector('input[name="procura_patrimonio"]');
                patrimonioInput.value = result.codeResult.code;

                patrimonioInput.focus();

                Quagga.stop();
                _scannerIsRunning = false;

                setTimeout(function() {
                    patrimonioInput.blur();  
                }, 100);
            });
        }

        document.getElementById("btn").addEventListener("click", function () {
            if (_scannerIsRunning) {
                Quagga.stop();
                _scannerIsRunning = false;
            } else {
                startScanner();
            }
        }, false);
    </script>
</body>
</html>
<?php 
    }
}
?>

