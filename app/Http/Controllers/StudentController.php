<?php

namespace App\Http\Controllers;

use App\Student;
use App\Course;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard()
    {
        $userID = $_SESSION['userID'];

        $student = Student::findOrFail($userID);
        $student['courses'] = $student->courses()->withCount([
            'students', 'teachers', 'lessons',
        ])->get();

        $data = [];
        $data['student'] = $student;

        $courses = Course::withCount([
            'students', 'teachers', 'lessons'
        ])->get();
        $data['courses'] = $courses;

        return $data;
    }
}
