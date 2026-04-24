<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\ImportUsersRequest;
use App\Services\UserImportService;

class UserImportController extends Controller
{
    public function __construct(private UserImportService $service)
    {
    }

    public function store(ImportUsersRequest $request)
    {

        $file = $request->getUploadedFile();

        // Pasamos el objeto al servicio y que él se pelee con la lógica
        $this->service->import($file);

        return response()->json(['message' => 'Importación realizada correctamente']);
    }

    

}
