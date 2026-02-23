<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ImportProductsRequest;
use App\Services\ProductImportService;

class ProductImportController extends Controller
{
    public function __construct(private ProductImportService $service) {}

    public function index()
    {
        return view('products.import');
    }

    public function store(ImportProductsRequest $request) 
    {
        // Da igual si el input se llama 'file', 'csv' o 'XyZ',
        // el método del Request ya sabe buscarlo.
        $file = $request->getUploadedFile();

        // Pasamos el objeto al servicio y que él se pelee con la lógica
        $this->service->import($file);

        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Importación realizada correctamente']);
        }

        return redirect()->route('import.index')->with('success', 'Importación realizada correctamente');
    }

}
