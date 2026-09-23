<?php

namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use CodeIgniter\HTTP\IncomingRequest;

class Student extends BaseController
{
    protected $studentModel;

    public function __construct()
    {
        $this->studentModel = new StudentModel();
    }

    public function index()
    {
        return view('admin/students/index');
    }

    public function list()
    {
        $q = $this->request->getGet('q');
        $filters = [
            'department' => $this->request->getGet('department'),
            'course' => $this->request->getGet('course'),
            'year_level' => $this->request->getGet('year_level'),
            'section' => $this->request->getGet('section'),
            'is_active' => $this->request->getGet('is_active'),
            'face_registered' => $this->request->getGet('face_registered'),
        ];

        $builder = $this->studentModel;

        if ($q) {
            $builder = $builder->groupStart()
                ->like('student_id', $q)
                ->orLike('full_name', $q)
                ->orLike('department', $q)
                ->orLike('course', $q)
                ->orLike('section', $q)
                ->groupEnd();
        }

        foreach ($filters as $key => $val) {
            if ($val !== null && $val !== '') {
                if ($key === 'is_active') {
                    $builder = $builder->where('is_active', $val);
                } else {
                    $builder = $builder->where($key, $val);
                }
            }
        }

        $perPage = (int) ($this->request->getGet('per_page') ?? 25);
        $page = (int) ($this->request->getGet('page') ?? 1);

        $total = $builder->countAllResults(false);
        $students = $builder->orderBy('created_at', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->findAll();

        // Never expose password hashes in the normal student list response.
        foreach ($students as &$student) {
            unset($student['password'], $student['face_descriptor'], $student['id_photo_hash'], $student['failed_login_attempts'], $student['locked_until']);
        }

        // DEBUG: log total and sample
        log_message('debug', 'Students list total: ' . intval($total));
        log_message('debug', 'Students list sample: ' . json_encode(array_slice($students, 0, 5)));

        // provide filter options (distinct values)
        $departments = $this->studentModel->distinct()->select('department')->where('department IS NOT NULL', null, false)->orderBy('department','ASC')->findColumn('department');
        $courses = $this->studentModel->distinct()->select('course')->where('course IS NOT NULL', null, false)->orderBy('course','ASC')->findColumn('course');
        $years = $this->studentModel->distinct()->select('year_level')->where('year_level IS NOT NULL', null, false)->orderBy('year_level','ASC')->findColumn('year_level');
        $sections = $this->studentModel->distinct()->select('section')->where('section IS NOT NULL', null, false)->orderBy('section','ASC')->findColumn('section');

        $summaryModel = new StudentModel();
        $summaryTotal = $summaryModel->countAllResults();
        $activeCount = $summaryModel->where('is_active', 1)->countAllResults();
        $inactiveCount = $summaryModel->where('is_active', 0)->countAllResults();
        $faceRegisteredCount = $summaryModel->where('face_registered', 1)->countAllResults();

        return $this->response->setJSON([
            'data' => $students,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'summary' => [
                'total' => $summaryTotal,
                'active' => $activeCount,
                'inactive' => $inactiveCount,
                'face_registered' => $faceRegisteredCount,
            ],
            'filters' => [
                'departments' => array_values(array_filter($departments)),
                'courses' => array_values(array_filter($courses)),
                'years' => array_values(array_filter($years)),
                'sections' => array_values(array_filter($sections)),
            ],
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        // sanitize student_id to digits only
        if (!empty($data['student_id'])) {
            $data['student_id'] = preg_replace('/\D+/', '', $data['student_id']);
        }

        // auto-generate username and password, but use the client-generated password when provided
        $data['username'] = $data['student_id'] ?? '';
        $plainPassword = $data['password'] ?? $this->generatePassword();
        $data['password'] = $plainPassword;
        $data['is_active'] = $data['is_active'] ?? 1;

        if ($this->studentModel->insert($data)) {
            $id = $this->studentModel->getInsertID();
            return $this->response->setJSON(['success' => true, 'id' => $id, 'password' => $plainPassword]);
        }

        return $this->response->setJSON(['success' => false, 'errors' => $this->studentModel->errors()]);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $existing = $this->studentModel->find($id);
        if (!$existing) {
            return $this->response->setJSON(['success' => false, 'errors' => ['Student not found.']]);
        }

        if (isset($data['is_active']) && (string) $data['is_active'] !== (string) ($existing['is_active'] ?? '')) {
            $data['password'] = $this->generatePassword();
            $data['must_change_password'] = 1;
        }

        if ($this->studentModel->update($id, $data)) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false, 'errors' => $this->studentModel->errors()]);
    }

    public function delete($id)
    {
        if ($this->studentModel->delete($id)) {
            return $this->response->setJSON(['success' => true]);
        }
        return $this->response->setJSON(['success' => false]);
    }

    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids') ?? [];
        if (!is_array($ids)) $ids = json_decode($ids, true) ?: [];

        if (empty($ids)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No students selected']);
        }

        switch ($action) {
            case 'activate':
                $passwords = [];
                foreach ($ids as $id) {
                    $newPassword = $this->generatePassword();
                    $this->studentModel->update($id, ['is_active' => 1, 'password' => $newPassword, 'must_change_password' => 1]);
                    $passwords[$id] = $newPassword;
                }
                return $this->response->setJSON(['success' => true, 'passwords' => $passwords]);
            case 'deactivate':
                $passwords = [];
                foreach ($ids as $id) {
                    $newPassword = $this->generatePassword();
                    $this->studentModel->update($id, ['is_active' => 0, 'password' => $newPassword, 'must_change_password' => 1]);
                    $passwords[$id] = $newPassword;
                }
                return $this->response->setJSON(['success' => true, 'passwords' => $passwords]);
            case 'delete':
                foreach ($ids as $id) {
                    $this->studentModel->delete($id);
                }
                break;
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function uploadFace($id)
    {
        // Accept base64 images in POST: faceImages[]
        $faceImages = $this->request->getPost('faceImages');
        $saved = [];
        if ($faceImages && is_array($faceImages)) {
            $uploadDir = WRITEPATH . 'uploads/faces/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            foreach ($faceImages as $idx => $dataUrl) {
                if (preg_match('/^data:image\/png;base64,/', $dataUrl)) {
                    $imgData = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $dataUrl));
                    $fname = $uploadDir . $id . '_' . uniqid() . '.png';
                    file_put_contents($fname, $imgData);
                    $saved[] = $fname;
                }
            }
        }

        if (!empty($saved)) {
            $this->studentModel->update($id, [
                'face_registered' => 1,
                'face_descriptor' => json_encode($saved),
                'face_enrolled_at' => date('Y-m-d H:i:s')
            ]);
            return $this->response->setJSON(['success' => true, 'files' => $saved]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'No face images received']);
    }

    public function resetPassword($id)
    {
        $plain = $this->generatePassword();
        if ($this->studentModel->update($id, ['password' => $plain, 'must_change_password' => 1])) {
            return $this->response->setJSON(['success' => true, 'password' => $plain]);
        }
        return $this->response->setJSON(['success' => false]);
    }

    public function import()
    {
        $file = $this->request->getFile('student_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please upload a valid CSV file.']);
        }

        $ext = strtolower(pathinfo($file->getClientName(), PATHINFO_EXTENSION));
        $path = $file->getTempName();
        $rows = [];

        if (in_array($ext, ['csv', 'txt'])) {
            $handle = fopen($path, 'r');
            if (!$handle) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unable to read uploaded file.']);
            }
            $headers = fgetcsv($handle);
            if ($headers === false) {
                fclose($handle);
                return $this->response->setJSON(['success' => false, 'message' => 'Uploaded file is empty or invalid.']);
            }
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
            while (($line = fgetcsv($handle)) !== false) {
                if (count(array_filter($line, fn($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }
                $rows[] = array_combine($headers, $line);
            }
            fclose($handle);
        } elseif (in_array($ext, ['xlsx', 'xls'])) {
            if (!class_exists('PhpOffice\PhpSpreadsheet\IOFactory')) {
                return $this->response->setJSON(['success' => false, 'message' => 'Excel import requires PhpSpreadsheet. Please upload CSV instead or install PhpSpreadsheet.']);
            }
            if (!extension_loaded('zip') || !class_exists('ZipArchive')) {
                return $this->response->setJSON(['success' => false, 'message' => 'Excel import requires the PHP Zip extension (ZipArchive). Please enable ext-zip in php.ini and restart Apache.']);
            }
            if (!extension_loaded('gd')) {
                return $this->response->setJSON(['success' => false, 'message' => 'Excel import requires the PHP GD extension. Please enable ext-gd in php.ini and restart Apache.']);
            }
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
            $spreadsheet = $reader->load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $headerRow = [];
            foreach ($sheet->getRowIterator(1, 1)->current()->getCellIterator() as $cell) {
                $headerRow[] = trim((string) $cell->getValue());
            }
            foreach ($sheet->getRowIterator(2) as $row) {
                $rowValues = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowValues[] = trim((string) $cell->getValue());
                }
                if (count(array_filter($rowValues, fn($value) => $value !== '')) === 0) {
                    continue;
                }
                $rows[] = array_combine($headerRow, $rowValues);
            }
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Unsupported file type. Use CSV.']);
        }

        $fieldMap = [
            'student id' => 'student_id',
            'student_id' => 'student_id',
            'id' => 'student_id',
            'first name' => 'first_name',
            'firstname' => 'first_name',
            'middle name' => 'middle_name',
            'middlename' => 'middle_name',
            'last name' => 'last_name',
            'lastname' => 'last_name',
            'full name' => 'full_name',
            'fullname' => 'full_name',
            'email' => 'email',
            'department' => 'department',
            'course' => 'course',
            'year level' => 'year_level',
            'yearlevel' => 'year_level',
            'year' => 'year_level',
            'section' => 'section',
            'status' => 'status',
        ];

        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            $record = [];
            foreach ($row as $header => $value) {
                $key = strtolower(trim((string) $header));
                $key = preg_replace('/[^a-z0-9 ]/', '', $key);
                $field = $fieldMap[$key] ?? null;
                if ($field) {
                    $record[$field] = trim((string) $value);
                }
            }

            if (empty($record['student_id'])) {
                $errors[] = 'Row ' . ($index + 2) . ': Missing student_id.';
                continue;
            }

            if (empty($record['full_name'])) {
                $record['full_name'] = trim(($record['first_name'] ?? '') . ' ' . ($record['middle_name'] ?? '') . ' ' . ($record['last_name'] ?? ''));
            }

            if (empty($record['full_name'])) {
                $errors[] = 'Row ' . ($index + 2) . ': Missing full name.';
                continue;
            }

            if (!isset($record['status'])) {
                $record['status'] = 'Active';
            }
            $record['is_active'] = stripos((string) $record['status'], 'inactive') !== false ? 0 : 1;
            $record['username'] = $record['student_id'];
            if (empty($record['password'])) {
                $record['password'] = $this->generatePassword();
            }

            $existing = $this->studentModel->where('student_id', $record['student_id'])->first();
            if ($existing) {
                $this->studentModel->update($existing['id'], $record);
                $updated++;
            } else {
                $this->studentModel->insert($record);
                $imported++;
            }
        }

        return $this->response->setJSON(['success' => true, 'imported' => $imported, 'updated' => $updated, 'errors' => $errors]);
    }

    protected function generatePassword($length = 10)
    {
        $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits  = '0123456789';
        $chars   = $letters . $digits;

        // Ensure at least one letter and at least one digit
        $pwd = [
            $letters[random_int(0, strlen($letters) - 1)],
            $digits[random_int(0, strlen($digits) - 1)],
        ];

        $max = strlen($chars) - 1;
        for ($i = 2; $i < $length; $i++) {
            $pwd[] = $chars[random_int(0, $max)];
        }

        shuffle($pwd);
        return implode('', $pwd);
    }
}
