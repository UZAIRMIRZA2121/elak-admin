<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        return "Brand Add New Placeholder";
    }

    public function list()
    {
        return "Brand List Placeholder";
    }

    public function store(Request $request)
    {
        return "Brand Store Placeholder";
    }

    public function edit($id)
    {
        return "Brand Edit Placeholder";
    }

    public function update(Request $request, $id)
    {
        return "Brand Update Placeholder";
    }

    public function delete($id)
    {
        return "Brand Delete Placeholder";
    }

    public function status(Request $request)
    {
        return "Brand Status Placeholder";
    }

    public function getBrandData(Request $request)
    {
        return "Brand Data Placeholder";
    }
}
