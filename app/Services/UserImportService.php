<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Role;

class UserImportService {

    private $configValidation = [
        "roles" => ['client', 'admin']
    ];

    public function import(UploadedFile $file) {
        $logPath = storage_path('logs/imports.log');

        if (file_exists($logPath)) {
            unlink($logPath);
        }

        Storage::deleteDirectory('imports');

        $path = $file->store('imports');
    
        $spreadsheet = IOFactory::load($file->getRealPath());

        $sheet = $spreadsheet->getSheet(0);

        Log::channel('imports')->info('Empezando importación del fichero: ' . $file->getClientOriginalName());


        //HARDCODED
        /*$allUsers = User::all(['nombre_usuario', 'email'])->toArray();
        $takenEmails = array_column($allUsers, 'email');
        $takenUserNames = array_column($allUsers, 'nombre_usuario'); */

        foreach ($sheet->getRowIterator(2) as $index => $row) { 
            $this->newLog("info", "Procesando fila numero " . $index);

            $cellIterator = $row->getCellIterator();

            $cellIterator->setIterateOnlyExistingCells(false);

            $newUser = [
                    "nombre" => '',
                    "apellidos" => '',
                    "nombre_usuario" => '',
                    "contraseña" => '',
                    "email" => '',
                    "rol" => ""
            ];

            foreach($cellIterator as $column) {
                $columnWord = $column->getColumn();

                $columnValue = $column->getValue();

                switch ($columnWord) {
                    case('A'):
                        $newUser['nombre'] = $columnValue;
                        break;
                    case('B'):
                        $newUser['apellidos'] = $columnValue;
                        break;
                    case('C'):
                        if($this->checkUserName($columnValue)) {
                            $newUser['nombre_usuario'] = $columnValue;
                        } else {
                            $this->newLog('bad-value', "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "El nombre de usuario esta siendo usado"
                            ]);
                            continue 3;
                        }
                        break;
                    case('D'):
                        if($this->checkPassword($columnValue)) {
                            $newUser['contraseña'] = $columnValue;
                        } else {
                            $this->newLog('error', "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "La contraseña no es segura"
                            ]);
                            continue 3;
                        }
                        break;
                    case('E'):
                        if ($this->checkEmail($columnValue)) {
                            $newUser['email'] = $columnValue;
                        } else {
                            $this->newLog('error', "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "El email esta siendo usado"
                            ]);
                            continue 3;
                        }
                        break;
                    case('F'): 
                        if ($this->checkRole($columnValue)) {
                            $idRole = $this->getRoleId($columnValue);
                            $newUser['rol'] = $idRole;
                        } else {
                            $this->newLog('error', 'Error en la fila', [
                                'Fila' => $index,
                                'Descripcion' => "El rol es invalido"
                            ]);
                            continue 3;
                        }
                        break;
                        

                        //HARDCODED 
                        /* else if (!in_array(strtolower($columnValue), $takenEmails)) {
                            $this->newLog('error', "Error en la fila", [
                                "Fila" => $index,
                                "Descripcion" => "El email esta siendo usado"
                            ]);
                        } */
                }
            }

            User::create($newUser);

        }
        
        return true;

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

    public function checkUserName(string $value): bool {

        return Validator::make(
            ['nombre_usuario' => $value], 
            ['nombre_usuario' => 'unique:users,nombre_usuario']
        )->passes();
        
    }

    public function checkPassword(string $value): bool {

        return Validator::make(
            ['p' => $value],
            ['p' => Password::defaults()] 
            
        )->passes();
        
    }

    public function checkEmail(string $value): bool {

        return Validator::make(
            ['email' => $value], 
            ['email' => 'email:rfc,unique:users,email']
        )->passes();
        
    }

    public function checkRole(string $value): bool {
        return Validator::make(
            ['role' => $value],
            ['role' => 'exists:roles,rol']
        )->passes();
    }

    public function getRoleId(string $value): int | false {
        $role = Role::where('rol', $value);

        if ($role) {
            return $role->id;
        } else {
            return false;
        }
    }


}