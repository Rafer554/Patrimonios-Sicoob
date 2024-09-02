<?php

class AbrirCamera extends TPage
{
    protected $form;
    private static $database = 'controlepatrimonio';
    private static $activeRecord = 'Movimentacao';
    private static $primaryKey = 'id';
    private static $formName = 'form_Movimentacao';

    use Adianti\Base\AdiantiFileSaveTrait;

    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct($param)
    {
        parent::__construct();

        if (!empty($param['target_container'])) {
            $this->adianti_target_container = $param['target_container'];
        }

        // Create the form
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle("Cadastrar uma movimentação");

        // Create and configure the form fields
        $criteria_fk_patrimonioId_id = new TCriteria();
        $criteria_localAntigo = new TCriteria();
        $criteria_patrimonioId = new TCriteria();
        $criteria_localAtual = new TCriteria();

        $criteria_fk_patrimonioId_id->setProperty('order', 'descricao asc');

        $fk_patrimonioId_id = new TDBSeekButton('fk_patrimonioId_id', 'controlepatrimonio', self::$formName, 'Patrimonio', 'CodigodoPatrimonio', 'fk_patrimonioId_id', 'fk_patrimonioId_id_display', $criteria_fk_patrimonioId_id);
        $fk_patrimonioId_id_display = new TEntry('fk_patrimonioId_id_display');
        $pesquisaPatrimonioId = new TEntry('pesquisaPatrimonioId');
        $localAntigo = new TDBUniqueSearch('localAntigo', 'controlepatrimonio', 'Local', 'id', 'id', 'id asc', $criteria_localAntigo);
        $patrimonioId = new TDBUniqueSearch('patrimonioId', 'controlepatrimonio', 'Patrimonio', 'id', 'CodigodoPatrimonio', 'CodigodoPatrimonio asc', $criteria_patrimonioId);
        $dataInspecao = new TDate('dataInspecao');
        $localAtual = new TDBUniqueSearch('localAtual', 'controlepatrimonio', 'Local', 'id', 'Descricao', 'id asc', $criteria_localAtual);
        $Descricao = new THtmlEditor('Descricao');
        $imagem = new TFile('Escolha uma Imagem');

        // Configure fields
        $fk_patrimonioId_id->setExitAction(new TAction([$this, 'alteraDados']));
        $pesquisaPatrimonioId->setEditable(false);

        $localAntigo->addValidation("LocalAntigo", new TRequiredValidator());
        $patrimonioId->addValidation("PatrimonioId", new TRequiredValidator());
        $dataInspecao->addValidation("DataInspecao", new TRequiredValidator());

        $fk_patrimonioId_id->setDisplayMask('{descricao}');
        $fk_patrimonioId_id_display->setEditable(false);
        $fk_patrimonioId_id->setId('fk_patrimonioId_id');

        $dataInspecao->setDatabaseMask('yyyy-mm-dd');
        $imagem->enableFileHandling();
        $localAtual->setMinLength(2);
        $localAntigo->setMinLength(2);
        $patrimonioId->setMinLength(2);

        $localAtual->setMask('{Descricao}');
        $localAntigo->setMask('{Descricao}');
        $dataInspecao->setMask('dd/mm/yyyy');
        $patrimonioId->setMask('{CodigodoPatrimonio}');

        $imagem->setSize('40%');
        $dataInspecao->setSize(110);
        $localAtual->setSize('40%');
        $localAntigo->setSize('40%');
        $patrimonioId->setSize('40%');
        $Descricao->setSize('40%', 110);
        $fk_patrimonioId_id->setSize(110);
        $pesquisaPatrimonioId->setSize('8.9%');
        $fk_patrimonioId_id_display->setSize(110);

        // Add fields to form
        $this->form->addFields([new TLabel("ID:", null, '14px', null)], [$pesquisaPatrimonioId]);
        $this->form->addFields([new TLabel("Código Patrimônio:", null, '14px', null)], [$fk_patrimonioId_id]);
        $this->form->addFields([new TLabel("Nome:", null, '14px', null)], [$fk_patrimonioId_id_display]);
        $this->form->addFields([new TLabel("Código do Patrimonio:", '#ff0000', '14px', null)], [$patrimonioId]);
        $this->form->addFields([new TLabel("Local Antigo:", '#ff0000', '14px', null)], [$localAntigo]);
        $this->form->addFields([new TLabel("Data da Inspecao:", '#ff0000', '14px', null)], [$dataInspecao]);
        $this->form->addFields([new TLabel("Local Atual:", null, '14px', null)], [$localAtual]);
        $this->form->addFields([new TLabel("Descricao:", null, '14px', null)], [$Descricao]);
        $this->form->addFields([new TLabel("Imagem:", null, '14px', null)], [$imagem]);

        // Create the form actions
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'far:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 

        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;
        
        $btnBack = $this->form->addAction("Voltar", new TAction(['LerCodigoQR', 'onShowBack']), 'fa-backward fa-fw #000000');
        
        // Vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(["Geral", "CadastraMovimentação"]));
        }
       

        parent::add($container);
    }

    public static function alteraDados($param = null)
    {
        try
        {
            $codigoPatrimonio = $param['fk_patrimonioId_id']; 

            TTransaction::open('controlepatrimonio');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodigodoPatrimonio', '=', $codigoPatrimonio));

            $repository = new TRepository('Patrimonio');
            $results = $repository->load($criteria);

            if (!empty($results)) {
                $result = $results[0]; 

                $obj = new StdClass;
                $obj->{'pesquisaPatrimonioId'} = $result->id;
                $obj->{'fk_patrimonioId_id_display'} = $result->descricao;
                $obj->{'patrimonioId'} = $result->id;
                $obj->{'localAntigo'} = $result->Local_id;
                $obj->{'localAtual'} = $result->Local_id;
                $obj->{'dataInspecao'} = date('d/m/Y');

                TForm::sendData(self::$formName, $obj);  // Atualize os dados no formulário
            }

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
            $codigoPatrimonio = $param['pesquisaPatrimonioId']; 

            TTransaction::open('controlepatrimonio');

            $criteria = new TCriteria;
            $criteria->add(new TFilter('CodigodoPatrimonio', '=', $codigoPatrimonio));

            $repository = new TRepository('Patrimonio');
            $results = $repository->load($criteria);

            if (!empty($results)) {
                $result = $results[0]; 

                $obj = new StdClass;
                $obj->{'patrimonioId'} = $result->id;
                $obj->{'localAntigo'} = $result->Local_id;
                $obj->{'localAtual'} = $result->Local_id;
                $obj->{'dataInspecao'} = date('d/m/Y');

                TForm::sendData('form_Movimentacao', $obj);
            } else {
                new TMessage('error', 'Patrimônio não encontrado.');
            }

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
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            $this->form->setData($this->form->getData()); 
            TTransaction::rollback(); 
        }
    }

    public function onEdit($param)
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  
                TTransaction::open(self::$database); 

                $object = new Movimentacao($key); 

                $object->fk_patrimonioId_id = $object->fk_patrimonioId->id;

                $this->form->setData($object); 

                TTransaction::close(); 
            }
            else
            {
                $this->form->clear();
            }
        }
        catch (Exception $e) 
        {
            new TMessage('error', $e->getMessage()); 
            TTransaction::rollback(); 
        }
    }

    public function onClear($param)
    {
        $this->form->clear(true);
    }
	

    public function onShowCadMov($param = null)
    {
    } 

    public function onShowBack($param = null)
    {
    }

    public static function getFormName()
    {
        return self::$formName;
    }

    public function onShow($param = null)
    {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Formulário com Câmera</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.rawgit.com/serratus/quaggaJS/0420d5e0/dist/quagga.min.js"></script>
    <style>
        /* Container principal que irá conter o formulário e a câmera */
        #container {
            position: relative;
            width: 100%;
            height: 100vh;
        }

        /* Container do formulário */
        .form-container {
            position: relative; 
            z-index: 1;
        }

        /* Container da câmera */
        #camera-button-container {
            position: absolute;
            top: 20px; 
            right: 20px; 
            z-index: 9999;
        }

        /* Estilo do scanner */
        #scanner-container {
            position: absolute;
            top: 15vh;
            left: 105vh;
            width: 100%;
            height: 100%;
            z-index: 9998; 
        }
    </style>
</head>
<body>
    <!-- Container principal -->
    <div id="container">
        <!-- Contêiner do formulário -->
        <div class="form-container">
            <!-- Aqui o formulário será incluído -->
            <?php echo $this->form; ?>
        </div>

        <!-- Div para mostrar o scanner -->
        <div id="scanner-container"></div>

        <!-- Contêiner para o botão da câmera -->
        <div id="camera-button-container">
            <input type="button" id="btn" value="Iniciar câmera" />
        </div>
    </div>

    <script src="quagga.min.js"></script>
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
                    return;
                }

                console.log("Initialization finished. Ready to start");
                Quagga.start();

                // Set a flag as running
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
                var patrimonioInput = document.querySelector('input[name="fk_patrimonioId_id"]');
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
