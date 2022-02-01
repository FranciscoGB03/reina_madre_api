<?php


namespace App\Helpers;

use App\Models\Propiedad;
use DB;
use Exception;
use Log;

class BuscadorPropiedadesHelper extends Helper {
    private $sort;
    private $filtros;
    private $pagina;
    private $rows;
    private $relaciones;
    private $metadata;
    private $propiedades;
    private $total_registros;

    public function __construct($sort, $filtros, $pagina, $rows = 50, $relaciones = []) {
        $this->sort = $sort;
        $this->filtros = $filtros;
        $this->pagina = isset($pagina) ? $pagina : 1;
        $this->rows = isset($rows) ? $rows : 50;
        $this->relaciones = isset($relaciones) ? $relaciones : [];
        $this->metadata = [];
        $this->propiedades = [];
        $this->total_registros = 0;
    }

    public function consultar() {
        $this->setPropiedades();
        //$this->setMetadata();
        return ['metadata' => $this->metadata, 'propiedades' => $this->propiedades];
    }

    private function aplicaFiltros() {
        if (isset($this->filtros['cliente_id']) && $this->filtros['cliente_id'] != "")
            $this->propiedades = $this->propiedades->porCliente($this->filtros['cliente_id']);
        if (isset($this->filtros['direccion']) && $this->filtros['direccion'] != "")
            $this->propiedades = $this->propiedades->porDireccion($this->filtros['direccion']);
        if (isset($this->filtros['estatus']) && $this->filtros['estatus'] != "")
            $this->propiedades = $this->propiedades->porEstatus($this->filtros['estatus']);
        if (isset($this->filtros['fecha_corte_max']) && $this->filtros['fecha_corte_max'] != "")
            $this->propiedades = $this->propiedades->porFechaCorte('<=', $this->filtros['fecha_corte_max']);
        if (isset($this->filtros['fecha_corte_min']) && $this->filtros['fecha_corte_min'] != "")
            $this->propiedades = $this->propiedades->porFechaCorte('>=', $this->filtros['fecha_corte_min']);
        if (isset($this->filtros['responsable_id']) && $this->filtros['responsable_id'] != "")
            $this->propiedades = $this->propiedades->porResponsable($this->filtros['responsable_id']);
        if (isset($this->filtros['subcategoria_id']) && $this->filtros['subcategoria_id'] != "")
            $this->propiedades = $this->propiedades->porSubcategoria($this->filtros['subcategoria_id']);
    }

    private function aplicaLimits() {
        $this->propiedades = $this->propiedades->skip(($this->pagina - 1) * $this->metadata['rows_por_pagina']);
        $this->propiedades = $this->propiedades->take($this->metadata['rows_por_pagina']);

    }

    private function aplicaOrder() {
        Log::debug($this->sort);
        if (in_array($this->sort['key'], ['id', 'nombre', 'direccion', 'fecha_corte', 'renta_mensual', 'estatus', 'observaciones']))
            $this->propiedades = $this->propiedades->orderBy($this->sort['key'], $this->sort['tipo']);
        if ($this->sort['key'] == 'subcategoria_nombre')
            $this->propiedades = $this->propiedades
                ->leftJoin('subcategoria', 'propiedad.subcategoria_id', 'subcategoria.id')
                ->orderBy('subcategoria.nombre', $this->sort['tipo']);
        if ($this->sort['key'] == 'categoria_nombre')
            $this->propiedades = $this->propiedades
                ->leftJoin('subcategoria', 'propiedad.subcategoria_id', 'subcategoria.id')
                ->leftJoin('categoria', 'subcategoria.categoria_id', 'categoria.id')
                ->orderBy('categoria.nombre', $this->sort['tipo']);
        if ($this->sort['key'] == 'responsable_nombre')
            $this->propiedades = $this->propiedades
                ->leftJoin('responsable', 'propiedad.responsable_id', 'responsable.id')
                ->orderBy('responsable.nombre', $this->sort['tipo']);
        if ($this->sort['key'] == 'cliente_nombre')
            $this->propiedades = $this->propiedades
                ->leftJoin('cliente', 'propiedad.cliente_id', 'cliente.id')
                ->orderBy('cliente.nombre', $this->sort['tipo']);
    }


    private function setPropiedades() {
        $this->propiedades = Propiedad::where('id', '>', 0);
        //DB::enableQueryLog();
        $relaciones = [];
        $this->aplicaOrder();
        $this->aplicaFiltros();
        if (isset($this->relaciones) && count($this->relaciones) > 0)
            $relaciones = array_merge($relaciones, $this->relaciones);
        $this->propiedades = $this->propiedades->with($relaciones);
        $this->propiedades = $this->propiedades->get();
        $this->total_registros = count($this->propiedades);
        $this->setMetadata();
        //Log::debug(DB::getQueryLog());
        $this->aplicaLimits();
        $this->propiedades = $this->propiedades->values()->all();
    }


    private function setMetadata() {
        $this->metadata = [
            'total_registros' => $this->total_registros,
            'rows_por_pagina' => $this->rows,
            'paginas' => $this->total_registros > 0 ? ceil($this->total_registros / $this->rows) : 0,
            'pagina' => $this->pagina,
        ];
    }

}
