<?php
namespace App\Http\Controllers\Api;

use App\Enums\TallaCategoria;
use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use App\Models\Variation;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Http\Requests\Auth\CreateUserRequest;

use App\Http\Requests\Auth\AddRoleRequest;
use App\Http\Requests\Auth\UpdateRoleRequest;

use App\Http\Requests\Auth\AddProductRequest;
use App\Http\Requests\Auth\UpdateProductRequest;

use App\Http\Resources\SizeResource;

use App\Models\ImageGallery;
use App\Models\ImageGalleryVariation;

use App\Models\Talla;
use App\Models\Color;

use Illuminate\Http\Request;

class AdminController extends BaseController {
    

    public function getAllUsers() {
        $allUsers = User::all();

        return $this->sendResponse(UserResource::collection($allUsers), "Se han obtenido correctamente los usuarios", 200);
    }

    public function getAllRoles() {
        $allRoles = Role::all();

        return $this->sendResponse($allRoles, "Se han obtenido los roles del sistema", 200);
    }

    public function addRole(AddRoleRequest $request) {
        $validatedData = $request->validated();

        Role::create($validatedData);
    }

    public function updateRole(Role $role, UpdateRoleRequest $request) {
        $validatedData = $request->validated();

        $role->update($validatedData);
    }

    public function deleteRole(Role $role) {
        $role->delete();
    }

    public function updateUser(User $user, UpdateUserRequest $request) {
        $validated = $request->validated();

        //HARDCODED BUSQUEDA DE EMAILS USADOS
        /* $takenEmails = array_column(
            array_filter(User::all(['id','email'])->toArray(), function ($value) use ($user) {
                $user->id !== $value['id'];
            }   
        ), 'email');

        
        if (array_search($user->email, $takenEmails)) {
            return $this->sendError('El email que has especificado esta siendo usado', ['email' => ["El email esta siendo usado"]], 400);
        } else {
            $user->update($validated);
            return $this->sendMessage("Se ha actualizado correctamente el usuario", 200);
        } */

        $user->update($validated);
        return $this->sendMessage("Se ha actualizado correctamente el usuario", 200);

    }

    public function createUser(CreateUserRequest $request) {
        $validated = $request->validated();

        User::create($validated);

        return $this->sendMessage("Se ha añadido correctamente el usuario", 200);
    }

    public function deleteUser(User $user) {
        $user->delete();
    }

    public function updateProduct(Product $product, UpdateProductRequest $request) {
        $validated = $request->validated();

        unset($validated['sku']);
        unset($validated['imagen_main']);
        unset($validated['imagenes_secundarias']);

        //Variables para almacenar los nombres de las imagenes guardadas y añadirlas a la BBDD
        $savedImages = [];
        $savedMainImage = "";

        $errors = [];

        $productMainImg = $request->file('imagen_main');

        //Necesarios para la URL completa donde se almacenaran las imagenes
        $productCategory = strtolower($validated['categoria']);
        $productBrand = strtoupper($validated['marca'][0]) . substr($validated['marca'], 1);

        //Necesarias para saber cuales eran la antigua categoria y marca donde estaban almacenadas las imagenes antiguas
        $oldProductCategory = strtolower($product->category);
        $oldProductBrand = strtoupper($product->marca[0]) . substr($product->marca, 1);

        $oldProductImgGallery = $product->imgGallery;
        //Necesario para saber la imagen antigua que hay que eliminar
        $oldProductImg = $product->img;

        // Inicializamos 'img' con la imagen actual para que siempre exista la clave,
        // aunque no se suba una nueva imagen distinta.
        $validated['img'] = $oldProductImg;

        //PROCESAMIENTO DE IMAGEN PRINCIPAL
        if (isset($productMainImg) && ($productMainImg->getClientOriginalName() !== $oldProductImg || $this->urlChanges($validated, $product->toArray()))) {
            $mainImgName    = $productMainImg->getClientOriginalName();
            $mainImgError   = $productMainImg->getError();
            $mainImgTmpName = $productMainImg->getClientOriginalPath();

            if ($mainImgError !== UPLOAD_ERR_OK) {
                $errors[] = "La imagen principal" . $mainImgName . "del producto contiene errores";
            } else {
                $cleanedMainImgName = str_replace(' ', '-', $mainImgName);

                $fullUrl = public_path('img/img' . $productBrand . '/' . $productCategory . '/' . $cleanedMainImgName);

                if (move_uploaded_file($mainImgTmpName, $fullUrl)) {
                    $savedMainImage = $cleanedMainImgName;
                } else {
                    $errors[] = "La imagen " . $mainImgName . " no se pudo subir";
                }
            }

            if (strlen($savedMainImage) !== 0) $validated['img'] = $savedMainImage;
            else $validated['img'] = "";
        }

        //GENERACION DE SKU DE PRODUCTO
        $prefijoCategoria = strtoupper(substr($validated["categoria"], 0, 3));
        $lastSKU = Product::where('sku', 'LIKE', '%' . $prefijoCategoria . '%')->latest()->value('sku');
        $lastProductModel = null;
        if (isset($lastSKU)) $lastProductModel = explode('-', $lastSKU)[1];
        else $lastProductModel = 0;

        $numeroModelo = str_pad(($lastProductModel === 0 ? $lastProductModel : $lastProductModel + 1), 4, '0', STR_PAD_LEFT);
        $sku = "{$prefijoCategoria}-{$numeroModelo}";
        $validated['sku'] = $sku;

        $productUpdated = $product->update($validated);


        //EN CASO DE PROBLEMAS AL HACER UPDATE O SI HAY UN UPDATE SATISFACTORIO
        //  - SI EL PRODUCTO FALLA NO PASAMOS DE LA EJECUCION DE ESTE IF
        if (!$productUpdated) {
            

            //- RESTAURACION DE IMAGEN PRINCIPAL (Eliminacion de imagen nueva subida)

            if (strlen($savedMainImage) !== 0 && $savedMainImage !== $oldProductImg) {
                $newImg = $savedMainImage;
                
                $fullUrl = public_path("img/img{$productBrand}/{$productBrand}/{$newImg}");

                if (File::exists($fullUrl)) File::delete($fullUrl); //o file_exists($path)) unlink($path);
            }
        
            return $this->sendError('No se ha podido actualizar el producto');
        } else {

            //- ELIMINACION DE IMAGEN ANTIGUA (Eliminacion de imagen antigua)

            if ($oldProductImg !== $validated['img']) {

                $fullUrl = public_path("img/img{$oldProductBrand}/{$oldProductCategory}/{$oldProductImg}");

                if (File::exists($fullUrl)) File::delete($fullUrl);
            }


        }

        //PROCESAMIENTO DE GALERIA
        
        $newProductImgGallery = $_FILES['imagenes_secundarias'];
        $newProductImgGalleryLength = count($newProductImgGallery['name']);

        if (isset($newProductImgGallery)) {
            for ($i = 0; $i < $newProductImgGalleryLength; $i++) {
                $imgName    = $newProductImgGallery['name'][$i];
                $imgTmpName = $newProductImgGallery['tmp_name'][$i];
                $imgError   = $newProductImgGallery['error'][$i];

                $cleanedImgName = str_replace(' ', '-', $imgName);

                if ($imgError !== UPLOAD_ERR_OK) {
                    $errors[] = "La imagen secundaria {$imgName} de la galeria presenta un error";
                    continue;
                }

                $fullUrl = public_path("img/img{$productBrand}/{$productCategory}/{$cleanedImgName}");

                if (File::exists($fullUrl)) continue;

                else if (move_uploaded_file($imgTmpName, $fullUrl)) $savedImages[] = $cleanedImgName;
                else $errors[] = "La imagen {$imgName} de la galeria no se ha podido subir";
            }
        }

        //- CREAMOS LA GALERIA DE IMAGENES EN LA BBDD
        if (count($savedImages) !== 0) {

            $error = false;
            ImageGallery::where('product_id', $product->id)->each(fn($value, $key) => $value->delete());

            array_walk($savedImages, function ($value, $key) use ($product) {

                $galleryItem = [
                    'nombre' => $value,
                    'product_id' => $product->id,
                    'orden' => 0
                ];

                $imageGalleryItem = ImageGallery::create($galleryItem);

                if (!$imageGalleryItem) $error = true;
                return;
            });


            
            if ($error) {
                // SI A NIVEL DE BASE DE DATOS FALLA TENIENDO LAS NUEVAS IMAGENES DE LA GALERIA SUBIDAS
                // - RESTAURAMOS A NIVEL DE BASE DE DATOS LOS ANTIGUOS NOMBRES DE ARCHIVOS CON LA GALERIA ANTIGUA
                // - ELIMINAMOS LAS IMAGENES NUEVAS SUBIDAS
                // - TENIENDO LA GALERIA ANTIGUA Y SI SE HA CAMBIADO LA UBICACION DE SUBIDAS TENEMOS QUE MOVER LAS IMAGENES ANTIGUAS A LA NUEVA UBICACION
                $errors[] = "Ha fallado la actualizacion de la galeria de producto a nivel de base de datos, intentando rescatar las antiguas imagenes";

                // - RESTAURAMOS A NIVEL DE BASE DE DATOS LOS ANTIGUOS NOMBRES DE ARCHIVOS CON LA GALERIA ANTIGUA
                foreach ($oldProductImgGallery as $value) ImageGallery::create($value->toArray());

                // - ELIMINAMOS LAS IMAGENES NUEVAS SUBIDAS
                array_walk($savedImages, function ($value, $key) use ($productBrand, $productCategory) {
                    $fullUrl = public_path("img/img{$productBrand}/{$productCategory}/{$value}");
                    if (File::exists($fullUrl)) File::delete($fullUrl);
                });


                if ($this->urlChanges(['categoria' => $oldProductCategory, 'marca' => $oldProductBrand], $product->toArray())) {
                    foreach ($oldProductImgGallery as $value) {

                        $fullOldUrl = public_path("img/img{$oldProductBrand}/{$oldProductCategory}/{$value->nombre}");

                        $fullUrl = public_path("img/img{$productBrand}/{$productCategory}/{$value->nombre}");

                        if (File::exists($fullOldUrl)) {
                            if (!move_uploaded_file($fullOldUrl, $fullUrl)) $errors[] = "La imagen antigua {$value->nombre} de la galeria del producto no se ha podido mover a la ubicacion nueva";
                        };

                    }

                }

            } else {
                // Ahora si tenemos tanto la galeria subida a nivel de BBDD como a nivel fisico en la nueva ubicacion o simplemente con las nuevas imagenes
                // Tenemos que eliminar las imagenes antiguas de la galeria antigua pero aqui hay que tener en cuenta algo
                // Si alguna de las imagenes nuevas subidas resulta ser la misma que la anterior y no cambia la ubicacion de las imagenes
                // Esa imagen no la podemos borrar o simplemente borramos la que habia antes con el mismo nombre antes
                

                foreach ($oldProductImgGallery as $value) {

                    $fullOldUrl = public_path("img/img{$oldProductBrand}/{$oldProductCategory}/{$value->nombre}");

                    if (File::exists($fullOldUrl)) File::delete($fullOldUrl);

                }


            }
        }

        return;


        //PROCESAMIENTO DE VARIACIONES

        if ($request->input('variaciones')) {
            $variaciones = $request->input('variaciones');

            foreach($variaciones as $index => &$variationData) {

                // PROCESAR IMAGEN PRINCIPAL
                $variationData['product_id'] = $product->id;
                $uploadDir = "img/img{$productBrand}/{$productCategory}";

                if ($request->hasFile("variaciones.{$index}.imagen-main-variacion")) {
                    $fichero = $request->file("variaciones.{$index}.imagen-main-variacion");
                    $nombreFichero = $fichero->getClientOriginalName();
                    $fichero->storeAs($uploadDir, $nombreFichero, 'raiz_publica');

                    $variationData['img'] = $nombreFichero;
                }

                //GENERACION DE SKU
                $variation = Variation::where('id', $variationData['id'])->first();
                $oldVariationImg = $variation?->img; 
                $updated = false;

                if ($variation) {
                    $colorAbv = strtoupper(substr(Color::where('id', $variationData['color_id'])->value('nombre'), 0, 3));
                    $variationSKU = implode('-', [ $validated['sku'], $colorAbv, Talla::where('id', $variationData['size_id'])->value('nombre')]);
                    $variationData['sku'] = $variationSKU;

                    $updated = $variation->update($variationData);

                    if (!$updated) {
                    // RESTAURACION DE IMAGEN PRINCIPAL DE VARIACION
                        if ($oldVariationImg !== $variationData['img'] || $this->urlChanges($product->toArray(), $validated)) {
                            $newVariationImg = $variationData['img'];
                            $fullUrl = public_path("img/img{$productBrand}/{$productCategory}/{$newVariationImg}");

                            if (File::exists($fullUrl)) File::delete($fullUrl);
                        }

                        continue;
                    } else {

                        if ($oldVariationImg !== $variationData['img'] || $this->urlChanges(
                            [
                                'categoria' => $oldProductCategory,
                                'marca' => $oldProductBrand
                            ], $validated
                        ))  {

                        }

                        $fullUrl = public_path("img/img{$productBrand}/{$productCategory}/{$oldVariationImg}");

                        if (File::exists($fullUrl)) File::delete($fullUrl);
                    }
                }

                

                if ($request->hasFile("variaciones.{$index}.imagenes-secundarias-variacion")) {
                    foreach($request->file("variaciones.{$index}.imagenes-secundarias-variacion") as $imagen) {
                        $nombreFichero = $imagen->getClientOriginalName();
                        $saved = $imagen->storeAs($uploadDir, $nombreFichero, 'raiz_publica');

                        if ($saved) {
                            $galleryItem = [
                                'variation_id' => $variation->id,
                                'nombre' => $nombreFichero,
                                'orden' => 0
                            ];

                            $createdItemGallery = ImageGalleryVariation::create($galleryItem);
                            // {{AQUI HAY QUE HACER RESTUAURACION DE GALERIA AL IGUAL QUE EN LA GALERIA DE PRODUCTO}} 

                            if ($createdItemGallery) {
                            } else {

                            }
                        }


                    }
                }


            }
        }
        

        return $this->sendMessage("Se ha actualizado correctamente el producto", 200);

    }

    public function createProduct(AddProductRequest $request) {

        //PROCESADO DE PRODUCTO GENERAL


        //Cambio de nombres de propiedades
        $validated = $request->validated();

        /*$validated['img'] = $validated['imagen-main'];
        $validated['img-galery'] = $validated['imagenes-secundarias']; */
        unset($validated['imagen-main']);
        unset($validated['imagenes-secundarias']);

        //Variables necesarias para el procesamiento de imagenes
        $mainImage = $_FILES['imagen-main'];
        $secondaryImages = $_FILES['imagenes-secundarias'];
        
        $secondaryImagesLenth = count($secondaryImages['name']);
        $errors = [];
        $savedImages = [];
        $savedMainImage = "";

        //Procesamiento de imagen principal del producto
        {
            $mainName = $mainImage['name'];
            $mainTmpName = $mainImage['tmp_name'];
            $mainError = $mainImage['error'];

            if ($mainError !== UPLOAD_ERR_OK) {
                $errors[] = "La imagen principal" . $mainName . " presenta un error";
            } else {
                $productCategory = strtolower($validated['categoria']);
                $productBrand = strtoupper($validated['marca'][0]) . substr($validated['marca'], 1);
                $mainName = str_replace(' ', '-', $mainName);
                    
                $uploadDir = public_path('img/img' . $productBrand . '/' . $productCategory . '/' . $mainName);
                if (move_uploaded_file($mainTmpName, $uploadDir)) {

                    $savedMainImage = $mainName;

                } else {
                    $errors[] = "No se pudo mover la imagen principal " . $mainName;
                }
            }

            
        }
        
        //Recorrer todas las imagenes secundarias subidas utilizando la estructura de $_FILES[]
        for ($i = 0; $i < $secondaryImagesLenth; $i++) {
            
            $name = $secondaryImages['name'][$i];
            $error = $secondaryImages['error'][$i];
            $tmpName = $secondaryImages['tmp_name'][$i];

            if ($error !== UPLOAD_ERR_OK) {
                $errors[] = "La imagen " . $name . " presenta un error"; 
                continue;
            }

            //Procesamiento de ubicacion de imagen segun marca, categoria
            $productCategory = strtolower($validated['categoria']);
            $productBrand = strtoupper($validated['marca'][0]) . substr($validated['marca'], 1);
            $name = str_replace(' ', '-', $name);
            
            $uploadDir = public_path('img/img' . $productBrand . '/' . $productCategory . '/' . $name);
            if (move_uploaded_file($tmpName, $uploadDir)) {

                $savedImages[] = $name;

            } else {
                $errors[] = "No se pudo mover el archivo " . $name;
            }

        }

        //Almacenamiento de nombre de imagen main subida
        if (strlen($savedMainImage) !== 0) $validated['img'] = $savedMainImage;
        else $validated['img'] = "";

        //Generacion de SKU simple para producto
        $prefijoCategoria = strtoupper(substr($validated['categoria'], 0, 3));
        $ultimoSku = Product::where('sku', 'LIKE', '%' . $prefijoCategoria . '%')->latest()->value('sku');
        $lastProductModel = null;
        if (isset($ultimoSku)) $lastProductModel = (int) explode('-', $ultimoSku)[1];
        else $lastProductModel = 0;
        
        $numeroModelo = str_pad(($lastProductModel === 0 ? $lastProductModel : $lastProductModel + 1), 4, '0', STR_PAD_LEFT);
        
        $sku = "{$prefijoCategoria}-{$numeroModelo}";

        $validated['sku'] = $sku;

        $productCreated = Product::create($validated);
        
        if (count($savedImages) !== 0) {
            array_walk($savedImages, function ($value, $key) use ($productCreated) {
                $galleryItem = [
                    'product_id' => $productCreated->id,
                    'nombre' => $value,
                    'orden' => 0
                ];

                ImageGallery::create($galleryItem);
            });
        }


        //PROCESAMIENTO DE VARIACIONES

        // 2. Recorremos las variaciones usando el índice ($index)
        $productCategory = strtolower($validated['categoria']);
        $productBrand = strtoupper($validated['marca'][0]) . substr($validated['marca'], 1);

        foreach ($request->input('variaciones') as $index => &$variationData) {

            $variationData['product_id'] = $productCreated->id;
            $uploadDir = 'img/img' . $productBrand . '/' . $productCategory;
        
            // Comprobamos si se ha subido una imagen para ESTE índice específico
            if ($request->hasFile("variaciones.$index.imagen-main-variacion")) {
                    
                // Guardamos la imagen en la carpeta 'variations' dentro de public
                $fichero = $request->file("variaciones.$index.imagen-main-variacion");
                $nombreFichero = $fichero->getClientOriginalName();
                $fichero->storeAs($uploadDir, $nombreFichero, 'raiz_publica');
                
                // Añadimos la ruta de la imagen a los datos de esta variación
                $variationData['img'] = $nombreFichero;
            }

            //Determinamos el sku segun las abreviaciones de atributos de variaciones
            $colorAbv = strtoupper(substr(Color::where('id', $variationData['color_id'])->value('nombre'), 0, 3));
            $variationSKU = implode('-', [ $validated['sku'], $colorAbv, Talla::where('id', $variationData['size_id'])->value('nombre')]);
            $variationData['sku'] = $variationSKU;
            
            $variationData['color_id'] = $variationData['color'];
            $variationData['size_id'] = $variationData['talla'];

            $createdVariation = Variation::create($variationData);

            if ($request->hasFile("variaciones.$index.imagenes-secundarias-variacion")) {
                foreach ($request->file("variaciones.$index.imagenes-secundarias-variacion") as $imagen) {
                    $nombreFichero = $imagen->getClientOriginalName();
                    $path = $imagen->storeAs($uploadDir, $nombreFichero, 'raiz_publica');

                    $galleryVariationItem = [
                        'variation_id' => $createdVariation->id,
                        'nombre' => $nombreFichero,
                        'orden' => 0
                    ];

                    if ($path) ImageGalleryVariation::create($galleryVariationItem);
                }
            }

        }

        
        if (count($savedImages) !== $secondaryImagesLenth || strlen($savedMainImage) === 0) {
            return $this->sendError("Algunas imagenes han presentado errores, demas subidas", $errors, 400);
        } else {
            return $this->sendMessage("Se han subido todas las imagenes", 200);
        }
        
    }

    public function deleteProduct(Product $product) {
        $product->delete();
    }

    public function getSizes(Request $request) {

        $categorySize = $request->input("category_size");
        $gender = $request->input("gender");
        $categoryProduct = $request->input("category_product");

        $categoriaEnum = TallaCategoria::tryFrom($categorySize);

        $correctValidationGenders = ['adulto' => ['mujer', 'hombre'], 'infantil' => ['niño', 'niña']];

        $correctValidationProducts = ['prendas' => ['pantalones', 'camisetas'], 'zapatillas' => ['zapatillas']];

        
        if ($categorySize && $gender && $categoriaEnum) {

            $categorySizeProduct = strtolower(substr($categorySize, 0, stripos($categorySize, ' ')));
            $lowerProductCategory = strtolower($categoryProduct);

            //Comparamos si la categoria del producto es igual a la categoria de tipo de talla para la variacion
            if (!in_array($lowerProductCategory, $correctValidationProducts[$categorySizeProduct])) {
                return $this->sendError('La categoria de producto no coincide con la categoria de talla', [], 400);
            }
            
            // Determinamos si es adulto o infantil buscando la palabra en la categoría de talla
            $lowerGender = strtolower($categorySize);
            $typeGender = str_contains($lowerGender, 'adulto') ? 'adulto' : 'infantil';

            // Validamos si el género es compatible con el tipo de talla (Adulto/Infantil)
            if (!in_array(strtolower($gender), $correctValidationGenders[$typeGender])) {
                return $this->sendError('La categoria de talla no coincide con el sexo del producto', [], 400);
            }
        
            $sizes = Talla::query()->where([
                ['categoria', '=', $categoriaEnum->value], 
                ['genero', '=', $gender]]
            )->get();

            $sizes = SizeResource::collection($sizes);

            return $this->sendResponse($sizes, "Se han obtenido las tallas disponibles", 200);
        } else {
            return $this->sendError('La categoria buscada no existe', [], 404);
        }

    }

    public function urlChanges(array $oldProductData, array $newProductData) {

        if ($oldProductData['categoria'] !== $newProductData['categoria']) return true;
        else if ($oldProductData['marca'] !== $newProductData['marca']) return true;
        else return false;
    }




}