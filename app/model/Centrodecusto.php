<?php

class Centrodecusto extends TRecord
{
    const TABLENAME  = 'CentrodeCusto';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CentroCusto');
        parent::addAttribute('Descricao');
            
    }

    /**
     * Method getLocals
     */
    public function getLocals()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('CentrodeCusto_id', '=', $this->id));
        return Local::getObjects( $criteria );
    }

    public function set_Local_CentrodeCusto_to_string($Local_CentrodeCusto_to_string)
    {
        if(is_array($Local_CentrodeCusto_to_string))
        {
            $values = Centrodecusto::where('id', 'in', $Local_CentrodeCusto_to_string)->getIndexedArray('CentroCusto', 'CentroCusto');
            $this->Local_CentrodeCusto_to_string = implode(', ', $values);
        }
        else
        {
            $this->Local_CentrodeCusto_to_string = $Local_CentrodeCusto_to_string;
        }

        $this->vdata['Local_CentrodeCusto_to_string'] = $this->Local_CentrodeCusto_to_string;
    }

    public function get_Local_CentrodeCusto_to_string()
    {
        if(!empty($this->Local_CentrodeCusto_to_string))
        {
            return $this->Local_CentrodeCusto_to_string;
        }
    
        $values = Local::where('CentrodeCusto_id', '=', $this->id)->getIndexedArray('CentrodeCusto_id','{CentrodeCusto->CentroCusto}');
        return implode(', ', $values);
    }

    
}

