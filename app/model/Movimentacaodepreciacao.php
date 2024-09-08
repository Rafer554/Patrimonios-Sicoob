<?php

class Movimentacaodepreciacao extends TRecord
{
    const TABLENAME  = 'movimentacaoDepreciacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $fk_patrimonioId;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('patrimonioId');
        parent::addAttribute('dataDepreciacao');
        parent::addAttribute('valor');
            
    }

    /**
     * Method set_Patrimonio
     * Sample of usage: $var->Patrimonio = $object;
     * @param $object Instance of Patrimonio
     */
    public function set_fk_patrimonioId(Patrimonio $object)
    {
        $this->fk_patrimonioId = $object;
        $this->patrimonioId = $object->id;
    }

    /**
     * Method get_fk_patrimonioId
     * Sample of usage: $var->fk_patrimonioId->attribute;
     * @returns Patrimonio instance
     */
    public function get_fk_patrimonioId()
    {
    
        // loads the associated object
        if (empty($this->fk_patrimonioId))
            $this->fk_patrimonioId = new Patrimonio($this->patrimonioId);
    
        // returns the associated object
        return $this->fk_patrimonioId;
    }

    
}

