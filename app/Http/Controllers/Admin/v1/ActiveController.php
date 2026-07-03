<?php

namespace App\Http\Controllers\Admin\v1;


use App\Http\Controllers\AdminController;

class ActiveController extends AdminController
{
    public function index()
    {
        return 'index';
    }

    public function detail($id)
    {
        return 'detail '.$id;
    }
}
