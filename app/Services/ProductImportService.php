<?php

namespace App\Services;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Repositories\ProductRepository;


class ProductImportService {


    private $configValidation = [
        'ajustes'    => ["ajustado", "holgado","normal"],
        'alturas'    => ["alto", "bajo", "normal", ""],
        'tallas'     => ["s", "m", "l", "xl"],
        'deportes'   => ["trail", "futbol", "tenis", "padel", "baloncesto"],
        'categorias' => ["zapatillas", "camisetas", "pantalones"],
        'sexos'      => ["h", "m", "hombre", "mujer", "niño", "niña"]
    ];



    public function __construct(private ProductRepository $repo) {}



    public function import(UploadedFile $file) {
        Storage::cleanDirectory('imports');

        $path = $file->store('imports');
    
        $spreadsheet = IOFactory::load($file->getRealPath());

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

            //Iterar cada celda de una fila            
            foreach ($cellIterator as $cell) {
                $value = $cell->getValue();
                $col = $cell->getColumn();
                
                $newProd = [
                    "id" => 0,"sku" => "", "Categoria" => "", "Nombre" => "", "Precio" => 0.0,
                    "Talla" => "", "Color" => "", "Stock" => 0, "Ajuste" => "",
                    "Sexo" => "", "Descripcion" => "", "Altura" => "", "Deporte" => "", "Oferta" => "", "Img" => ""
                ];

                switch ($col) {
                    case "A": // ID
                        if (is_numeric($value)) $newProd["id"] = (int)$value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "No existe id o valor invalido"
                            ]);
                            continue 3;
                        }
                        break;
                    
                    case "B": //sku
                        $newProd["sku"] = $value;
                        break;

                    case "C": // Categoría
                        if (in_array(strtolower($value), $this->configValidation['categorias'])) $newProd["Categoria"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "No existe la categoria"
                            ]);
                            continue 3;
                        }
                        break;

                    case "D": // Nombre
                        $newProd["Nombre"] = $value;
                        break;

                    case "E": // Precio
                        $precio = limpiarPrecio($value);
                        if (is_numeric($precio)) $newProd["Precio"] = (float)$precio;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "Precio invalido"
                            ]);
                            continue 3;
                        }
                        break;

                    case "F": // Talla
                        if (is_numeric($value) || in_array(strtolower($value), $this->configValidation['tallas'])) $newProd["Talla"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "La talla no existe"
                            ]);
                            continue 3;
                        }
                        break;

                    case "G": // Color
                        $newProd["Color"] = $value;
                        break;

                    case "H": // Stock
                        if (is_numeric($value)) $newProd["Stock"] = (int)$value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "El stock no es numerico"
                            ]);
                            continue 3;
                        }
                        break;

                    case "I": // Ajuste
                        if (in_array(strtolower($vavaluel), $this->configValidation['ajustes'])) $newProd["Ajuste"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "No el ajuste de la prenda"
                            ]);
                            continue 3;
                        }
                        break;

                    case "J": // Altura
                        if (in_array(strtolower($value), $this->configValidation['alturas'])) $newProd["Altura"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "No existe la altura de la prenda"
                            ]);
                            continue 3;
                        }
                        break;

                    case "K": // Deporte
                        if (in_array(strtolower($value), $this->configValidation['deportes'])) $newProd["Deporte"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "El deporte no existe"
                            ]);
                            continue 3;
                        }
                        break;

                    case "L": // Oferta
                        if (in_array(strtolower($value), ["no", "yes"])) $newProd["Oferta"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "Valor de oferta no valido"
                            ]);
                            continue 3;
                        }
                        break;

                    case "M": // Sexo
                        if (in_array(strtolower($value), $this->configValidation['sexos'])) $newProd["Sexo"] = $value;
                        else {
                            $this->newLog("bad-value", "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "No existe el sexo"
                            ]);
                            continue 3;
                        }
                        break;

                    case "N": // Descripción
                        $newProd["Descripcion"] = $value;
                        break;
                    case "O";
                        $newProd["Img"] = $value;
                        break; 
                }
            }

            //Mensaje de fila procesada
            $this->newLog("info", "Fila numero " . $index . " procesada correctamente");

            //Añadir a la BBDD el nuevo producto
            $this->repo->create($newProd);

            
        }


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