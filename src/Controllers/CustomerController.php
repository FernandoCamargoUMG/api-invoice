<?php

namespace InvoiceSystem\Controllers;

use InvoiceSystem\Models\CustomerModel;

class CustomerController {
    public function index() {
        return CustomerModel::getAll();
    }

    public function show($id) {
        return CustomerModel::getById($id);
    }

    public function store($data) {
        return CustomerModel::create($data);
    }

    public function update($id, $data) {
        return CustomerModel::update($id, $data);
    }

    public function destroy($id) {
        return CustomerModel::delete($id);
    }
}