<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\AdministrativeRegionController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\ExperimentCatalogController;
use App\Http\Controllers\Api\LaboratoryController;
use App\Http\Controllers\Api\LaboratoryTypeController;
use App\Http\Controllers\Api\EquipmentStandardController;
use App\Http\Controllers\Api\ExperimentReservationController;
use App\Http\Controllers\Api\ExperimentRecordController;
use App\Http\Controllers\Api\ExperimentStatisticsController;
use App\Http\Controllers\EquipmentCategoryController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentBorrowController;
use App\Http\Controllers\EquipmentMaintenanceController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\EquipmentQrcodeController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\SmartReservationController;
use App\Http\Controllers\Api\ExperimentWorkController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// 认证相关路由
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    
    Route::middleware('auth:api')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

// 需要认证的API路由
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 用户管理
    Route::middleware(['data.scope'])->group(function () {
        Route::apiResource('users', UserController::class);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword']);
    });
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::put('user/profile', [UserController::class, 'updateProfile']);

    // 组织管理
    Route::middleware(['data.scope'])->group(function () {
        Route::get('organizations/manageable', [\App\Http\Controllers\Api\OrganizationController::class, 'getManageableOrganizations']);
        Route::get('organizations/manageable-schools', [\App\Http\Controllers\Api\OrganizationController::class, 'getManageableSchools']);
        Route::get('organizations/editable', [\App\Http\Controllers\Api\OrganizationController::class, 'getEditableOrganizations']);
        Route::put('organizations/{type}/{id}', [\App\Http\Controllers\Api\OrganizationController::class, 'updateOrganization']);
        Route::get('organizations/schools', [\App\Http\Controllers\Api\OrganizationController::class, 'getOrganizationSchools']);
        Route::get('organizations/tree', [\App\Http\Controllers\Api\OrganizationController::class, 'getOrganizationTree']);
        Route::get('organizations/users', [\App\Http\Controllers\Api\OrganizationController::class, 'getOrganizationUsers']);
        Route::get('organizations/stats', [\App\Http\Controllers\Api\OrganizationController::class, 'getOrganizationStats']);
        Route::get('organizations/equipments', [\App\Http\Controllers\Api\OrganizationController::class, 'getOrganizationEquipments']);
        Route::get('organizations/laboratories', [\App\Http\Controllers\Api\OrganizationController::class, 'getOrganizationLaboratories']);
    });

    // 角色管理
    Route::apiResource('roles', RoleController::class);

    // 角色权限管理
    Route::get('roles/{role}/permissions', [RoleController::class, 'getPermissions']);
    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions']);
    Route::get('roles/{role}/default-permissions', [RoleController::class, 'getDefaultPermissions']);

    // 权限管理
    Route::get('permissions', [PermissionController::class, 'index']);
    Route::get('permissions/tree', [PermissionController::class, 'tree']);

    // 行政区域管理
    Route::middleware(['data.scope'])->group(function () {
        Route::apiResource('regions', AdministrativeRegionController::class);
    });

    // 学校管理
    Route::middleware(['data.scope'])->group(function () {
        Route::get('schools/options', [SchoolController::class, 'options']);
        Route::apiResource('schools', SchoolController::class);
    });

    // 学科管理
    Route::middleware(['data.scope'])->group(function () {
        Route::apiResource('subjects', SubjectController::class);
        Route::get('subjects-options', [SubjectController::class, 'options']);
    });

    // 实验目录管理
    Route::middleware(['data.scope'])->group(function () {
        Route::apiResource('experiment-catalogs', ExperimentCatalogController::class);
        Route::post('experiment-catalogs/batch-import', [ExperimentCatalogController::class, 'batchImport']);
    });

    // 实验室管理
    Route::middleware(['data.scope'])->group(function () {
        Route::apiResource('laboratories', LaboratoryController::class);
        Route::get('laboratories-options', [LaboratoryController::class, 'options']);
        Route::post('laboratories/{laboratory}/check-availability', [LaboratoryController::class, 'checkAvailability']);
        Route::get('laboratories/{laboratory}/schedule', [LaboratoryController::class, 'schedule']);
    });

    // 实验室类型管理
    Route::apiResource('laboratory-types', LaboratoryTypeController::class);
    Route::post('laboratory-types/update-sort', [LaboratoryTypeController::class, 'updateSort']);

    // 设备配备标准管理
    Route::apiResource('equipment-standards', EquipmentStandardController::class);
    Route::get('equipment-standards-subjects', [EquipmentStandardController::class, 'getSubjects']);
    Route::post('equipment-standards/check-compliance', [EquipmentStandardController::class, 'checkCompliance']);

    // 实验预约管理
    Route::middleware(['data.scope'])->group(function () {
        Route::apiResource('experiment-reservations', ExperimentReservationController::class);
        Route::post('experiment-reservations/{experimentReservation}/cancel', [ExperimentReservationController::class, 'cancel']);
        Route::post('experiment-reservations/{experimentReservation}/review', [ExperimentReservationController::class, 'review']);
    });

    // 实验记录管理
    Route::middleware(['data.scope'])->group(function () {
        Route::apiResource('experiment-records', ExperimentRecordController::class);
        Route::post('experiment-records/{experimentRecord}/complete', [ExperimentRecordController::class, 'complete']);
        Route::post('experiment-records/{experimentRecord}/upload-photos', [ExperimentRecordController::class, 'uploadPhotos']);
        Route::post('experiment-records/{experimentRecord}/upload-videos', [ExperimentRecordController::class, 'uploadVideos']);
    });

    // 实验统计分析
    Route::middleware(['data.scope'])->prefix('experiment-statistics')->group(function () {
        Route::get('completion-rate', [ExperimentStatisticsController::class, 'completionRate']);
        Route::get('school-ranking', [ExperimentStatisticsController::class, 'schoolRanking']);
        Route::get('trends', [ExperimentStatisticsController::class, 'trends']);
        Route::get('subject-statistics', [ExperimentStatisticsController::class, 'subjectStatistics']);
    });

    // 设备分类管理
    Route::apiResource('equipment-categories', EquipmentCategoryController::class);
    Route::get('equipment-categories-options', [EquipmentCategoryController::class, 'options']);
    Route::get('equipment-categories-tree', [EquipmentCategoryController::class, 'tree']);
    Route::post('equipment-categories/update-sort', [EquipmentCategoryController::class, 'updateSort']);

    // 设备档案管理
    Route::middleware(['data.scope'])->group(function () {
        Route::apiResource('equipments', EquipmentController::class);
        Route::post('equipments/batch-import', [EquipmentController::class, 'batchImport']);
        Route::get('equipments/export', [EquipmentController::class, 'export']);
        Route::post('equipments/{equipment}/photos', [EquipmentController::class, 'uploadPhoto']);
        Route::delete('equipments/{equipment}/photos/{photo}', [EquipmentController::class, 'deletePhoto']);
        Route::get('equipments/{equipment}/availability', [EquipmentController::class, 'checkAvailability']);
        Route::get('equipments/stats', [EquipmentController::class, 'getStats']);
        Route::post('equipments/find-by-qr-code', [EquipmentController::class, 'findByQrCode']);
    });

    // 设备借用管理
    Route::middleware(['data.scope'])->group(function () {
        Route::apiResource('equipment-borrows', EquipmentBorrowController::class);
        Route::post('equipment-borrows/{equipmentBorrow}/review', [EquipmentBorrowController::class, 'review']);
        Route::post('equipment-borrows/{equipmentBorrow}/return', [EquipmentBorrowController::class, 'returnEquipment']);
        Route::post('equipment-borrows/batch-action', [EquipmentBorrowController::class, 'batchAction']);
        Route::post('equipment-borrows/update-overdue-status', [EquipmentBorrowController::class, 'updateOverdueStatus']);
    });

    // 设备维修管理
    Route::middleware(['data.scope'])->group(function () {
        Route::apiResource('equipment-maintenances', EquipmentMaintenanceController::class);
        Route::post('equipment-maintenances/{equipmentMaintenance}/assign', [EquipmentMaintenanceController::class, 'assignTechnician']);
        Route::post('equipment-maintenances/{equipmentMaintenance}/start', [EquipmentMaintenanceController::class, 'startMaintenance']);
        Route::post('equipment-maintenances/{equipmentMaintenance}/complete', [EquipmentMaintenanceController::class, 'completeMaintenance']);
        Route::get('equipment-maintenances/statistics', [EquipmentMaintenanceController::class, 'getStatistics']);
        Route::post('equipment-maintenances/batch-action', [EquipmentMaintenanceController::class, 'batchAction']);
    });

    // 设备二维码管理
    Route::middleware(['data.scope'])->group(function () {
        Route::post('equipments/{equipment}/qrcode', [EquipmentQrcodeController::class, 'generate']);
        Route::get('equipments/{equipment}/qrcode', [EquipmentQrcodeController::class, 'show']);
        Route::delete('equipments/{equipment}/qrcode', [EquipmentQrcodeController::class, 'destroy']);
        Route::post('equipments/batch-qrcode', [EquipmentQrcodeController::class, 'batchGenerate']);
    });

    // 统计报表相关路由
    Route::prefix('statistics')->middleware(['data.scope'])->group(function () {
        Route::get('dashboard', [StatisticsController::class, 'getDashboardStats']);
        Route::get('experiments', [StatisticsController::class, 'getExperimentStats']);
        Route::get('equipment', [StatisticsController::class, 'getEquipmentStats']);
        Route::get('users', [StatisticsController::class, 'getUserActivityStats']);
        Route::get('performance', [StatisticsController::class, 'getOrganizationPerformance']);
    });
});

// 公开的二维码查询路由（不需要认证）
Route::get('qrcode/scan/{code}', [EquipmentQrcodeController::class, 'scan']);

// 器材需求配置路由
Route::middleware(['auth:sanctum'])->group(function () {
    // 实验器材需求配置
    Route::prefix('experiment-catalogs/{catalogId}/equipment-requirements')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\EquipmentRequirementController::class, 'index']);
        Route::post('/batch', [App\Http\Controllers\Api\EquipmentRequirementController::class, 'batchStore']);
        Route::get('/recommendations', [App\Http\Controllers\Api\EquipmentRequirementController::class, 'getRecommendations']);
        Route::post('/copy', [App\Http\Controllers\Api\EquipmentRequirementController::class, 'copyFromCatalog']);
        Route::put('/sort-order', [App\Http\Controllers\Api\EquipmentRequirementController::class, 'updateSortOrder']);
        Route::put('/{requirementId}', [App\Http\Controllers\Api\EquipmentRequirementController::class, 'update']);
        Route::delete('/{requirementId}', [App\Http\Controllers\Api\EquipmentRequirementController::class, 'destroy']);
    });
    
    // 器材配置模板
    Route::apiResource('equipment-requirement-templates', App\Http\Controllers\Api\EquipmentRequirementTemplateController::class);

    // 智能预约相关路由
    Route::prefix('smart-reservations')->group(function () {
        Route::get('laboratories/{laboratoryId}/schedule', [SmartReservationController::class, 'getLaboratorySchedule']);
        Route::post('create', [SmartReservationController::class, 'smartCreate']);
        Route::post('check-conflicts', [SmartReservationController::class, 'checkConflicts']);
    });

    // 实验作品管理
    Route::apiResource('experiment-works', ExperimentWorkController::class);

    // 个人实验档案相关路由
    Route::prefix('personal')->group(function () {
        Route::get('experiment-stats', [App\Http\Controllers\Api\ExperimentReservationController::class, 'getPersonalStats']);
        Route::get('experiment-archive/export', [App\Http\Controllers\Api\ExperimentReservationController::class, 'exportPersonalArchive']);
    });
});

