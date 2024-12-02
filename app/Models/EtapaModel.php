<?php

namespace App\Models;

use CodeIgniter\Model;

class EtapaModel extends Crud_model 
{
    
    protected $table = null;
    
    function __construct() {
        $this->table = 'etapa';
        parent::__construct($this->table);

    }

    public function getEtapasComSubetapas()
    {
        return $this->select('etapas.*, subetapas.nome AS subetapa_nome')
                    ->join('subetapas', 'subetapas.etapa_id = etapas.id', 'left')
                    ->findAll();
    }
}