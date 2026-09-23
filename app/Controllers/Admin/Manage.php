<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Manage extends BaseController
{
    public function index()
    {
        return view('admin/manage/index');
    }

    public function departments()
    {
        // return partial HTML for departments (table + modal + scripts)
        return view('admin/partials/departments_partial');
    }

    public function courses()
    {
        return view('admin/partials/courses_partial');
    }
}
