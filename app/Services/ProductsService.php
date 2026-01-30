<?php

namespace App\Services;

use App\Repositories\ProductsRepository;

class EquipService {
    public function __construct(private ProductsRepository $repo) {}

    public function list() {
        return $this->repo->getAll();
    }

    public function find($id){
        return $this->repo->find($id);
    }

    public function store(array $data) {
        return $this->repo->create($data);
    }

    public function update($id, array $data) {
        return $this->repo->update($id, $data);
    }

    public function delete($id) {
        return $this->repo->delete($id);
    }
}