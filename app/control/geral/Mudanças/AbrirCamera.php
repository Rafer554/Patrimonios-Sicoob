<?php

class AbrirCamera extends TPage {
    protected $form;
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'Movimentacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_teste';
    
    public function __construct($param) {
        parent::__construct();
        
        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }
        
		//Criando o Formulário:
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle("Registrar movimentação");
        
		//Definindo os criterios:
        $criteriaprocura_patrimonio = new TCriteria();
        	$criteriaprocura_patrimonio->setProperty('order', 'descricao asc');
        
        $criteria_localAntigo = new TCriteria();
        $criteria_patrimonioId = new TCriteria();
        $criteria_localAtual = new TCriteria();
       
		//Botão que procura o patrimonio:
        $procura_patrimonio = new TDBSeekButton('procura_patrimonio', 'controlepatrimonio', self::$formName, 'Patrimonio', 'CodigodoPatrimonio', 'procura_patrimonio', $criteriaprocura_patrimonio);
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
		
		//Mostra o patrimonio pesquisado
        $pesquisaPatrimonioId = new TEntry('pesquisaPatrimonioId');
        $pesquisaPatrimonioId->setEditable(false);
        $pesquisaPatrimonioId->setSize('8.9%');
        
		//Busca o local no qual o patrimonio está registrado no momento:
        $localAntigo = new TDBUniqueSearch('localAntigo', 'controlepatrimonio', 'Local', 'id', 'id', 'id asc', $criteria_localAntigo);
        $localAntigo->addValidation("LocalAntigo", new TRequiredValidator());
        $localAntigo->setMinLength(2);
        $localAntigo->setMask('{Descricao}');
        $localAntigo->setSize('40%');
        
        //Seta a data atual:
        $dataInspecao = new TDate('dataInspecao');
        $dataInspecao->addValidation("DataInspecao", new TRequiredValidator());
        $dataInspecao->setDatabaseMask('yyyy-mm-dd');
        $dataInspecao->setSize('40%');
        
		//Mostra os locais para a movimentação:
        $localAtual = new TDBUniqueSearch('localAtual', 'controlepatrimonio', 'Local', 'id', 'Descricao', 'id asc', $criteria_localAtual);
        $localAtual->setMinLength(2);
        $localAtual->setMask('{Descricao}');
        $localAtual->setSize('40%');
			if (isset($param['localAtual'])) {
				$localAtualValue = $param['localAtual'];
				$localAtual->setValue($localAtualValue);}
        
		//Campo para adicionar descrição
        $Descricao = new THtmlEditor('Descricao');
        $Descricao->setSize('40%', 110);
        
		//Campo para acrescentar imagem
        $imagem = new TFile('Escolha uma Imagem');
        $imagem->enableFileHandling();
        $imagem->setSize('40%');
        
		
		//Definição de ordem e campos do formulário
        $row1 = $this->form->addFields([new TLabel("ID:", null, '14px', null)], [$pesquisaPatrimonioId]);
        $row2 = $this->form->addFields([new TLabel("Código Patrimônio:", null, '14px', null)], [$procura_patrimonio]);
        $row3 = $this->form->addFields([new TLabel("Nome:", null, '14px', null)], [$procura_patrimonio_display]);
		
		//Espaçamento visando melhor visualização Userside
        $row4 = $this->form->addFields([new TLabel(" ", null, '14px', null, '100%')],[]);
		
		//Continuação da definição:
        $row5 = $this->form->addFields([new TLabel("Código do Patrimonio:", '#ff0000', '14px', null)], [$patrimonioId]);
        $row6 = $this->form->addFields([new TLabel("Local Antigo:", '#ff0000', '14px', null)], [$localAntigo]);
        $row7 = $this->form->addFields([new TLabel("Data da Inspecao:", '#ff0000', '14px', null)], [$dataInspecao]);
        $row8 = $this->form->addFields([new TLabel("Local Atual:", null, '14px', null)], [$localAtual]);
        $row9 = $this->form->addFields([new TLabel("Descricao:", null, '14px', null)], [$Descricao]);
        $row10 = $this->form->addFields([new TLabel("Imagem:", null, '14px', null)], [$imagem]);
        
		
		//Declaração e implementação dos botões
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'far:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 
        
        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;
        
        $btnBack = $this->form->addAction("Voltar", new TAction(['EscolhaRegistro', 'onShowBack']), 'fa-backward fa-fw #000000');
        
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
    	/*Essa função vai alterar os dados automaticamente quando um patrimonio for escolhido la pelo 'procura_patrimonio', então se algum tiver algum problma com o comando estar puxando errado em alguma futura atualização do PHP provavelmente é entre esses dois o problema.*/
	
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
    
	//função que vai salvar:
    public function onSave($param = null) {
        try {
            TTransaction::open(self::$database); 

            $messageAction = null;

            $this->form->validate(); 

            $object = new Movimentacao(); 

            $data = $this->form->getData(); 

            $object->fromArray((array) $data); 
            $patrimonio = new Patrimonio($object->patrimonioId);
            $patrimonio->Local_id = $param['localAtual'];
            $patrimonio->store();

            $imagem_dir = 'imagens';  

            $object->store(); 

            $this->saveFile($object, $data, 'imagem', $imagem_dir); 

            $data->id = $object->id; 

            $this->form->setData($data); 
            TTransaction::close(); 

            new TMessage('info', AdiantiCoreTranslator::translate('Record saved'), $messageAction);
        }
        catch (Exception $e) {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData($this->form->getData()); 
            TTransaction::rollback(); 
        }
    }
    
    public function onClear($param) {
        $this->form->clear(true);
		
		TSession::setValue('selected_local', null);
    }
    
    public function onShowBack($param = null) {
        // Código para voltar para a página anterior, se necessário
    }
	

    public function onShow($param = null) {
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Leitor de Código de Barras</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.rawgit.com/serratus/quaggaJS/0420d5e0/dist/quagga.min.js"></script>
    <style>
        /* In order to place the tracking correctly */
        canvas.drawing, canvas.drawingBuffer {
            position: absolute;
            left: 0;
            top: 0;
        }
        #scanner-container {
			right: 525px;
			top: 225px;
            width: 100%;
    		height: 100vh
            position: absolute;
			z-index: 9998;
        }
		#btn {
			width: 12vh;
			height: 5vh;
			position: fixed; /* Faz o botão ficar fixo na tela */
			top: 70px; /* Posição vertical do botão */
			left: 310px; /* Posição horizontal do botão */
			background-color: #8bc34a;
			color: #fff;
			border: none;
			border-radius: 2px;
			font-size: 14px;
			cursor: pointer;
            z-index: 3;
            box-shadow: 5px 5px 10px rgba(0,0,0,0.2);

        }
        #btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <!-- Div to show the scanner -->
    <div id="scanner-container"></div>
    <input type="button" id="btn" value="Iniciar Câmera" />

    <script>
        var _scannerIsRunning = false;

        function startScanner() {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#scanner-container'),
                    constraints: {
                        width: 300,
                        height: 300,
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
                    ],
                    debug: {
                        showCanvas: true,
                        showPatches: true,
                        showFoundPatches: true,
                        showSkeleton: true,
                        showLabels: true,
                        showPatchLabels: true,
                        showRemainingPatchLabels: true,
                        boxFromPatches: {
                            showTransformed: true,
                            showTransformedBox: true,
                            showBB: true
                        }
                    }
                },

            }, function (err) {
                if (err) {
                    console.log(err);
                    return
                }

                console.log("Initialization finished. Ready to start");
                Quagga.start();

                // Set flag to is running
                _scannerIsRunning = true;
            });

            Quagga.onProcessed(function (result) {
                var drawingCtx = Quagga.canvas.ctx.overlay,
                drawingCanvas = Quagga.canvas.dom.overlay;

                if (result) {
                    if (result.boxes) {
                        drawingCtx.clearRect(0, 0, parseInt(drawingCanvas.getAttribute("width")), parseInt(drawingCanvas.getAttribute("height")));
                        result.boxes.filter(function (box) {
                            return box !== result.box;
                        }).forEach(function (box) {
                            Quagga.ImageDebug.drawPath(box, { x: 0, y: 1 }, drawingCtx, { color: "green", lineWidth: 2 });
                        });
                    }

                    if (result.box) {
                        Quagga.ImageDebug.drawPath(result.box, { x: 0, y: 1 }, drawingCtx, { color: "#00F", lineWidth: 2 });
                    }

                    if (result.codeResult && result.codeResult.code) {
                        Quagga.ImageDebug.drawPath(result.line, { x: 'x', y: 'y' }, drawingCtx, { color: 'red', lineWidth: 3 });
                    }
                }
            });

            Quagga.onDetected(function (result) {
                console.log("Barcode detected and processed : [" + result.codeResult.code + "]", result);

                // Atualiza o input
                var patrimonioInput = document.querySelector('input[name="procura_patrimonio"]');
                patrimonioInput.value = result.codeResult.code;

                // Foco no campo 'Código Patrimônio'
                patrimonioInput.focus();
               
                // Para o Scanner pós leitura
                Quagga.stop();
                _scannerIsRunning = false;
                
                // Remove o foco do campo
                setTimeout(function() {
                    patrimonioInput.blur();  
                }, 100);
            });
        }

        // Encerra o Scanner on action
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
