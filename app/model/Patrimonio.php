<?php

class Patrimonio extends TRecord
{
    const TABLENAME  = 'Patrimonio';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $tido_baixa;
    private $Grupo;

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodigodoPatrimonio');
        parent::addAttribute('descricao');
        parent::addAttribute('ativo');
        parent::addAttribute('responsavel');
        parent::addAttribute('chapa');
        parent::addAttribute('Local_id');
        parent::addAttribute('Grupo_id');
        parent::addAttribute('ValorOriginal');
        parent::addAttribute('ValorAtual');
        parent::addAttribute('DataEntrada');
        parent::addAttribute('imagem');
        parent::addAttribute('tido_baixa_id');
    
        parent::addAttribute('dataentrada');
                            
    }

    /**
     * Method set_tipo_baixa
     * Sample of usage: $var->tipo_baixa = $object;
     * @param $object Instance of TipoBaixa
     */
    public function set_tido_baixa(TipoBaixa $object)
    {
        $this->tido_baixa = $object;
        $this->tido_baixa_id = $object->id;
    }

    /**
     * Method get_tido_baixa
     * Sample of usage: $var->tido_baixa->attribute;
     * @returns TipoBaixa instance
     */
    public function get_tido_baixa()
    {
    
        // loads the associated object
        if (empty($this->tido_baixa))
            $this->tido_baixa = new TipoBaixa($this->tido_baixa_id);
    
        // returns the associated object
        return $this->tido_baixa;
    }
    /**
     * Method set_Grupo
     * Sample of usage: $var->Grupo = $object;
     * @param $object Instance of Grupo
     */
    public function set_Grupo(Grupo $object)
    {
        $this->Grupo = $object;
        $this->Grupo_id = $object->id;
    }

    /**
     * Method get_Grupo
     * Sample of usage: $var->Grupo->attribute;
     * @returns Grupo instance
     */
    public function get_Grupo()
    {
    
        // loads the associated object
        if (empty($this->Grupo))
            $this->Grupo = new Grupo($this->Grupo_id);
    
        // returns the associated object
        return $this->Grupo;
    }

    /**
     * Method getMovimentacaos
     */
    public function getMovimentacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('patrimonioId', '=', $this->id));
        return Movimentacao::getObjects( $criteria );
    }
    /**
     * Method getMovimentacaodepreciacaos
     */
    public function getMovimentacaodepreciacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('patrimonioId', '=', $this->id));
        return Movimentacaodepreciacao::getObjects( $criteria );
    }

    public function set_movimentacao_fk_localAntigo_to_string($movimentacao_fk_localAntigo_to_string)
    {
        if(is_array($movimentacao_fk_localAntigo_to_string))
        {
            $values = Local::where('id', 'in', $movimentacao_fk_localAntigo_to_string)->getIndexedArray('id', 'id');
            $this->movimentacao_fk_localAntigo_to_string = implode(', ', $values);
        }
        else
        {
            $this->movimentacao_fk_localAntigo_to_string = $movimentacao_fk_localAntigo_to_string;
        }

        $this->vdata['movimentacao_fk_localAntigo_to_string'] = $this->movimentacao_fk_localAntigo_to_string;
    }

    public function get_movimentacao_fk_localAntigo_to_string()
    {
        if(!empty($this->movimentacao_fk_localAntigo_to_string))
        {
            return $this->movimentacao_fk_localAntigo_to_string;
        }
    
        $values = Movimentacao::where('patrimonioId', '=', $this->id)->getIndexedArray('localAntigo','{fk_localAntigo->id}');
        return implode(', ', $values);
    }

    public function set_movimentacao_fk_patrimonioId_to_string($movimentacao_fk_patrimonioId_to_string)
    {
        if(is_array($movimentacao_fk_patrimonioId_to_string))
        {
            $values = Patrimonio::where('id', 'in', $movimentacao_fk_patrimonioId_to_string)->getIndexedArray('CodigodoPatrimonio', 'CodigodoPatrimonio');
            $this->movimentacao_fk_patrimonioId_to_string = implode(', ', $values);
        }
        else
        {
            $this->movimentacao_fk_patrimonioId_to_string = $movimentacao_fk_patrimonioId_to_string;
        }

        $this->vdata['movimentacao_fk_patrimonioId_to_string'] = $this->movimentacao_fk_patrimonioId_to_string;
    }

    public function get_movimentacao_fk_patrimonioId_to_string()
    {
        if(!empty($this->movimentacao_fk_patrimonioId_to_string))
        {
            return $this->movimentacao_fk_patrimonioId_to_string;
        }
    
        $values = Movimentacao::where('patrimonioId', '=', $this->id)->getIndexedArray('patrimonioId','{fk_patrimonioId->CodigodoPatrimonio}');
        return implode(', ', $values);
    }

    public function set_movimentacaoDepreciacao_fk_patrimonioId_to_string($movimentacaoDepreciacao_fk_patrimonioId_to_string)
    {
        if(is_array($movimentacaoDepreciacao_fk_patrimonioId_to_string))
        {
            $values = Patrimonio::where('id', 'in', $movimentacaoDepreciacao_fk_patrimonioId_to_string)->getIndexedArray('CodigodoPatrimonio', 'CodigodoPatrimonio');
            $this->movimentacaoDepreciacao_fk_patrimonioId_to_string = implode(', ', $values);
        }
        else
        {
            $this->movimentacaoDepreciacao_fk_patrimonioId_to_string = $movimentacaoDepreciacao_fk_patrimonioId_to_string;
        }

        $this->vdata['movimentacaoDepreciacao_fk_patrimonioId_to_string'] = $this->movimentacaoDepreciacao_fk_patrimonioId_to_string;
    }

    public function get_movimentacaoDepreciacao_fk_patrimonioId_to_string()
    {
        if(!empty($this->movimentacaoDepreciacao_fk_patrimonioId_to_string))
        {
            return $this->movimentacaoDepreciacao_fk_patrimonioId_to_string;
        }
    
        $values = Movimentacaodepreciacao::where('patrimonioId', '=', $this->id)->getIndexedArray('patrimonioId','{fk_patrimonioId->CodigodoPatrimonio}');
        return implode(', ', $values);
    }

}

