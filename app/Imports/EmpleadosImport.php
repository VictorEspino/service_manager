<?php

namespace App\Imports;

use App\Models\CargaEmpleados;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class EmpleadosImport implements ToModel,WithHeadingRow,WithValidation,WithBatchInserts
{
    use Importable;

    private $carga_id;

    public function __construct($carga_id) 
    {
        $this->carga_id = $carga_id;
    }

    public function model(array $row)
    {
        return new CargaEmpleados([
            'numero_empleado'=>$row['empleado'],
            'nombre'=>trim($row['nombre']).' '.trim($row['apellido_paterno']).' '.trim($row['apellido_materno']),
            'area'=>trim($row['area']),
            'subarea'=>trim($row['subarea']),
            'puesto'=>trim($row['puesto']),
            'estatus'=>trim($row['activo']),
            'carga_id'=>$this->carga_id,
            'user_id_carga'=>Auth::user()->id,
        ]);
    }
    public function rules(): array
    {
        return [
            '*.empleado' => ['required'],
            '*.nombre' => ['required'],
            '*.area' => ['required'],
            '*.subarea' => ['required'],
            '*.puesto' => ['required'],
            '*.activo' => ['required',Rule::in(['Activo','Inactivo'])],
        ];
    }
    public function customValidationMessages()
    {
        return [
            'empleado.required' => 'Campo requerido',
            'empleado.numeric' => 'Se espera valor numerico',
            'nombre.required' => 'Campo requerido',
            'area.required' => 'Campo requerido',
            'subarea.required' => 'Campo requerido',
            'puesto.required' => 'Campo requerido',
            'activo.required' => 'Campo requerido',
        ];
    }
    public function batchSize(): int
    {
        return 50;
    }
}
