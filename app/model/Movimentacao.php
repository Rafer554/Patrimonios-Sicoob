<?php

class Movimentacao extends TRecord
{
    const TABLENAME  = 'movimentacao';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    private $fk_localAntigo;
    private $fk_patrimonioId;

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('localAntigo');
        parent::addAttribute('patrimonioId');
        parent::addAttribute('dataInspecao');
        parent::addAttribute('Descricao');
        parent::addAttribute('imagem');
            
    }

    /**
     * Method set_Local
     * Sample of usage: $var->Local = $object;
     * @param $object Instance of Local
     */
    public function set_fk_localAntigo(Local $object)
    {
        $this->fk_localAntigo = $object;
        $this->localAntigo = $object->id;
    }

    /**
     * Method get_fk_localAntigo
     * Sample of usage: $var->fk_localAntigo->attribute;
     * @returns Local instance
     */
    public function get_fk_localAntigo()
    {
    
        // loads the associated object
        if (empty($this->fk_localAntigo))
            $this->fk_localAntigo = new Local($this->localAntigo);
    
        // returns the associated object
        return $this->fk_localAntigo;
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

