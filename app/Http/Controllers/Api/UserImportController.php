<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;

use Illuminate\Http\Request;
use App\Http\Requests\ImportUsersRequest;
use App\Services\UserImportService;

class UserImportController extends BaseController
{
    public function __construct(private UserImportService $service)
    {
    }

    public function store(ImportUsersRequest $request)
    {

        $file = $request->getUploadedFile();

        if (!isset($file)) {
            return $this->sendError("No se ha proporcionado ningun fichero o no se ha podido acceder", [], 400);
        }

        // Pasamos el objeto al servicio y que él se pelee con la lógica
        $result = $this->service->import($file);

        if ($result) {
            $this->sendMessage('Se ha realizado la importacion correctamente', 200);
        }

        
    }

    public function getLogs(Request $request) {

        $perPage = 100;
        $page = $request->query('page', 1);
        $linesToSkip = ($page - 1) * $perPage;
        $logs = [];
        $currentLine = 0;

        $handle = fopen(storage_path('logs/imports.log'), 'r');

        $payload = [
            'data' => [],
            'next_page' => null,
            'previous_page' => null,
        ];

        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if ($currentLine >= $linesToSkip) {
                    $logs[] = trim($line);
                }

                $currentLine++;

                if (count($logs) === $perPage) {

                    //Leemos una linea mas del fichero cuando llegamos a la misma longitud de logs
                    $line = fgets($handle);

                    //Si existe valor significa que hay mas logs aun
                    if ($line) {
                        $payload['next_page'] = $page + 1;
                    } else {
                        $payload['next_page'] = null;
                    }
                    
                    break;
                }
            }

            fclose($handle);
        }

        
        $payload ['data'] = $logs;

        if ($page > 1 && count($logs) > 0) {
            $payload['previous_page'] = $page - 1;
        }


        return $this->sendResponse($payload, "Se han obtenido los logs correctamente", 200);

        
        
    }

    

}
