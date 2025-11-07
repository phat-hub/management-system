<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../bootstrap.php';

define('APPNAME', 'htql');

session_start();

$router = new \Bramus\Router\Router();

// Auth routes
$router->post('/logout', '\App\Controllers\Auth\LoginController@destroy');
$router->get('/login', '\App\Controllers\Auth\LoginController@create');
$router->post('/login', '\App\Controllers\Auth\LoginController@store');


$router->get('/', '\App\Controllers\HomeController@index');
$router->get('/home', '\App\Controllers\HomeController@index');

$router->get('/student', '\App\Controllers\UserController@student');
$router->get('/student/register', '\App\Controllers\Auth\RegisterController@createStudent');
$router->post('/student/register', '\App\Controllers\Auth\RegisterController@storeStudent');
$router->get('/student/edit/(\d+)', '\App\Controllers\UserController@editStudent');
$router->post('/student/edit/(\d+)', '\App\Controllers\UserController@updateStudent');
$router->post('/student/lock/(\d+)', '\App\Controllers\UserController@lock');
$router->post('/student/unlock/(\d+)', '\App\Controllers\UserController@unlock');

$router->get('/lecturer', '\App\Controllers\UserController@lecturer');
$router->get('/lecturer/register', '\App\Controllers\Auth\RegisterController@createLecturer');
$router->post('/lecturer/register', '\App\Controllers\Auth\RegisterController@storeLecturer');
$router->get('/lecturer/edit/(\d+)', '\App\Controllers\UserController@editLecturer');
$router->post('/lecturer/edit/(\d+)', '\App\Controllers\UserController@updateLecturer');
$router->post('/lecturer/lock/(\d+)', '\App\Controllers\UserController@lock');
$router->post('/lecturer/unlock/(\d+)', '\App\Controllers\UserController@unlock');

$router->get('/change_password', '\App\Controllers\UserController@changePasswordForm');
$router->post('/change_password', '\App\Controllers\UserController@changePassword');

$router->get('/department', '\App\Controllers\DepartmentController@department');
$router->get('/department/create', '\App\Controllers\DepartmentController@create');
$router->post('/department/create', '\App\Controllers\DepartmentController@store');
$router->get('/department/edit/(\d+)', '\App\Controllers\DepartmentController@edit');
$router->post('/department/edit/(\d+)', '\App\Controllers\DepartmentController@update');
$router->post('/department/delete/(\d+)', '\App\Controllers\DepartmentController@destroy');

$router->get('/major', '\App\Controllers\MajorController@major');
$router->get('/major/create', '\App\Controllers\MajorController@create');
$router->post('/major/create', '\App\Controllers\MajorController@store');
$router->get('/major/edit/(\d+)', '\App\Controllers\MajorController@edit');
$router->post('/major/edit/(\d+)', '\App\Controllers\MajorController@update');
$router->post('/major/delete/(\d+)', '\App\Controllers\MajorController@destroy');

$router->get('/course', '\App\Controllers\CourseController@course');
$router->get('/course/create', '\App\Controllers\CourseController@create');
$router->post('/course/create', '\App\Controllers\CourseController@store');
$router->get('/course/edit/(\d+)', '\App\Controllers\CourseController@edit');
$router->post('/course/edit/(\d+)', '\App\Controllers\CourseController@update');
$router->post('/course/delete/(\d+)', '\App\Controllers\CourseController@destroy');

$router->get('/classroom', '\App\Controllers\ClassroomController@classroom');
$router->get('/classroom/create', '\App\Controllers\ClassroomController@create');
$router->post('/classroom/create', '\App\Controllers\ClassroomController@store');
$router->get('/classroom/edit/(\d+)', '\App\Controllers\ClassroomController@edit');
$router->post('/classroom/edit/(\d+)', '\App\Controllers\ClassroomController@update');
$router->post('/classroom/delete/(\d+)', '\App\Controllers\ClassroomController@destroy');

$router->get('/subject', '\App\Controllers\SubjectController@subject');
$router->get('/subject/create', '\App\Controllers\SubjectController@create');
$router->post('/subject/create', '\App\Controllers\SubjectController@store');
$router->get('/subject/edit/(\d+)', '\App\Controllers\SubjectController@edit');
$router->post('/subject/edit/(\d+)', '\App\Controllers\SubjectController@update');
$router->post('/subject/delete/(\d+)', '\App\Controllers\SubjectController@destroy');

$router->get('/semester', '\App\Controllers\SemesterController@semester');
$router->get('/semester/create', '\App\Controllers\SemesterController@create');
$router->post('/semester/create', '\App\Controllers\SemesterController@store');
$router->get('/semester/edit/(\d+)', '\App\Controllers\SemesterController@edit');
$router->post('/semester/edit/(\d+)', '\App\Controllers\SemesterController@update');
$router->post('/semester/delete/(\d+)', '\App\Controllers\SemesterController@destroy');

$router->get('/teaching_assignment', '\App\Controllers\TeachingAssignmentController@teaching_assignment');
$router->post('/teaching_assignment', '\App\Controllers\TeachingAssignmentController@teaching_assignment');
$router->get('/teaching_assignment/create', '\App\Controllers\TeachingAssignmentController@create');
$router->post('/teaching_assignment/create', '\App\Controllers\TeachingAssignmentController@store');
$router->get('/teaching_assignment/edit/(\d+)', '\App\Controllers\TeachingAssignmentController@edit');
$router->post('/teaching_assignment/edit/(\d+)', '\App\Controllers\TeachingAssignmentController@update');
$router->post('/teaching_assignment/delete/(\d+)', '\App\Controllers\TeachingAssignmentController@destroy');
$router->get('/teaching_assignment/(\d+)/students', '\App\Controllers\TeachingAssignmentController@students');
$router->post('/teaching_assignment/subject_registration/(\d+)', '\App\Controllers\SubjectRegistrationController@store');
$router->post('/teaching_assignment/subject_registration/delete/(\d+)', '\App\Controllers\SubjectRegistrationController@destroy');
$router->get('/teaching_assignment/exam_datetime/(\d+)', '\App\Controllers\SubjectRegistrationController@exam_datetime');
$router->post('/teaching_assignment/exam_datetime/(\d+)', '\App\Controllers\SubjectRegistrationController@update_exam_datetime');
$router->get('/teaching_assignment/score/(\d+)', '\App\Controllers\SubjectRegistrationController@score');
$router->post('/teaching_assignment/score/(\d+)', '\App\Controllers\SubjectRegistrationController@update_score');

$router->get('/subject_registration', '\App\Controllers\SubjectRegistrationController@subject_registration');
$router->get('/subject_registration/score_result', '\App\Controllers\SubjectRegistrationController@score_result');
$router->post('/subject_registration/score_result', '\App\Controllers\SubjectRegistrationController@score_result');
$router->post('/subject_registration', '\App\Controllers\SubjectRegistrationController@subject_registration');
$router->get('/tuition', '\App\Controllers\SubjectRegistrationController@tuition');

$router->post('/registration_status/open', '\App\Controllers\TeachingAssignmentController@open');
$router->post('/registration_status/close', '\App\Controllers\TeachingAssignmentController@close');

$router->set404('\App\Controllers\Controller@sendNotFound');

$router->run();
