<?php

class TesteJavaScript extends TPage {
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
        
        $this->form = new BootstrapFormBuilder(self::$formName);
        $this->form->setFormTitle("Teste");
        
        $criteriaprocura_patrimonio = new TCriteria();
        $criteriaprocura_patrimonio->setProperty('order', 'descricao asc');
        
        $criteria_localAntigo = new TCriteria();
        $criteria_patrimonioId = new TCriteria();
        $criteria_localAtual = new TCriteria();
        
        $procura_patrimonio = new TDBSeekButton('procura_patrimonio', 'controlepatrimonio', self::$formName, 'Patrimonio', 'CodigodoPatrimonio', 'procura_patrimonio', 'procura_patrimonio_display', $criteriaprocura_patrimonio);
        $procura_patrimonio->setExitAction(new TAction([$this, 'alteraDados']));
        $procura_patrimonio->setDisplayMask('{descricao}');
        $procura_patrimonio->setId('procura_patrimonio');
        $procura_patrimonio->setSize(110);
        
        $procura_patrimonio_display = new TEntry('procura_patrimonio_display');
        $procura_patrimonio_display->setEditable(false);
        $procura_patrimonio_display->setSize(110);
        
        $pesquisaPatrimonioId = new TEntry('pesquisaPatrimonioId');
        $pesquisaPatrimonioId->setEditable(false);
        $pesquisaPatrimonioId->setSize('8.9%');
        
        $localAntigo = new TDBUniqueSearch('localAntigo', 'controlepatrimonio', 'Local', 'id', 'id', 'id asc', $criteria_localAntigo);
        $localAntigo->addValidation("LocalAntigo", new TRequiredValidator());
        $localAntigo->setMinLength(2);
        $localAntigo->setMask('{Descricao}');
        $localAntigo->setSize('40%');
        
        $patrimonioId = new TDBUniqueSearch('patrimonioId', 'controlepatrimonio', 'Patrimonio', 'id', 'CodigodoPatrimonio', 'CodigodoPatrimonio asc', $criteria_patrimonioId);
        $patrimonioId->addValidation("PatrimonioId", new TRequiredValidator());
        $patrimonioId->setMinLength(2);
        $patrimonioId->setMask('{CodigodoPatrimonio}');
        $patrimonioId->setSize('40%');
        
        $dataInspecao = new TDate('dataInspecao');
        $dataInspecao->addValidation("DataInspecao", new TRequiredValidator());
        $dataInspecao->setDatabaseMask('yyyy-mm-dd');
        $dataInspecao->setSize(110);
        
        $localAtual = new TDBUniqueSearch('localAtual', 'controlepatrimonio', 'Local', 'id', 'Descricao', 'id asc', $criteria_localAtual);
        $localAtual->setMinLength(2);
        $localAtual->setMask('{Descricao}');
        $localAtual->setSize('40%');
        if (isset($param['localAtual'])) {
            $localAtualValue = $param['localAtual'];
            $localAtual->setValue($localAtualValue);}
        
        $Descricao = new THtmlEditor('Descricao');
        $Descricao->setSize('40%', 110);
        
        $imagem = new TFile('Escolha uma Imagem');
        $imagem->enableFileHandling();
        $imagem->setSize('40%');
        
        $row1 = $this->form->addFields([new TLabel("ID:", null, '14px', null)], [$pesquisaPatrimonioId]);
        $row2 = $this->form->addFields([new TLabel("Código Patrimônio:", null, '14px', null)], [$procura_patrimonio]);
        $row3 = $this->form->addFields([new TLabel("Nome:", null, '14px', null)], [$procura_patrimonio_display]);
        $row4 = $this->form->addFields([new TLabel(" ", null, '14px', null, '100%')],[]);
        $row5 = $this->form->addFields([new TLabel("Código do Patrimonio:", '#ff0000', '14px', null)], [$patrimonioId]);
        $row6 = $this->form->addFields([new TLabel("Local Antigo:", '#ff0000', '14px', null)], [$localAntigo]);
        $row7 = $this->form->addFields([new TLabel("Data da Inspecao:", '#ff0000', '14px', null)], [$dataInspecao]);
        $row8 = $this->form->addFields([new TLabel("Local Atual:", null, '14px', null)], [$localAtual]);
        $row9 = $this->form->addFields([new TLabel("Descricao:", null, '14px', null)], [$Descricao]);
        $row10 = $this->form->addFields([new TLabel("Imagem:", null, '14px', null)], [$imagem]);
        
        $btn_onsave = $this->form->addAction("Salvar", new TAction([$this, 'onSave']), 'far:save #ffffff');
        $this->btn_onsave = $btn_onsave;
        $btn_onsave->addStyleClass('btn-primary'); 
        
        $btn_onclear = $this->form->addAction("Limpar formulário", new TAction([$this, 'onClear']), 'fas:eraser #dd5a43');
        $this->btn_onclear = $btn_onclear;
        
        $btnBack = $this->form->addAction("Voltar", new TAction(['LerCodigoQR', 'onShowBack']), 'fa-backward fa-fw #000000');
        
        $btn_start_scanner = $this->form->addAction("Iniciar Câmera", new TAction([$this, 'onShow']), 'fas:camera #ffffff');
        $btn_start_scanner->addStyleClass('btn-primary');
        $btn_start_scanner->setId('btn-start-scanner'); // Adiciona o ID para o botão
        
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->class = 'form-container';
        if (empty($param['target_container'])) {
            $container->add(TBreadCrumb::create(["Geral", "CadastraMovimentação"]));
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
    }
    
    public function onShowBack($param = null) {
        // Código para voltar para a página anterior, se necessário
    }

    public function onShow($param = null) {
        parent::onShow($param);

        // Adiciona o script para iniciar o QuaggaJS
        $script = "
            <script>
                function startScanner() {
                    Quagga.init({
                        inputStream: {
                            name: 'Live',
                            type: 'LiveStream',
                            target: document.querySelector('#scanner')    // Seleciona o elemento de vídeo
                        },
                        decoder: {
                            readers: ['code_128_reader']  // Adicione outros leitores se necessário
                        }
                    }, function(err) {
                        if (err) {
                            console.log(err);
                            return;
                        }
                        Quagga.start();
                    });

                    // Adiciona o listener para quando o código de barras é lido
                    Quagga.onDetected(function(data) {
                        alert('Código detectado: ' + data.codeResult.code);
                        Quagga.stop(); // Para a leitura após a detecção
                        // Aqui você pode adicionar código para manipular o valor detectado
                    });
                }

                // Espera que o DOM esteja pronto
                document.addEventListener('DOMContentLoaded', function() {
                    // Adiciona um listener ao botão para iniciar o scanner
                    document.querySelector('#btn-start-scanner').addEventListener('click', startScanner);
                });
            </script>
        ";

        // Adiciona o script ao final do conteúdo da página
        $this->form->addHtml($script);

        // Adiciona um contêiner para o scanner de vídeo
        $scannerContainer = new TElement('div');
        $scannerContainer->id = 'scanner';
        $scannerContainer->style = 'width: 100%; height: 400px;'; // Ajuste a altura conforme necessário
        $this->form->add($scannerContainer);
    }
}
