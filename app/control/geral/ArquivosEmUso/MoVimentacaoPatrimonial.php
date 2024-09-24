<?php

class MoVimentacaoPatrimonial extends TPage
{
    public function __construct($param)
    {
        parent::__construct();
      $this->form   = new TForm('form_SaleMultiValue');
        $panel_master = new TPanelGroup( 'movimentações Patrimoniais' );
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $this->form->add($panel_master);
        $panel_master->add($vbox);
        
        $frame_Geral = new TFrame;
        $frame_Geral->class = 'tframe tframe-custom';
        $frame_Geral->setLegend('Patrimônio');
        $frame_Geral->style = 'background:whiteSmoke';
        
        $table_general = new TTable;
        $table_general->width = '100%';
        $frame_Geral->add($table_general);
        
        $frame_Movimento = new TFrame;
        $frame_Movimento->class = 'tframe tframe-custom';
        $frame_Movimento->setLegend('Movimentos'); 
        
        $vbox->add( $frame_Geral );
        $vbox->add( $frame_Movimento );
        $id             = new TEntry('id');
        $date           = new TDate('date');
        $CodigodoPatrimonio    = new TDBSeekButton('CodigodoPatrimonio', 'controlepatrimonio', $this->form->getName(), 'Patrimonio', 'descricao', 'CodigodoPatrimonio', 'descricao');
        $descricao  = new TEntry('descricao');
        $localId           = new TEntry('localId');
        
        $id->setSize(40);
        $id->setEditable(false);
        $date->setSize(100);
        $localId->setSize('100%',50);
        $CodigodoPatrimonio->setSize(50);
        $descricao->setEditable(false);
        $date->addValidation('Date', new TRequiredValidator);
        $CodigodoPatrimonio->addValidation('CodigodoPatrimonio', new TRequiredValidator);
        
        $this->form->addField($id);
        $this->form->addField($date);
        $this->form->addField($CodigodoPatrimonio);
        $this->form->addField($descricao);
        $this->form->addField($localId);
        
        $table_general->addRowSet( new TLabel('ID'), $id );
        $table_general->addRowSet( $label_date     = new TLabel('Data Inspeção'), $date );
        $table_general->addRowSet( $label_patrimonio = new TLabel('Patrimonio'), array( $CodigodoPatrimonio, $descricao ) );
        $table_general->addRowSet( new TLabel('localId'), $localId );
        $label_date->setFontColor('#FF0000');
        
        
        // create detail fields
        $movimentacaoId = new TDBUniqueSearch('movimentacaoId[]', 'controlepatrimonio', 'Movimentacao', 'id', 'patrimonioId');
        $movimentacaoId->setMinLength(1);
        $movimentacaoId->setSize('100%');
        $movimentacaoId->setMask('{description} ({id})');
        $movimentacaoId->setChangeAction(new TAction(array($this, 'onChangeProduct')));
        /*
        $product_price = new TEntry('product_price[]');
        $product_price->setNumericMask(2,',','.', true);
        $product_price->setSize('100%');
        $product_price->setExitAction(new TAction(array($this, 'onUpdateTotal')));
        
        $product_amount = new TEntry('product_amount[]');
        $product_amount->setNumericMask(2,',','.', true);
        $product_amount->setSize('100%');
        $product_amount->setExitAction(new TAction(array($this, 'onUpdateTotal')));
        
        $product_total = new TEntry('product_total[]');
        $product_total->setEditable(FALSE);
        $product_total->setNumericMask(2,',','.', true);
        $product_total->setSize('100%');
        
        $this->form->addField($movimentacaoId);
        $this->form->addField($product_price);
        $this->form->addField($product_amount);
        $this->form->addField($product_total);
        
        // detail
        $this->product_list = new TFieldList;
        $this->product_list->addField( '<b>Product</b>', $movimentacaoId, ['width' => '40%']);
        $this->product_list->addField( '<b>Price</b>', $product_price, ['width' => '20%']);
        $this->product_list->addField( '<b>Amount</b>', $product_amount, ['width' => '20%']);
        $this->product_list->addField( '<b>Total</b>', $product_total, ['width' => '20%']);
        $this->product_list-> width = '100%';
        $frame_Movimento->add($this->product_list);
        */
        $save_button = TButton::create('save', array($this, 'onSave'),  _t('Save'),  'fa:save green');
        $new_button  = TButton::create('new',  array($this, 'onClear'), _t('Clear'), 'fa:eraser red');
        
        // define form fields
        $this->form->addField($save_button);
        $this->form->addField($new_button);
        
        $panel_master->addFooter( THBox::pack($save_button, $new_button) );
        
        // create the page container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        parent::add($container);
    }
    
    /**
     * Executed whenever the user clicks at the edit button da datagrid
     */
    function onEdit($param)
    {
        try
        {
            TTransaction::open('controlepatrimonio');
            
            if (isset($param['key']))
            {
                $key = $param['key'];
                
                $sale = new Sale($key);
                $this->form->setData($sale);
                
                $sale_items = $sale->getSaleItems();
                
                $this->product_list->addHeader();
                if ($sale_items)
                {
                    foreach($sale_items  as $item )
                    {
                        $item->product_price  = $item->sale_price;
                        $item->product_amount = $item->amount;
                        $item->product_total  = $item->sale_price * $item->amount;
                        $this->product_list->addDetail($item);
                    }
                    $this->product_list->addCloneAction();
                }
                else
                {
                    $this->onClear($param);
                }
                
                TTransaction::close(); // close transaction
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    /**
     * Change product
     */
    public static function onChangeProduct($param)
    {
        $input_id = $param['_field_id'];
        $movimentacaoId = $param['_field_value'];
        $input_pieces = explode('_', $input_id);
        $unique_id = end($input_pieces);
        
        if ($movimentacaoId)
        {
            $response = new stdClass;
            
            try
            {
                TTransaction::open('controlepatrimonio');
                $product = Product::find($movimentacaoId);
                $response->{'product_price_'.$unique_id} = number_format($product->sale_price,2,',', '.');
                $response->{'product_amount_'.$unique_id} = '1,00';
                $response->{'product_total_'.$unique_id} = number_format($product->sale_price,2,',', '.');
                TForm::sendData('form_SaleMultiValue', $response);
                TTransaction::close();
            }
            catch (Exception $e)
            {
                TTransaction::rollback();
            }
        }
    }
    
    /**
     * Update the total based on the sale price, amount and discount
     */
    public static function onUpdateTotal($param)
    {
        $input_id = $param['_field_id'];
        $movimentacaoId = $param['_field_value'];
        $input_pieces = explode('_', $input_id);
        $unique_id = end($input_pieces);
        parse_str($param['_field_data'], $field_data);
        $row = $field_data['row'];
        
        $sale_price = (double) str_replace(['.', ','], ['', '.'], $param['product_price'][$row]);
        $amount     = (double) str_replace(['.', ','], ['', '.'], $param['product_amount'][$row]);
        
        $obj = new StdClass;
        $obj->{'product_total_'.$unique_id} = number_format( ($sale_price * $amount), 2, '.', ',');
        TForm::sendData('form_SaleMultiValue', $obj);
    }
    
    /**
     * Clear form
     */
    public function onClear($param)
    {
        $this->product_list->addHeader();
        $this->product_list->addDetail( new stdClass );
        $this->product_list->addCloneAction();
    }
    
    /**
     * Save the sale and the sale items
     */
    public static function onSave($param)
    {
        try
        {
            // open a transaction with database 'controlepatrimonio'
            TTransaction::open('controlepatrimonio');
            
            $id = (int) $param['id'];
            $sale = new Sale($id);
            $sale->date = $param['date'];
            $sale->CodigodoPatrimonio = $param['CodigodoPatrimonio'];
            $sale->localId = $param['localId'];
            $sale->clearParts();
            $total = 0;
            
            if( !empty($param['movimentacaoId']) AND is_array($param['movimentacaoId']) )
            {
                foreach( $param['movimentacaoId'] as $row => $movimentacaoId)
                {
                    if ($movimentacaoId)
                    {
                        $item = new SaleItem;
                        $item->movimentacaoId  = (float) str_replace(['.',','], ['','.'], $param['movimentacaoId'][$row]);
                        $item->sale_price  = (float) str_replace(['.',','], ['','.'], $param['product_price'][$row]);
                        $item->amount      = (float) str_replace(['.',','], ['','.'], $param['product_amount'][$row]);
                        $item->discount    = 0;
                        $item->total       = $item->sale_price * $item->amount;
                        
                        $total += $item->total;
                        $sale->addSaleItem($item);
                    }
                }
            }
            $sale->total = $total;
            $sale->store(); // stores the object
            
            $data = new stdClass;
            $data->id = $sale->id;
            TForm::sendData('form_SaleMultiValue', $data);
            TTransaction::close(); // close the transaction
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
}
    
  
