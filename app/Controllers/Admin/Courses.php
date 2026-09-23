<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\DepartmentModel;

class Courses extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new CourseModel();
    }

    public function index()
    {
        $deptModel = new DepartmentModel();
        $data['departments'] = $deptModel->orderBy('department_name')->findAll();
        $data['courses'] = $this->model->orderBy('created_at', 'DESC')->findAll();
        return view('admin/courses/index', $data);
    }

    public function all()
    {
        $builder = $this->model
            ->select('courses.*, departments.department_name, departments.department_code')
            ->join('departments', 'departments.id = courses.department_id', 'left')
            ->orderBy('courses.created_at', 'DESC');

        $courses = $builder->findAll();
        return $this->response->setJSON($courses);
    }

    public function store()
    {
        $data = $this->request->getPost();
        $rules = [
            'course_code' => 'required',
            'course_name' => 'required',
            'department_id' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $this->model->insert([
            'department_id' => $data['department_id'],
            'course_code' => strtoupper(trim($data['course_code'])),
            'course_name' => trim($data['course_name']),
            'status' => $data['status'] ?? 'Active',
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Course added successfully.']);
    }

    public function edit($id)
    {
        $course = $this->model->find($id);
        return $this->response->setJSON($course);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $rules = [
            'course_code' => 'required',
            'course_name' => 'required',
            'department_id' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $this->model->update($id, [
            'department_id' => $data['department_id'],
            'course_code' => strtoupper(trim($data['course_code'])),
            'course_name' => trim($data['course_name']),
            'status' => $data['status'] ?? 'Active',
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Course updated successfully.']);
    }

    public function delete($id)
    {
        $studentModel = new \App\Models\StudentModel();
        $usedByStudents = $studentModel->where('course_id', $id)->countAllResults();

        if ($usedByStudents > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'This course is currently being used by students. Consider deactivating it instead of deleting it.'
            ]);
        }

        $this->model->delete($id);
        return $this->response->setJSON(['success' => true, 'message' => 'Course deleted.']);
    }

    public function toggle($id)
    {
        $course = $this->model->find($id);
        if (! $course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found.']);
        }

        $newStatus = ($course['status'] === 'Active') ? 'Inactive' : 'Active';
        $this->model->update($id, ['status' => $newStatus]);

        return $this->response->setJSON(['success' => true, 'status' => $newStatus]);
    }

    public function byDepartment($id)
    {
        $courses = $this->model
            ->select('courses.*, departments.department_name, departments.department_code')
            ->join('departments', 'departments.id = courses.department_id', 'left')
            ->where('courses.department_id', $id)
            ->where('courses.status', 'Active')
            ->orderBy('courses.course_name')
            ->findAll();

        return $this->response->setJSON($courses);
    }
}
