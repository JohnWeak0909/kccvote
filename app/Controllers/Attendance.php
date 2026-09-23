<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\StudentModel;
use App\Models\VoteModel;
use App\Models\ElectionModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Attendance extends BaseController
{
    protected $attendanceModel;
    protected $studentModel;
    protected $voteModel;
    protected $electionModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        if (session()->get('role') !== 'admin') {
            header('Location: ' . base_url('/login?action=admin'));
            exit;
        }

        $this->attendanceModel = new AttendanceModel();
        $this->studentModel = new StudentModel();
        $this->voteModel = new VoteModel();
        $this->electionModel = new ElectionModel();
    }

    public function index()
    {
        $date = $this->request->getGet('date') ?? date('Y-m-d');
        $studentId = $this->request->getGet('student_id');
        $department = $this->request->getGet('department');
        $course = $this->request->getGet('course');
        $year = $this->request->getGet('year_level');
        $section = $this->request->getGet('section');
        $academicYear = $this->request->getGet('school_year');
        $electionId = $this->request->getGet('election_id');
        $originalElectionId = $electionId; // Preserve original for view
        $showAllElections = empty($electionId);
        
        // If no election selected, default to first election for queries only
        if (!$electionId) {
            $firstElection = $this->electionModel->first();
            $electionId = $firstElection ? $firstElection['id'] : 1;
        }

        // Get election status
        $election = $this->electionModel->find($electionId);
        $electionClosed = $election && $election['status'] === 'closed';

        // Get all students with filters applied
        $query = $this->studentModel;
        if ($department) {
            $query = $query->where('department', $department);
        }
        if ($course) {
            $query = $query->where('course', $course);
        }
        if ($year) {
            $query = $query->where('year_level', $year);
        }
        if ($section) {
            $query = $query->where('section', $section);
        }
        if ($academicYear) {
            $query = $query->where('school_year', $academicYear);
        }

        $students = $query
            ->orderBy('department', 'ASC')
            ->orderBy('course', 'ASC')
            ->orderBy('school_year', 'ASC')
            ->orderBy('year_level', 'ASC')
            ->orderBy('section', 'ASC')
            ->orderBy('full_name', 'ASC')
            ->findAll();

        $data['students'] = $students;
        
        // If election is closed, show all students and mark as VOTED or ABSENT
        if ($electionClosed) {
            $attendanceRecords = [];
            foreach ($students as $student) {
                $hasVoted = $this->voteModel->hasVoted($student['id'], $electionId);
                
                $record = [
                    'date' => $date,
                    'student_id' => $student['id'],
                    'full_name' => $student['full_name'],
                    'student_number' => $student['student_id'],
                    'department' => $student['department'],
                    'course' => $student['course'],
                    'year_level' => $student['year_level'],
                    'section' => $student['section'],
                    'school_year' => $student['school_year'],
                    'vote_status' => $hasVoted ? 'VOTED' : 'ABSENT',
                    'vote_time' => null,
                    'election_title' => $election['title'],
                    'election_id' => $election['id']
                ];
                
                $attendanceRecords[] = $record;
            }
            $data['attendance'] = $attendanceRecords;
        } else {
            // Election is open, show only voted students
            $votedStudents = $this->voteModel->getVotedStudentsWithDetails($electionId, $date);
            
            // Apply filters to voted students
            $filteredVotedStudents = [];
            foreach ($votedStudents as $student) {
                $include = true;
                
                // Handle NULL/empty values and convert to string for comparison
                $studentDept = (string)($student['department'] ?? '');
                $studentCourse = (string)($student['course'] ?? '');
                $studentYear = (string)($student['year_level'] ?? '');
                $studentSection = (string)($student['section'] ?? '');
                $studentSchoolYear = (string)($student['school_year'] ?? '');
                
                if ($department && $studentDept !== (string)$department) {
                    $include = false;
                }
                if ($course && $studentCourse !== (string)$course) {
                    $include = false;
                }
                if ($year && $studentYear !== (string)$year) {
                    $include = false;
                }
                if ($section && $studentSection !== (string)$section) {
                    $include = false;
                }
                if ($academicYear && $studentSchoolYear !== (string)$academicYear) {
                    $include = false;
                }
                
                if ($include) {
                    $student['vote_status'] = 'VOTED';
                    $filteredVotedStudents[] = $student;
                }
            }
            
            $data['attendance'] = $filteredVotedStudents;
        }
        
        // If showing all elections, group attendance by election
        if ($showAllElections) {
            $allElections = $this->electionModel->findAll();
            $attendanceByElection = [];
            
            foreach ($allElections as $elec) {
                $elecClosed = $elec && $elec['status'] === 'closed';
                $elecRecords = [];
                
                if ($elecClosed) {
                    $students = $this->studentModel
                        ->orderBy('department', 'ASC')
                        ->orderBy('course', 'ASC')
                        ->orderBy('school_year', 'ASC')
                        ->orderBy('year_level', 'ASC')
                        ->orderBy('section', 'ASC')
                        ->orderBy('full_name', 'ASC')
                        ->findAll();
                    
                    foreach ($students as $student) {
                        $hasVoted = $this->voteModel->hasVoted($student['id'], $elec['id']);
                        
                        $record = [
                            'date' => $date,
                            'student_id' => $student['id'],
                            'full_name' => $student['full_name'],
                            'student_number' => $student['student_id'],
                            'department' => $student['department'],
                            'course' => $student['course'],
                            'year_level' => $student['year_level'],
                            'section' => $student['section'],
                            'school_year' => $student['school_year'],
                            'vote_status' => $hasVoted ? 'VOTED' : 'ABSENT',
                            'vote_time' => null,
                            'election_title' => $elec['title'],
                            'election_id' => $elec['id']
                        ];
                        
                        $elecRecords[] = $record;
                    }
                } else {
                    $votedStudents = $this->voteModel->getVotedStudentsWithDetails($elec['id'], $date);
                    foreach ($votedStudents as $student) {
                        $student['vote_status'] = 'VOTED';
                        $elecRecords[] = $student;
                    }
                }
                
                if (!empty($elecRecords)) {
                    $attendanceByElection[$elec['title']] = $elecRecords;
                }
            }
            
            $data['attendance'] = $attendanceByElection;
            $data['group_by_election'] = true;
        } else {
            $data['group_by_election'] = false;
        }
        
        $data['selected_date'] = $date;
        $data['selected_student'] = $studentId;
        $data['election_closed'] = $electionClosed;
        $data['selected_election_id'] = $originalElectionId; // Use original to preserve empty for all elections
        $data['election'] = $election;

        // Get all elections for filter dropdown
        $elections = $this->electionModel->findAll();
        $data['elections'] = $elections;

        // Get distinct values for filter dropdowns - exclude NULL and empty values
        $db = \Config\Database::connect();
        
        $deptQuery = $db->query("SELECT DISTINCT department FROM students WHERE department IS NOT NULL AND department != '' ORDER BY department ASC");
        $data['departments'] = $deptQuery->getResultArray();
        
        $courseQuery = $db->query("SELECT DISTINCT course FROM students WHERE course IS NOT NULL AND course != '' ORDER BY course ASC");
        $data['courses'] = $courseQuery->getResultArray();
        
        $yearQuery = $db->query("SELECT DISTINCT year_level FROM students WHERE year_level IS NOT NULL AND year_level != '' ORDER BY year_level ASC");
        $data['years'] = $yearQuery->getResultArray();
        
        $sectionQuery = $db->query("SELECT DISTINCT section FROM students WHERE section IS NOT NULL AND section != '' ORDER BY section ASC");
        $data['sections'] = $sectionQuery->getResultArray();
        
        $yearSchoolQuery = $db->query("SELECT DISTINCT school_year FROM students WHERE school_year IS NOT NULL AND school_year != '' ORDER BY school_year ASC");
        $data['academicYears'] = $yearSchoolQuery->getResultArray();

        // Store current filters
        $data['filters'] = [
            'election_id' => $electionId,
            'department' => $department,
            'course' => $course,
            'year_level' => $year,
            'section' => $section,
            'school_year' => $academicYear,
        ];

        return view('admin/attendance/index', $data);
    }

    public function mark()
    {
        $data = $this->request->getPost();
        $data['marked_by'] = session()->get('admin_id'); // Assuming admin_id in session

        if ($this->attendanceModel->save($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Attendance marked successfully.']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to mark attendance.']);
        }
    }

    public function printReport()
    {
        $date = $this->request->getGet('date') ?? date('Y-m-d');
        $department = $this->request->getGet('department');
        $course = $this->request->getGet('course');
        $year = $this->request->getGet('year_level');
        $section = $this->request->getGet('section');
        $academicYear = $this->request->getGet('school_year');
        $electionId = $this->request->getGet('election_id');
        $showAllElections = empty($electionId);
        
        // If no election selected, default to first election
        if (!$electionId) {
            $firstElection = $this->electionModel->first();
            $electionId = $firstElection ? $firstElection['id'] : 1;
        }

        $election = $this->electionModel->find($electionId);
        $electionClosed = $election && $election['status'] === 'closed';

        $query = $this->studentModel;
        if ($department) {
            $query = $query->where('department', $department);
        }
        if ($course) {
            $query = $query->where('course', $course);
        }
        if ($year) {
            $query = $query->where('year_level', $year);
        }
        if ($section) {
            $query = $query->where('section', $section);
        }
        if ($academicYear) {
            $query = $query->where('school_year', $academicYear);
        }

        $students = $query
            ->orderBy('department', 'ASC')
            ->orderBy('course', 'ASC')
            ->orderBy('school_year', 'ASC')
            ->orderBy('year_level', 'ASC')
            ->orderBy('section', 'ASC')
            ->orderBy('full_name', 'ASC')
            ->findAll();

        if ($electionClosed) {
            $attendanceRecords = [];
            foreach ($students as $student) {
                $hasVoted = $this->voteModel->hasVoted($student['id'], $electionId);
                $record = [
                    'date' => $date,
                    'student_id' => $student['id'],
                    'full_name' => $student['full_name'],
                    'student_number' => $student['student_id'],
                    'department' => $student['department'],
                    'course' => $student['course'],
                    'year_level' => $student['year_level'],
                    'section' => $student['section'],
                    'school_year' => $student['school_year'],
                    'vote_status' => $hasVoted ? 'VOTED' : 'ABSENT',
                    'vote_time' => null,
                    'election_title' => $election['title'],
                    'election_id' => $election['id']
                ];
                $attendanceRecords[] = $record;
            }
            $data['attendance'] = $attendanceRecords;
        } else {
            $votedStudents = $this->voteModel->getVotedStudentsWithDetails($electionId, $date);
            $filteredVotedStudents = [];
            foreach ($votedStudents as $student) {
                $include = true;
                $studentDept = (string)($student['department'] ?? '');
                $studentCourse = (string)($student['course'] ?? '');
                $studentYear = (string)($student['year_level'] ?? '');
                $studentSection = (string)($student['section'] ?? '');
                $studentSchoolYear = (string)($student['school_year'] ?? '');
                if ($department && $studentDept !== (string)$department) {
                    $include = false;
                }
                if ($course && $studentCourse !== (string)$course) {
                    $include = false;
                }
                if ($year && $studentYear !== (string)$year) {
                    $include = false;
                }
                if ($section && $studentSection !== (string)$section) {
                    $include = false;
                }
                if ($academicYear && $studentSchoolYear !== (string)$academicYear) {
                    $include = false;
                }
                if ($include) {
                    $student['vote_status'] = 'VOTED';
                    $filteredVotedStudents[] = $student;
                }
            }
            $data['attendance'] = $filteredVotedStudents;
        }

        // If showing all elections, group attendance by election
        if ($showAllElections) {
            $allElections = $this->electionModel->findAll();
            $attendanceByElection = [];
            
            foreach ($allElections as $elec) {
                $elecClosed = $elec && $elec['status'] === 'closed';
                $elecRecords = [];
                
                // Build query with filters for each election
                $query = $this->studentModel;
                if ($department) {
                    $query = $query->where('department', $department);
                }
                if ($course) {
                    $query = $query->where('course', $course);
                }
                if ($year) {
                    $query = $query->where('year_level', $year);
                }
                if ($section) {
                    $query = $query->where('section', $section);
                }
                if ($academicYear) {
                    $query = $query->where('school_year', $academicYear);
                }
                
                $elecStudents = $query
                    ->orderBy('department', 'ASC')
                    ->orderBy('course', 'ASC')
                    ->orderBy('school_year', 'ASC')
                    ->orderBy('year_level', 'ASC')
                    ->orderBy('section', 'ASC')
                    ->orderBy('full_name', 'ASC')
                    ->findAll();
                
                if ($elecClosed) {
                    foreach ($elecStudents as $student) {
                        $hasVoted = $this->voteModel->hasVoted($student['id'], $elec['id']);
                        
                        $record = [
                            'date' => $date,
                            'student_id' => $student['id'],
                            'full_name' => $student['full_name'],
                            'student_number' => $student['student_id'],
                            'department' => $student['department'],
                            'course' => $student['course'],
                            'year_level' => $student['year_level'],
                            'section' => $student['section'],
                            'school_year' => $student['school_year'],
                            'vote_status' => $hasVoted ? 'VOTED' : 'ABSENT',
                            'vote_time' => null,
                            'election_title' => $elec['title'],
                            'election_id' => $elec['id']
                        ];
                        
                        $elecRecords[] = $record;
                    }
                } else {
                    $votedStudents = $this->voteModel->getVotedStudentsWithDetails($elec['id'], $date);
                    
                    // Apply filters to voted students
                    foreach ($votedStudents as $student) {
                        $include = true;
                        $studentDept = (string)($student['department'] ?? '');
                        $studentCourse = (string)($student['course'] ?? '');
                        $studentYear = (string)($student['year_level'] ?? '');
                        $studentSection = (string)($student['section'] ?? '');
                        $studentSchoolYear = (string)($student['school_year'] ?? '');
                        
                        if ($department && $studentDept !== (string)$department) {
                            $include = false;
                        }
                        if ($course && $studentCourse !== (string)$course) {
                            $include = false;
                        }
                        if ($year && $studentYear !== (string)$year) {
                            $include = false;
                        }
                        if ($section && $studentSection !== (string)$section) {
                            $include = false;
                        }
                        if ($academicYear && $studentSchoolYear !== (string)$academicYear) {
                            $include = false;
                        }
                        
                        if ($include) {
                            $student['vote_status'] = 'VOTED';
                            $elecRecords[] = $student;
                        }
                    }
                }
                
                if (!empty($elecRecords)) {
                    $attendanceByElection[$elec['title']] = $elecRecords;
                }
            }
            
            $data['attendance'] = $attendanceByElection;
            $data['group_by_election'] = true;
        } else {
            $data['group_by_election'] = false;
        }

        $data['selected_date'] = $date;
        $data['selected_student'] = null;
        $data['election_closed'] = $electionClosed;
        $data['election'] = $election;

        return view('admin/attendance/print', $data);
    }
}