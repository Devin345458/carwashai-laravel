<?php

use App\Http\Controllers\V1\CompletedProcedureStepsController;
use App\Http\Controllers\V1\CarCountsController;
use App\Http\Controllers\V1\CategoriesController;
use App\Http\Controllers\V1\CommentsController;
use App\Http\Controllers\V1\CompaniesController;
use App\Http\Controllers\V1\ContactLogsController;
use App\Http\Controllers\V1\DashboardController;
use App\Http\Controllers\V1\EquipmentFilesController;
use App\Http\Controllers\V1\EquipmentGroupsController;
use App\Http\Controllers\V1\EquipmentsController;
use App\Http\Controllers\V1\FilesController;
use App\Http\Controllers\V1\IncidentFormsController;
use App\Http\Controllers\V1\IncidentFormSubmissionsController;
use App\Http\Controllers\V1\InventoriesController;
use App\Http\Controllers\V1\InventoryTransactionsController;
use App\Http\Controllers\V1\ItemsController;
use App\Http\Controllers\V1\ItemTypesController;
use App\Http\Controllers\V1\LocationsController;
use App\Http\Controllers\V1\MaintenancesController;
use App\Http\Controllers\V1\MaintenanceSessionsController;
use App\Http\Controllers\V1\MaintenanceSessionsMaintenancesController;
use App\Http\Controllers\V1\NotificationsController;
use App\Http\Controllers\V1\OrderItemsController;
use App\Http\Controllers\V1\ProcedureAssignmentsController;
use App\Http\Controllers\V1\ProceduresController;
use App\Http\Controllers\V1\RecordingsController;
use App\Http\Controllers\V1\RepairRemindersController;
use App\Http\Controllers\V1\RepairsController;
use App\Http\Controllers\V1\RolesController;
use App\Http\Controllers\V1\StoresController;
use App\Http\Controllers\V1\SuppliersController;
use App\Http\Controllers\V1\TransferRequestsController;
use App\Http\Controllers\V1\UsersController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

Route::group(['prefix' => '/v1'], function () {
    Route::group(['prefix' => '/users'], function () {
        Route::post('/register', [UsersController::class, 'register']);
        Route::post('/login', [UsersController::class, 'login']);
        Route::post('/reset-password', [UsersController::class, 'resetPassword']);
    });

    Route::post('/stores/subscription-end-point', [StoresController::class, 'subscriptionEndPoint']);

    // Files Routes
    Route::group(['prefix' => '/files'], function () {
        Route::get('/thumbnail/{id}', [FilesController::class, 'thumbnail']);
        Route::get('/image/{id}/{size?}', [FilesController::class, 'image']);
    });

    // Equipment Files Routes
    Route::group(['prefix' => '/equipments-files'], function () {
        Route::get('/thumbnail/{id}', [EquipmentFilesController::class, 'thumbnail']);
        Route::get('/image/{id}/{size?}', [EquipmentFilesController::class, 'image']);
    });

    Route::group(['middleware' => ['auth', 'team']], function () {
        Route::group(['prefix' => '/dashboard'], function () {
            Route::get('/recommendations/{store}', [DashboardController::class, 'recommendations']);
            Route::get('/recent-activity/{store?}', [DashboardController::class, 'activity']);
            Route::get('/statistics/{store?}', [DashboardController::class, 'statistics']);
        });

        // Car Count Routes
        Route::get('/car-counts/check-car-count', [CarCountsController::class, 'checkCarCount']);

        // Categories Routes
        Route::get('/categories/get-company-categories/{type}', [CategoriesController::class, 'getCompanyCategories']);

        // Companies Routes
        Route::get('/companies/get-company', [CompaniesController::class, 'getCompany']);
        Route::get('/companies/store-count', [CompaniesController::class, 'storeCount']);
        Route::get('/companies/stores', [CompaniesController::class, 'stores']);
        Route::get('/companies/all', [CompaniesController::class, 'all']);
        Route::post('/companies/update-settings', [CompaniesController::class, 'updateSettings']);


        // EquipmentGroups Routes
        Route::group(['prefix' => '/equipment-groups'], function () {
            Route::post('/add', [EquipmentGroupsController::class, 'add']);
            Route::post('/edit/{id}', [EquipmentGroupsController::class, 'edit']);
            Route::delete('/delete/{equipmentGroup}', [EquipmentGroupsController::class, 'delete']);
            Route::get('/view/{id}', [EquipmentGroupsController::class, 'view']);
            Route::get('/{storeId?}', [EquipmentGroupsController::class, 'index']);
        });

        // Equipments Routes
        Route::group(['prefix' => '/equipments'], function () {
            Route::get('/get-equipment/{storeId?}', [EquipmentsController::class, 'getEquipment']);
            Route::post('/add', [EquipmentsController::class, 'add']);
            Route::post('/edit/{id}', [EquipmentsController::class, 'edit']);
            Route::get('/view/{id}', [EquipmentsController::class, 'view']);
            Route::get('/equipment-activities/{id}', [EquipmentsController::class, 'equipmentActivities']);
            Route::delete('/delete/{id}', [EquipmentsController::class, 'delete']);
            Route::get('/by-store/{storeId?}', [EquipmentsController::class, 'byStore']);
            Route::get('/catalogue', [EquipmentsController::class, 'catalogue']);
            Route::post('/reorder/{locationId}', [EquipmentsController::class, 'reorder']);
            Route::post('/copy-equipment/{storeId}/{locationId}', [EquipmentsController::class, 'copyEquipment']);
        });

        // Files Routes
        Route::group(['prefix' => '/files'], function () {
            Route::get('/media', [FilesController::class, 'media']);
            Route::get('/file/{id}', [FilesController::class, 'file']);
            Route::post('/save', [FilesController::class, 'save']);
            Route::delete('/delete', [FilesController::class, 'delete']);
            Route::post('/upload', [FilesController::class, 'upload']);
        });

        // Equipment Files Routes
        Route::group(['prefix' => '/equipments-files'], function () {
            Route::get('/media', [EquipmentFilesController::class, 'media']);
            Route::get('/file/{id}', [EquipmentFilesController::class, 'file']);
            Route::post('/save', [EquipmentFilesController::class, 'save']);
            Route::delete('/delete', [EquipmentFilesController::class, 'delete']);
            Route::post('/upload', [EquipmentFilesController::class, 'upload']);
        });

        // Incident Forms Routes
        Route::group(['prefix' => '/incident-forms'], function () {
            Route::get('/view/{storeId}', [IncidentFormsController::class, 'view']);
            Route::post('/update/{storeId}', [IncidentFormsController::class, 'update']);
            Route::post('/revert/{storeId}', [IncidentFormsController::class, 'revert']);
        });

        // Incident Form Submissions Routes
        Route::group(['prefix' => '/incident-form-submissions'], function () {
            Route::get('/view/{id}', [IncidentFormSubmissionsController::class, 'view']);
            Route::post('/add/{storeId}', [IncidentFormSubmissionsController::class, 'add']);
            Route::post('/edit/{id}', [IncidentFormSubmissionsController::class, 'edit']);
            Route::post('/change-status/{id}', [IncidentFormSubmissionsController::class, 'changeStatus']);
            Route::get('/activities/{id}', [IncidentFormSubmissionsController::class, 'activities']);
            Route::get('/metrics/{storeId?}', [IncidentFormSubmissionsController::class, 'metrics']);
            Route::get('/{storeId?}', [IncidentFormSubmissionsController::class, 'index']);
            Route::get('/store/{store}', [IncidentFormSubmissionsController::class, 'store']);
        });

        // Inventories Routes
        Route::group(['prefix' => '/inventories'], function () {
            Route::post('/create-store-record', [InventoriesController::class, 'createStoreRecord']);
            Route::get('/view/{id}', [InventoriesController::class, 'view']);
            Route::post('/upsert', [InventoriesController::class, 'upsert']);
            Route::get('/stores-with-inventory/{itemId}/{quantity}/{storeId?}', [InventoriesController::class, 'storesWithInventory']);
            Route::get('/types', [InventoriesController::class, 'types']);
            Route::get('/dashboard-widget/{storeId?}', [InventoriesController::class, 'dashboardWidget']);
            Route::get('/{storeId?}', [InventoriesController::class, 'index']);
        });

        // Inventory Transactions Routes
        Route::group(['prefix' => '/inventory-transactions'], function () {
            Route::get('/inventory-item-history/{id}', [InventoryTransactionsController::class, 'inventoryItemHistory']);
            Route::post('/save-conduct-inventory/{storeId}', [InventoryTransactionsController::class, 'saveConductInventory']);
            Route::get('/history', [InventoryTransactionsController::class, 'history']);
        });

        // Items Routes
        Route::group(['prefix' => '/items'], function () {
            Route::get('/search', [ItemsController::class, 'search']);
            Route::post('/upsert', [ItemsController::class, 'upsert']);
            Route::post('/import-vendor-products', [ItemsController::class, 'importVendorProducts']);
        });

        // Item Types Routes
        Route::group(['prefix' => '/item-types'], function () {
            Route::get('', [ItemTypesController::class, 'index']);
            Route::post('/add', [ItemTypesController::class, 'add']);
            Route::post('/edit/{id}', [ItemTypesController::class, 'edit']);
            Route::delete('/delete/{id}', [ItemTypesController::class, 'delete']);
        });

        // Locations Routes
        Route::group(['prefix' => '/locations'], function () {
            Route::get('/get-stores-locations/{storeId}', [LocationsController::class, 'getStoresLocations']);
            Route::post('/upsert/{storeId}', [LocationsController::class, 'upsert']);
            Route::post('/reorder', [LocationsController::class, 'reorder']);
            Route::delete('/delete/{locationId}', [LocationsController::class, 'delete']);
        });

        // Maintenances Routes
        Route::group(['prefix' => '/maintenances'], function () {
            Route::post('/add', [MaintenancesController::class, 'add']);
            Route::post('/edit/{id}', [MaintenancesController::class, 'edit']);
            Route::delete('/delete/{id}', [MaintenancesController::class, 'delete']);
            Route::get('/stores-maintenance/{storeId}', [MaintenancesController::class, 'storesMaintenance']);
            Route::get('/get-maintenances', [MaintenancesController::class, 'getMaintenances']);
            Route::get('/dashboard-widget/{storeId?}', [MaintenancesController::class, 'dashboardWidget']);
            Route::get('/get-maintenance/{id}', [MaintenancesController::class, 'getMaintenance']);
            Route::get('/catalogue', [MaintenancesController::class, 'catalogue']);
            Route::get('/get-maintenances-by-ids', [MaintenancesController::class, 'getMaintenancesByIds']);
            Route::get('/view/{id}', [MaintenancesController::class, 'view']);
            Route::get('/equipment/{id}', [MaintenancesController::class, 'equipment']);
            Route::post('/copy-maintenance', [MaintenancesController::class, 'copyMaintenance']);
        });

        // Maintenance Sessions Routes
        Route::group(['prefix' => '/maintenance-sessions'], function () {
            Route::get('/get-active-user-session-by-store-id/{storeId?}', [MaintenanceSessionsController::class, 'getActiveUserSessionByStoreId']);
            Route::post('/mark-skipped/{storeId}', [MaintenanceSessionsController::class, 'markSkipped']);
            Route::post('/complete-maintenance/{sessionId}', [MaintenanceSessionsController::class, 'completeMaintenance']);
            Route::post('/add', [MaintenanceSessionsController::class, 'add']);
            Route::post('/complete-maintenance-session', [MaintenanceSessionsController::class, 'completeMaintenanceSession']);
            Route::get('/dashboard-widget/{storeId?}', [MaintenanceSessionsController::class, 'dashboardWidget']);
        });

        // Maintenance Sessions Maintenances Routes
        Route::group(['prefix' => '/maintenance-sessions-maintenances'], function () {
            Route::post('/add-comment/{id}', [MaintenanceSessionsMaintenancesController::class, 'addComment']);
            Route::get('/get-comments/{id}', [MaintenanceSessionsMaintenancesController::class, 'getComments']);
            Route::get('/search/{store?z}', [MaintenanceSessionsMaintenancesController::class, 'search']);
        });

        // Order Items Routes
        Route::group(['prefix' => '/order-items'], function () {
            Route::get('/approved-order-items/{storeId?}', [OrderItemsController::class, 'approvedOrderItems']);
            Route::get('/purchase-order-items/{storeId?}', [OrderItemsController::class, 'purchaseOrderItems']);
            Route::get('/pending-delivery/{storeId?}', [OrderItemsController::class, 'pendingDelivery']);
            Route::get('/history/{storeId?}', [OrderItemsController::class, 'history']);
            Route::get('/pending-order-items/{storeId?}', [OrderItemsController::class, 'pendingOrderItems']);
            Route::get('/get-item-counts/{storeId?}', [OrderItemsController::class, 'getItemCounts']);
            Route::get('/order-items-by-inventory/{id}', [OrderItemsController::class, 'orderItemsByInventory']);
            Route::get('/dashboard-widget/{storeId}', [OrderItemsController::class, 'dashboardWidget']);
            Route::post('/update-status', [OrderItemsController::class, 'updateStatus']);
            Route::post('/mark-ordered', [OrderItemsController::class, 'markOrdered']);
            Route::post('/mark-received', [OrderItemsController::class, 'markReceived']);
            Route::post('/create/{storeId}', [OrderItemsController::class, 'create']);
        });

        // Repair Reminders Routes
        Route::group(['prefix' => '/repair-reminders'], function () {
            Route::post('/upsert', [RepairRemindersController::class, 'upsert']);
            Route::get('/get-reminder/{id}', [RepairRemindersController::class, 'getReminder']);
        });

        // Repairs Routes
        Route::group(['prefix' => '/repairs'], function () {
            Route::post('/add', [RepairsController::class, 'add']);
            Route::get('/view/{id}', [RepairsController::class, 'view']);
            Route::post('/update-field/{id}', [RepairsController::class, 'updateField']);
            Route::delete('/delete/{repairId}', [RepairsController::class, 'delete']);
            Route::post('/complete-repair/{id}', [RepairsController::class, 'completeRepair']);
            Route::get('/dashboard-widget/{storeId?}', [RepairsController::class, 'dashboardWidget']);
            Route::get('/activities/{id}', [RepairsController::class, 'activities']);
            Route::delete('/delete-item-from-repair/{id}/{itemId}', [RepairsController::class, 'deleteItemFromRepair']);
            Route::post('/bulk-import', [RepairsController::class, 'bulkImport']);
            Route::post('/{storeId?}', [RepairsController::class, 'index']);
        });

        // Stores Routes
        Route::group(['prefix' => '/stores'], function () {
            Route::get('/get-users-stores/{pagination?}', [StoresController::class, 'getUsersStores']);
            Route::post('/set-store/{storeId?}', [StoresController::class, 'setStore']);
            Route::get('/settings', [StoresController::class, 'settings']);
            Route::post('/save-settings/{storeId}', [StoresController::class, 'saveSettings']);
            Route::get('/users/{storeId}', [StoresController::class, 'users']);
            Route::post('/reactivate/{storeId}', [StoresController::class, 'reactivate']);
            Route::post('/cancel-subscription/{storeId}', [StoresController::class, 'cancelSubscription']);
            Route::post('/add', [StoresController::class, 'add']);
            Route::get('/view/{store}', [StoresController::class, 'view']);
        });

        // Suppliers Routes
        Route::group(['prefix' => '/suppliers'], function () {
            Route::get('/{storeId?}', [SuppliersController::class, 'index']);
            Route::get('/search/{storeId?}', [SuppliersController::class, 'search']);
            Route::post('/upsert/{storeId}', [SuppliersController::class, 'upsert']);
            Route::delete('/delete/{supplierId}', [SuppliersController::class, 'delete']);
        });

        // Transfer Requests Routes
        Route::group(['prefix' => '/transfer-requests'], function () {
           Route::post('/add', [TransferRequestsController::class, 'add']);
           Route::get('/get-item-counts/{storeId?}', [TransferRequestsController::class, 'getItemCounts']);
           Route::get('/get-transfers-by-status/{storeId?}', [TransferRequestsController::class, 'getTransfersByStatus']);
           Route::get('/history', [TransferRequestsController::class, 'history']);
           Route::post('/update-status', [TransferRequestsController::class, 'updateStatus']);
           Route::delete('/delete/{id}', [TransferRequestsController::class, 'delete']);
        });

        // Users Routes
        Route::group(['prefix' => '/users'], function () {
            Route::get('/logged-in-user', [UsersController::class, 'loggedInUser']);
            Route::get('/view/{user}', [UsersController::class, 'view']);
            Route::get('/check-email/{email}', [UsersController::class, 'checkEmail']);
            Route::post('/add-store/{user}', [UsersController::class, 'addStore']);
            Route::post('/logout', [UsersController::class, 'logout']);
            Route::post('/edit', [UsersController::class, 'register']);
            Route::post('/add', [UsersController::class, 'add']);
            Route::get('/store/{storeId}', [UsersController::class, 'store']);
            Route::post('/upsert/{storeId?}', [UsersController::class, 'upsert']);
            Route::delete('/remove-store/{userId}/{storeId}', [UsersController::class, 'removeStore']);
            Route::get('/all', [UsersController::class, 'all']);
            Route::get('/{storeId?}', [UsersController::class, 'index']);
            Route::post('/save-device-token', [UsersController::class, 'saveDeviceToken']);
        });

        // Notification Routes
        Route::group(['prefix' => '/notifications'], function () {
           Route::get('', [NotificationsController::class, 'index']);
            Route::get('/{id}', [NotificationsController::class, 'read']);
        });

        // Comment Routes
        Route::group(['prefix' => '/comments'], function () {
            Route::get('', [CommentsController::class, 'index']);
            Route::post('/add', [CommentsController::class, 'add']);
        });

        // Procedure Routes
        Route::group(['prefix' => '/procedures'], function () {
            Route::delete('/delete/{procedure}', [ProceduresController::class, 'delete']);
            Route::post('/upsert/{storeId}', [ProceduresController::class, 'upsert']);
            Route::get('/view/{procedure}', [ProceduresController::class, 'view']);
            Route::get('/{storeId}', [ProceduresController::class, 'index']);
        });

        // Procedure Assignment Routes
        Route::group(['prefix' => '/procedure-assignments'], function () {
            Route::post('/assign', [ProcedureAssignmentsController::class, 'assign']);
            Route::get('/{storeId}', [ProcedureAssignmentsController::class, 'index']);
        });

        // Procedure Assignment Routes
        Route::group(['prefix' => '/complete-procedure-steps'], function () {
            Route::post('/add-note/{id}', [CompletedProcedureStepsController::class, 'addNote']);
            Route::post('/toggle/{id}', [CompletedProcedureStepsController::class, 'toggle']);
            Route::get('/view/{id}', [CompletedProcedureStepsController::class, 'view']);
            Route::post('/complete', [CompletedProcedureStepsController::class, 'complete']);
            Route::get('/{storeId}', [CompletedProcedureStepsController::class, 'index']);
        });

        Route::group(['prefix' => '/recordings'], function () {
            Route::post('/save/{id}', [RecordingsController::class, 'save']);
        });

        Route::group(['prefix' => '/contact-logs'], function () {
            Route::post('/save/{id}', [ContactLogsController::class, 'save']);
        });

        Route::group(['prefix' => '/roles'], function () {
            Route::get('', [RolesController::class, 'index']);
            Route::get('all', [RolesController::class, 'all']);
            Route::get('possible-permissions', [RolesController::class, 'possiblePermissions']);
            Route::get('view/{role}', [RolesController::class, 'view']);
            Route::post('/update/{role}', [RolesController::class, 'update']);
            Route::post('/create', [RolesController::class, 'create']);
        });
    });
});
