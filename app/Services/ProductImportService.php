<?php

namespace App\Services;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Repositories\ProductsRepository;
use Illuminate\Support\Facades\Log;


class ProductImportService {


    private $configValidation = [
        'ajustes'    => ["ajustado", "holgado","normal"],
        'marcas'     => ["nike", "adidas", "puma", "asics"],
        'alturas'    => ["alto", "bajo", "normal", ""],
        'tallas'     => ["s", "m", "l", "xl"],
        'deportes'   => ["trail", "futbol", "tenis", "padel", "baloncesto"],
        'categorias' => ["zapatillas", "camisetas", "pantalones"],
        'sexos'      => ["h", "m", "hombre", "mujer", "niño", "niña"]
    ];

    public function __construct(private ProductsRepository $repo) {}


    

    public function import(UploadedFile $file) {
        // Limpiar el log de importaciones anteriores
        $logPath = storage_path('logs/imports.log');
        if (file_exists($logPath)) {
            @unlink($logPath);
        }

        // En lugar de borrar y crear el directorio (que causa problemas de permisos en Docker),
        // guardamos el archivo con un nombre único en el disco local ('storage/app')
        $filename = 'import_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('imports', $filename, 'local');
    
        // Obtener la ruta absoluta correcta
        $absolutePath = Storage::disk('local')->path($path);
        $spreadsheet = IOFactory::load($absolutePath);

        $sheet = $spreadsheet->getSheet(0);

        $saltarPrimeraLinea = true;

        Log::channel('imports')->info('Empezando importación del fichero: ' . $file->getClientOriginalName());

        //Iterar cada fila de la hoja
        foreach ($sheet->getRowIterator() as $index => $row) {

            if ($saltarPrimeraLinea) {
                $saltarPrimeraLinea = false;
                continue;
            }

            $this->newLog("info","Procesando fila numero " . $index);

            $cellIterator = $row->getCellIterator();

            $cellIterator->setIterateOnlyExistingCells(false); 

            $newProd = [
                    "sku" => "", "marca" => "", "categoria" => "", "nombre" => "", "precio" => 0.0,
                    "talla" => "", "color" => "", "stock" => 0, "ajuste" => "",
                    "sexo" => "", "descripcion" => "", "altura" => "", "deporte" => "", "oferta" => "", "img" => ""
                ];

            //Iterar cada celda de una fila            
            foreach ($cellIterator as $cell) {
                $value = $cell->getValue();
                $col = $cell->getColumn();
                
            
                switch ($col) {
                    /*case "A": // ID
                        if (is_numeric($value)) $newProd["id"] = (int)$value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "No existe id o valor invalido"
                            ]);
                            continue 3;
                        }
                        break;*/
                    
                    case "A": //sku
                        $newProd["sku"] = $value;
                        break;
                    case "B": //marca
                        if (in_array(strtolower($value), $this->configValidation['marcas'])) $newProd["marca"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "No existe la marca"
                            ]);
                            continue 3;
                        }
                        break;

                    case "C": // Categoría
                        if (in_array(strtolower($value), $this->configValidation['categorias'])) $newProd["categoria"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "No existe la categoria"
                            ]);
                            continue 3;
                        }
                        break;

                    case "D": // Nombre
                        $newProd["nombre"] = $value;
                        break;

                    case "E": // Precio
                        $precio = $this->cleanPrice($value);
                        if (is_numeric($precio)) $newProd["precio"] = (float)$precio;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "Precio invalido"
                            ]);
                            continue 3;
                        }
                        break;

                    case "F": // Talla
                        if (is_numeric($value) || in_array(strtolower($value), $this->configValidation['tallas'])) $newProd["talla"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "La talla no existe"
                            ]);
                            continue 3;
                        }
                        break;

                    case "G": // Color
                        $newProd["color"] = $value;
                        break;

                    case "H": // Stock
                        if (is_numeric($value)) $newProd["stock"] = (int)$value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "El stock no es numerico"
                            ]);
                            continue 3;
                        }
                        break;

                    case "I": // Ajuste
                        if (in_array(strtolower($value), $this->configValidation['ajustes'])) $newProd["ajuste"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "No el ajuste de la prenda"
                            ]);
                            continue 3;
                        }
                        break;

                    case "J": // Altura
                        if (in_array(strtolower($value), $this->configValidation['alturas'])) $newProd["altura"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "No existe la altura de la prenda"
                            ]);
                            continue 3;
                        }
                        break;

                    case "K": // Deporte
                        if (in_array(strtolower($value), $this->configValidation['deportes'])) $newProd["deporte"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "El deporte no existe"
                            ]);
                            continue 3;
                        }
                        break;

                    case "L": // Oferta
                        $valLower = strtolower($value);
                        if (in_array($valLower, ["no", "yes", "si", "sí"])) {
                            $newProd["oferta"] = ($valLower === 'yes' || $valLower === 'si' || $valLower === 'sí');
                        }
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "Valor de oferta no valido"
                            ]);
                            continue 3;
                        }
                        break;

                    case "M": // Sexo
                        if (in_array(strtolower($value), $this->configValidation['sexos'])) $newProd["sexo"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "No existe el sexo"
                            ]);
                            continue 3;
                        }
                        break;

                    case "N": // Descripción
                        $newProd["descripcion"] = $value;
                        break;
                    case "O";
                        $newProd["img"] = $value;
                        break; 
                }
            }

            // Validar campos obligatorios
            $camposObligatorios = [
                'sku' => 'SKU',
                'nombre' => 'Nombre',
                'precio' => 'Precio',
                'stock' => 'Stock'
            ];

            foreach ($camposObligatorios as $campo => $label) {
                if (empty($newProd[$campo]) && $newProd[$campo] !== 0 && $newProd[$campo] !== 0.0) {
                    $this->newLog("bad-value", "Error en la fila $index", [
                        "Fila" => $index,
                        "Descripcion" => "El campo obligatorio '$label' está vacío"
                    ]);
                    continue 2; // Salta a la siguiente fila
                }
            }

            //Mensaje de fila procesada
            $this->newLog("info", "Fila numero " . $index . " procesada correctamente");

            //Añadir a la BBDD el nuevo producto
            $this->repo->create($newProd);

            
        }


    }

    function cleanPrice($value) {
        $clean = preg_replace('/[^\d,.]/', '', $value);
        return str_replace(',', '.', $clean);
    }

    public function newLog(string $type, string $message, array $context = []) {
        $logger = Log::channel('imports');

        switch ($type) {
            case "info":
                $logger->info($message, $context);
                break;
            case "bad-value":
                $logger->error($message, $context);
                break;
            case "error":
                $logger->emergency($message, $context);
                break;
            default:
                $logger->debug($message, $context);
                break;
        }
    }


    
}