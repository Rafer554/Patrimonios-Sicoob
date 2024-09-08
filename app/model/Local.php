<?php

class Local extends TRecord
{
    const TABLENAME  = 'Local';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $CentrodeCusto;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('Descricao');
        parent::addAttribute('CentrodeCusto_id');
        parent::addAttribute('Local');
        parent::addAttribute('responsavel');
        parent::addAttribute('chapa');
            
    }

    /**
     * Method set_CentrodeCusto
     * Sample of usage: $var->CentrodeCusto = $object;
     * @param $object Instance of Centrodecusto
     */
    public function set_CentrodeCusto(Centrodecusto $object)
    {
        $this->CentrodeCusto = $object;
        $this->CentrodeCusto_id = $object->id;
    }

    /**
     * Method get_CentrodeCusto
     * Sample of usage: $var->CentrodeCusto->attribute;
     * @returns Centrodecusto instance
     */
    public function get_CentrodeCusto()
    {
    
        // loads the associated object
        if (empty($this->CentrodeCusto))
            $this->CentrodeCusto = new Centrodecusto($this->CentrodeCusto_id);
    
        // returns the associated object
        return $this->CentrodeCusto;
    }

    /**
     * Method getMovimentacaos
     */
    public function getMovimentacaos()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('localAntigo', '=', $this->id));
        return Movimentacao::getObjects( $criteria );
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
    
        $values = Movimentacao::where('localAntigo', '=', $this->id)->getIndexedArray('localAntigo','{fk_localAntigo->id}');
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
    
        $values = Movimentacao::where('localAntigo', '=', $this->id)->getIndexedArray('patrimonioId','{fk_patrimonioId->CodigodoPatrimonio}');
        return implode(', ', $values);
    }

    
}

