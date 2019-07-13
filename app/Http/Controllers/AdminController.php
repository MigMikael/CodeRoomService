<?php

namespace App\Http\Controllers;

use App\Course;
use App\Teacher;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $courses = $courses = Course::withCount([
            'students', 'teachers', 'lessons'
        ])->get();
        foreach ($courses as $c){
            $c->students;
            $c->teachers;
            $c->lessons;
            $c->announcement;
            $c->badges;
        }
        $teachers = Teacher::all();

        $data['courses'] = $courses;
        $data['teacher'] = $teachers;
        return $data;
    }
}
