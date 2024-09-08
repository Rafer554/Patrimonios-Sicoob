<?php

class Grupo extends TRecord
{
    const TABLENAME  = 'Grupo';
    const PRIMARYKEY = 'id';
    const IDPOLICY   =  'serial'; // {max, serial}

    

    /**
     * Constructor method
     */
    public function __construct($id = NULL, $callObjectLoad = TRUE)
    {
        parent::__construct($id, $callObjectLoad);
        parent::addAttribute('CodGrupoPatrimonio');
        parent::addAttribute('tipoDepreciacao');
        parent::addAttribute('valorDepreciacao');
        parent::addAttribute('column_5');
            
    }

    /**
     * Method getPatrimonios
     */
    public function getPatrimonios()
    {
        $criteria = new TCriteria;
        $criteria->add(new TFilter('Grupo_id', '=', $this->id));
        return Patrimonio::getObjects( $criteria );
    }

    public function set_Patrimonio_Grupo_to_string($Patrimonio_Grupo_to_string)
    {
        if(is_array($Patrimonio_Grupo_to_string))
        {
            $values = Grupo::where('id', 'in', $Patrimonio_Grupo_to_string)->getIndexedArray('id', 'id');
            $this->Patrimonio_Grupo_to_string = implode(', ', $values);
        }
        else
        {
            $this->Patrimonio_Grupo_to_string = $Patrimonio_Grupo_to_string;
        }

        $this->vdata['Patrimonio_Grupo_to_string'] = $this->Patrimonio_Grupo_to_string;
    }

    public function get_Patrimonio_Grupo_to_string()
    {
        if(!empty($this->Patrimonio_Grupo_to_string))
        {
            return $this->Patrimonio_Grupo_to_string;
        }
    
        $values = Patrimonio::where('Grupo_id', '=', $this->id)->getIndexedArray('Grupo_id','{Grupo->id}');
        return implode(', ', $values);
    }

    public function set_Patrimonio_tido_baixa_to_string($Patrimonio_tido_baixa_to_string)
    {
        if(is_array($Patrimonio_tido_baixa_to_string))
        {
            $values = TipoBaixa::where('id', 'in', $Patrimonio_tido_baixa_to_string)->getIndexedArray('id', 'id');
            $this->Patrimonio_tido_baixa_to_string = implode(', ', $values);
        }
        else
        {
            $this->Patrimonio_tido_baixa_to_string = $Patrimonio_tido_baixa_to_string;
        }

        $this->vdata['Patrimonio_tido_baixa_to_string'] = $this->Patrimonio_tido_baixa_to_string;
    }

    public function get_Patrimonio_tido_baixa_to_string()
    {
        if(!empty($this->Patrimonio_tido_baixa_to_string))
        {
            return $this->Patrimonio_tido_baixa_to_string;
        }
    
        $values = Patrimonio::where('Grupo_id', '=', $this->id)->getIndexedArray('tido_baixa_id','{tido_baixa->id}');
        return implode(', ', $values);
    }

    
}

