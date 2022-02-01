<?php


namespace App\Helpers;


use App\Models\Usuario;
use Exception;
use Log;

class BuscadorUsuariosHelper extends Helper {
    private $order;
    private $filtros;
    private $pagina;
    private $rows;
    private $relaciones;
    private $metadata;
    private $usuarios;


    public function __construct($order, $filtros, $pagina, $rows = 50, $relaciones = []) {
        $this->order = $order;
        $this->filtros = $filtros;
        $this->pagina = isset($pagina) ? $pagina : 1;
        $this->rows = isset($rows) ? $rows : 50;
        $this->relaciones = isset($relaciones) ? $relaciones : [];
        $this->metadata = [];
        $this->usuarios = [];
    }

    public function consultar() {
        $this->setMetadata();
        $this->setUsuarios();
        return ['metadata' => $this->metadata, 'usuarios' => $this->usuarios];
    }

    private function getTotalRegistros() {
        $this->usuarios = Usuario::where('id', '>', 0);
        $this->aplicaFiltros();
        return $this->usuarios->count();
    }

    private function setUsuarios() {
        $relaciones = ['info_personal'];
        $this->usuarios = Usuario::where('id', '>', 0);
        $this->aplicaFiltros();
        $this->aplicaLimits();
        if (isset($this->relaciones) && count($this->relaciones) > 0)
            $relaciones = array_merge($relaciones, $this->relaciones);
        $this->usuarios = $this->usuarios->with($relaciones);
        $this->usuarios = $this->usuarios->get();
        $this->aplicaOrder();
    }

    private function aplicaFiltros() {
        if (isset($this->filtros['id']))
            $this->usuarios->where('id', 'like', '%' . $this->filtros['id'] . '%');
        if (isset($this->filtros['email']))
            $this->usuarios->where('email', 'like', '%' . $this->filtros['email'] . '%');
        if (isset($this->filtros['nombre']))
            $this->usuarios->whereHas('info_personal', function ($q) { return $q->where('nombre', 'like', '%' . $this->filtros['nombre'] . '%'); });
        if (isset($this->filtros['apellidos']))
            $this->usuarios->whereHas('info_personal', function ($q) { return $q->where('apellidos', 'like', '%' . $this->filtros['apellidos'] . '%'); });
    }

    private function aplicaLimits() {
        $this->usuarios->skip(($this->pagina - 1) * $this->metadata['rows_por_pagina']);
        $this->usuarios->take($this->metadata['rows_por_pagina']);

    }

    private function aplicaOrder() {
        if (in_array($this->order['key'], ['id', 'email']))
            $ordenados = $this->order['tipo'] == 'asc' ? $this->usuarios->sortBy($this->order['key']) : $this->usuarios->sortByDesc($this->order['key']);
        if ($this->order['key'] == 'nombre')
            if ($this->order['tipo'] == 'asc')
                $ordenados = $this->usuarios->sortBy(function ($usuario) { return strtolower($usuario->info_personal->nombre); } );
            else
                $ordenados = $this->usuarios->sortByDesc(function ($usuario) { return strtolower($usuario->info_personal->nombre); } );
        if ($this->order['key'] == 'apellidos')
            if ($this->order['tipo'] == 'asc')
                $ordenados = $this->usuarios->sortBy(function ($usuario) { return strtolower($usuario->info_personal->apellidos); } );
            else
                $ordenados = $this->usuarios->sortByDesc(function ($usuario) { return strtolower($usuario->info_personal->apellidos); } );
        $this->usuarios = $ordenados->values()->all();
    }

    private function setMetadata() {
        $total_registros = $this->getTotalRegistros();
        $this->metadata = [
            'total_registros' => $total_registros,
            'rows_por_pagina' => $this->rows,
            'paginas' => $total_registros > 0 ? ceil($total_registros / $this->rows) : 0,
            'pagina' => $this->pagina,
        ];
    }

}
