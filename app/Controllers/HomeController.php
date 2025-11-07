<?php

namespace App\Controllers;

use App\Models\Major;
use App\Models\Department;
use App\Models\Course;
use App\Models\SubjectRegistration;

class HomeController extends Controller
{
  public function __construct()
  {
    if (!AUTHGUARD()->isUserLoggedIn()) {
      redirect('/login');
    }

    parent::__construct();
  }

  public function index()
  {
    if (AUTHGUARD()->user()->major_id && AUTHGUARD()->user()->department_id && AUTHGUARD()->user()->course_id){
      $major = (new Major(PDO()))->find(AUTHGUARD()->user()->major_id);
      $department = (new Department(PDO()))->find(AUTHGUARD()->user()->department_id);
      $course = (new Course(PDO()))->find(AUTHGUARD()->user()->course_id);
      $subjectRegModel = new SubjectRegistration(PDO());
      $totalCredits = $subjectRegModel->getTotalCreditsByStudent(AUTHGUARD()->user()->id);
      $this->sendPage('/home/home', [
          'majorName' => $major?->majorName,
          'departmentName' => $department?->departmentName,
          'courseName' => $course?->courseName,
          'totalCredits' => $totalCredits
      ]);
    } else if (AUTHGUARD()->user()->department_id){
      $department = (new Department(PDO()))->find(AUTHGUARD()->user()->department_id);
      $this->sendPage('/home/home', [
          'departmentName' => $department?->departmentName,
      ]);
    }
    $this->sendPage('/home/home');
  }

}
