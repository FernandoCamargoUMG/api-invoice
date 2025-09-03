<?php

namespace InvoiceSystem\Controllers;

use InvoiceSystem\Models\ProductModel;

class ProductController {
    public function index() {
        return ProductModel::getAll();
    }

    public function show($id) {
        return ProductModel::getById($id);
    }

    public function store($data) {
        return ProductModel::create($data);
    }

    public function update($id, $data) {
        return ProductModel::update($id, $data);
    }

    public function destroy($id) {
        return ProductModel::delete($id);
    }
}