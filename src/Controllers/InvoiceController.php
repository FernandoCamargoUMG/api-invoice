<?php

namespace InvoiceSystem\Controllers;

use InvoiceSystem\Models\InvoiceModel;

class InvoiceController
{
    public function index($limit = 10, $offset = 0)
    {
        return InvoiceModel::getAll($limit, $offset);
    }

    public function show($id)
    {
        return InvoiceModel::getById($id);
    }

    public function store($data)
    {
        return InvoiceModel::create($data);
    }

    public function update($id, $data)
    {
        return InvoiceModel::update($id, $data);
    }

    public function destroy($id)
    {
        return InvoiceModel::delete($id);
    }
}
