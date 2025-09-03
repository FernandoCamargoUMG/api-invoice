<?php
namespace InvoiceSystem\Controllers;

use InvoiceSystem\Models\UserModel;

class UserController {
    public function index() {
        return UserModel::getAll();
    }

    public function show($id) {
        return UserModel::getById($id);
    }

    public function store($data) {
        return UserModel::create($data);
    }

    public function update($id, $data) {
        return UserModel::update($id, $data);
    }

    public function destroy($id) {
        return UserModel::delete($id);
    }
}
