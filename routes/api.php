<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SubServiceController;
use App\Http\Controllers\SubServiceFieldController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\ForumReplyController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\FormFieldController;
use App\Http\Controllers\RequirementEnquiryFormController;
use App\Http\Controllers\ContactEnquiryFormController;
use App\Http\Controllers\SubServiceFormFieldController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return ["status"=>"hello"];
});

Route::get('/test', function () {
    return response()->json(["server"=>"running"], 200);
});

Route::post('register', [UserController::class, 'create']);
Route::post('verify/{email}', [UserController::class, 'verify']);
Route::post('social/verify/{email}', [UserController::class, 'google_verify']);
Route::post('social/{email}', [UserController::class, 'google_phone']);
Route::post('login', [UserController::class, 'login']);
Route::post('social', [UserController::class, 'google']);
Route::get('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::post('service/create', [ServiceController::class, 'create'])->middleware('auth:sanctum');
Route::get('service/view', [ServiceController::class, 'view']);
Route::post('service/update/{id}', [ServiceController::class, 'update'])->middleware('auth:sanctum');
Route::get('service/view-by-id/{id}', [ServiceController::class, 'viewById'])->middleware('auth:sanctum');
Route::post('service/update-logo/{id}', [ServiceController::class, 'update_logo'])->middleware('auth:sanctum');
Route::delete('service/delete/{id}', [ServiceController::class, 'delete'])->middleware('auth:sanctum');
Route::post('sub-service/create/{service_id}', [SubServiceController::class, 'create'])->middleware('auth:sanctum');
Route::get('sub-service/view', [SubServiceController::class, 'view']);
Route::get('sub-service/view-by-service-id/{service_id}', [SubServiceController::class, 'viewById']);
Route::get('sub-service/view-by-id/{sub_service_id}', [SubServiceController::class, 'viewBySubServiceId']);
Route::post('sub-service/update/{id}', [SubServiceController::class, 'update'])->middleware('auth:sanctum');
Route::delete('sub-service/delete/{id}', [SubServiceController::class, 'delete'])->middleware('auth:sanctum');
Route::post('sub-service-fields/create/{sub_service_id}', [SubServiceFieldController::class, 'create'])->middleware('auth:sanctum');
Route::get('sub-service-fields/view/{sub_service_id}', [SubServiceFieldController::class, 'view'])->middleware('auth:sanctum');
Route::get('sub-service-fields/view-for-admin', [SubServiceFieldController::class, 'view_admin'])->middleware('auth:sanctum');
Route::get('sub-service-fields/view-for-admin-id/{sub_service_id}', [SubServiceFieldController::class, 'view_admin_sub_service_id'])->middleware('auth:sanctum');
Route::post('sub-service-fields/set-status/{sub_service_field_id}', [SubServiceFieldController::class, 'set_status'])->middleware('auth:sanctum');
Route::post('sub-service-fields/enter-data/{sub_service_id}', [SubServiceFieldController::class, 'create_custom_sub_service_field_data'])->middleware('auth:sanctum');

// forum api
Route::post('forum/create/{service_id}', [ForumController::class, 'create'])->middleware('auth:sanctum');
Route::get('forum/view', [ForumController::class, 'view'])->middleware('auth:sanctum');
Route::get('forum/view-all/{service_id}', [ForumController::class, 'viewAll'])->middleware('auth:sanctum');
Route::post('forum/update/{id}', [ForumController::class, 'update'])->middleware('auth:sanctum');
Route::delete('forum/delete/{id}', [ForumController::class, 'delete'])->middleware('auth:sanctum');

// forum reply api
Route::post('forum-reply/create/{forum_id}', [ForumReplyController::class, 'create'])->middleware('auth:sanctum');
Route::post('forum-reply/update/{id}', [ForumReplyController::class, 'update'])->middleware('auth:sanctum');
Route::delete('forum-reply/delete/{id}', [ForumReplyController::class, 'delete'])->middleware('auth:sanctum');


// city api
Route::post('city/create', [CityController::class, 'create'])->middleware('auth:sanctum');
Route::get('city/view', [CityController::class, 'view']);
Route::post('city/update/{id}', [CityController::class, 'update'])->middleware('auth:sanctum');
Route::get('city/view-by-id/{id}', [CityController::class, 'viewById'])->middleware('auth:sanctum');
Route::delete('city/delete/{id}', [CityController::class, 'delete'])->middleware('auth:sanctum');

// form-fields api
Route::post('form-field/create', [FormFieldController::class, 'create'])->middleware('auth:sanctum');
Route::get('form-field/view-all', [FormFieldController::class, 'viewAll'])->middleware('auth:sanctum');
Route::get('form-field/view/{id}', [FormFieldController::class, 'view'])->middleware('auth:sanctum');
Route::post('form-field/set-status/{id}', [FormFieldController::class, 'set_status'])->middleware('auth:sanctum');
Route::delete('form-field/delete/{id}', [FormFieldController::class, 'delete'])->middleware('auth:sanctum');

// testimonial api
Route::post('testimonial/create', [TestimonialController::class, 'create'])->middleware('auth:sanctum');
Route::get('testimonial/view', [TestimonialController::class, 'view']);
Route::post('testimonial/update/{id}', [TestimonialController::class, 'update'])->middleware('auth:sanctum');
Route::get('testimonial/view-by-id/{id}', [TestimonialController::class, 'viewById'])->middleware('auth:sanctum');
Route::post('testimonial/update-logo/{id}', [TestimonialController::class, 'update_logo'])->middleware('auth:sanctum');
Route::delete('testimonial/delete/{id}', [TestimonialController::class, 'delete'])->middleware('auth:sanctum');

// requirement enquiry api
Route::post('requirement/create', [RequirementEnquiryFormController::class, 'create']);
Route::get('requirement/view', [RequirementEnquiryFormController::class, 'viewAll'])->middleware('auth:sanctum');
Route::delete('requirement/delete/{id}', [RequirementEnquiryFormController::class, 'delete'])->middleware('auth:sanctum');

// contact enquiry api
Route::post('contact/create', [ContactEnquiryFormController::class, 'create']);
Route::get('contact/view', [ContactEnquiryFormController::class, 'viewAll'])->middleware('auth:sanctum');
Route::delete('contact/delete/{id}', [ContactEnquiryFormController::class, 'delete'])->middleware('auth:sanctum');

// sub service form fields
Route::post('sub-service-form-fields/create/{sub_service_id}', [SubServiceFormFieldController::class, 'create'])->middleware('auth:sanctum');
Route::post('sub-service-form-fields/create-form-entry/{sub_service_id}', [SubServiceFormFieldController::class, 'create_custom_sub_service_form_field_data_entry'])->middleware('auth:sanctum');
Route::get('sub-service-form-fields/view-all-form-entry/{sub_service_id}', [SubServiceFormFieldController::class, 'view_all_custom_sub_service_form_field_data_entry'])->middleware('auth:sanctum');
Route::get('sub-service-form-fields/view-form-entry/{id}/{sub_service_id}', [SubServiceFormFieldController::class, 'view_by_id_custom_sub_service_form_field_data_entry'])->middleware('auth:sanctum');
Route::post('sub-service-form-fields/edit-form-entry/{id}/{sub_service_id}', [SubServiceFormFieldController::class, 'edit_by_id_custom_sub_service_form_field_data_entry'])->middleware('auth:sanctum');
Route::delete('sub-service-form-fields/delete-form-entry/{id}/{sub_service_id}', [SubServiceFormFieldController::class, 'delete_by_id_custom_sub_service_form_field_data_entry'])->middleware('auth:sanctum');
Route::get('sub-service-form-fields/view-all/{sub_service_id}', [SubServiceFormFieldController::class, 'view_all']);
Route::get('sub-service-form-fields/view-all-order/{sub_service_id}', [SubServiceFormFieldController::class, 'view_all_order']);
Route::get('sub-service-form-fields/view-all-search/{sub_service_id}', [SubServiceFormFieldController::class, 'view_all_search'])->middleware('auth:sanctum');


Route::post('admin/login', [AdminController::class, 'login']);
Route::post('admin/forgot-password', [AdminController::class, 'forgot_password']);
Route::post('admin/reset-password/{email}', [AdminController::class, 'reset_password']);

Route::get('admin/check', [AdminController::class, 'checkAdmin'])->middleware('auth:sanctum');
Route::get('user/check', [UserController::class, 'checkUser'])->middleware('auth:sanctum');