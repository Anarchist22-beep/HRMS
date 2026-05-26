<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\PermissionController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\DepartmentController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\ShiftController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ProjectController;
use App\Http\Controllers\Api\Sales\LeadController;
use App\Http\Controllers\Api\Sales\DashboardController  as SalesDashboardController;



Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);


Route::middleware('auth:api')->group(function(){

//DashboardController
Route::get('/admin/dashboard',[DashboardController::class,'index']);

Route::post('logout',[AuthController::class,'logout']);
Route::post('/change-name',[AuthController::class,'changeName']);
Route::post('/change-password',[AuthController::class,'changePassword']);

Route::get('/permission-list',[PermissionController::class,'index']);
Route::post('/permission-store',[PermissionController::class,'store']);
Route::get('/permission/{id}/edit',[PermissionController::class,'edit']);
Route::put('/permission/{id}/update',[PermissionController::class,'update']);
Route::delete('/permission/{id}/delete',[PermissionController::class,'delete']);

//roles
Route::get('/role-list',[RoleController::class,'index']);
Route::post('/role-store',[RoleController::class,'store']);
Route::get('/role/{id}/edit',[RoleController::class,'edit']);
Route::put('/role/{id}/update',[RoleController::class,'update']);
Route::delete('/role/{id}/delete',[RoleController::class,'delete']);

//departments
Route::get('/department-list',[DepartmentController::class,'index']);
Route::post('/department-store',[DepartmentController::class,'store']);
Route::get('/department/{id}/edit',[DepartmentController::class,'edit']);
Route::put('/department/{id}/update',[DepartmentController::class,'update']);
Route::delete('/department/{id}/delete',[DepartmentController::class,'delete']);

//users
Route::get('/user-list',[UserController::class,'index']);
Route::get('/user-create',[UserController::class,'create']);
Route::post('/user-store',[UserController::class,'store']);
Route::get('/user/{id}/edit',[UserController::class,'edit']);
Route::put('/user/{id}/update',[UserController::class,'update']);
Route::put('/user/{id}/status',[UserController::class,'updateStatus']);
Route::delete('/user/{id}/delete',[UserController::class,'delete']);


//shifts

Route::get('/shift-list',[ShiftController::class,'index']);
Route::post('/shift-store',[ShiftController::class,'store']);
Route::get('/shift/{id}/edit',[ShiftController::class,'edit']);
Route::put('/shift/{id}/update',[ShiftController::class,'update']);
Route::delete('/shift/{id}/delete',[ShiftController::class,'delete']);
Route::put('/shift/{id}/status',[ShiftController::class,'updateStatus']);
Route::get('/fetch-employees',[ShiftController::class,'list_employee']);
Route::post('/shift-schedule-store',[ShiftController::class,'store_schedule']);
Route::get('/list-shift-schedule',[ShiftController::class,'list_schedule']);
Route::get('/schedule/{id}/edit',[ShiftController::class,'schedule_edit']);
Route::put('/schedule/{id}/update',[ShiftController::class,'update_schedule']);
Route::delete('/schedule/{id}/delete',[ShiftController::class,'delete_schedule']);

//projects
Route::get('/projects-list',[ProjectController::class,'index']);
Route::post('/project-store',[ProjectController::class,'store']);
Route::get('/project/{id}/edit',[ProjectController::class,'edit']);
Route::put('/project/{id}/update',[ProjectController::class,'update']);
Route::delete('/project/{id}/delete',[ProjectController::class,'delete']);
Route::put('/project/{id}/status',[ProjectController::class,'updateStatus']);
Route::put('/project/{id}/payments',[ProjectController::class,'updatePayments']);







//leads
Route::get('/lead-list',[LeadController::class,'index']);
Route::post('/lead-store',[LeadController::class,'store']);
Route::get('/lead/{id}/edit',[LeadController::class,'edit']);
Route::put('/lead/{id}/update',[LeadController::class,'update']);
Route::put('/lead/{id}/status',[LeadController::class,'updateStatus']);
Route::delete('/lead/{id}/delete',[LeadController::class,'delete']);
Route::get('/get-countries',[LeadController::class,'getcountries']);
Route::get('/lead-export', [LeadController::class, 'export']);


//sales dashboard
Route::get('/lead-analytics',[SalesDashboardController::class,'dashboard']);

























Route::post('project_payments', [App\Http\Controllers\Api\Admin\ProjectPaymentController::class, 'store']);
});
