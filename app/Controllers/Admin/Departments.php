<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;
use App\Models\CourseModel;

class Departments extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new DepartmentModel();
    }

    public function index()
    {
        $data['departments'] = $this->model->orderBy('created_at', 'DESC')->findAll();
        return view('admin/departments/index', $data);
    }

    public function list()
    {
        // Return active departments for dropdowns
        $deps = $this->model->where('status', 'Active')->orderBy('department_name')->findAll();
        return $this->response->setJSON($deps);
    }

    public function all()
    {
        $courseModel = new CourseModel();
        $departments = $this->model->orderBy('department_name')->findAll();

        foreach ($departments as &$department) {
            $department['course_count'] = $courseModel
                ->where('department_id', $department['id'])
                ->countAllResults();
        }

        return $this->response->setJSON($departments);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $rules = [
            'department_code' => 'required|is_unique[departments.department_code]',
            'department_name' => 'required',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $this->model->insert([
            'department_code' => strtoupper(trim($data['department_code'])),
            'department_name' => trim($data['department_name']),
            'status' => $data['status'] ?? 'Active',
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Department added successfully.']);
    }

    public function edit($id)
    {
        $dept = $this->model->find($id);
        return $this->response->setJSON($dept);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $rules = [
            'department_code' => 'required',
            'department_name' => 'required',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        // check uniqueness of code if changed
        $existing = $this->model->where('department_code', $data['department_code'])->first();
        if ($existing && $existing['id'] != $id) {
            return $this->response->setJSON(['success' => false, 'errors' => ['department_code' => 'Department code must be unique.']]);
        }

        $this->model->update($id, [
            'department_code' => strtoupper(trim($data['department_code'])),
            'department_name' => trim($data['department_name']),
            'status' => $data['status'] ?? 'Active',
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Department updated successfully.']);
    }

    public function delete($id)
    {
        // prevent delete if courses exist
        $courseModel = new CourseModel();
        $count = $courseModel->where('department_id', $id)->countAllResults();
        if ($count > 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'This department has courses assigned to it. Please remove or deactivate the courses first.']);
        }

        $this->model->delete($id);
        return $this->response->setJSON(['success' => true, 'message' => 'Department deleted.']);
    }

    public function toggle($id)
    {
        $dept = $this->model->find($id);
        if (! $dept) {
            return $this->response->setJSON(['success' => false]);
        }
        $new = $dept['status'] === 'Active' ? 'Inactive' : 'Active';
        $this->model->update($id, ['status' => $new]);
        return $this->response->setJSON(['success' => true, 'status' => $new]);
    }
}
