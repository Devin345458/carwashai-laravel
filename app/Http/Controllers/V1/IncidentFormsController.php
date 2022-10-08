<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\IncidentForm;
use App\Models\IncidentFormVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncidentFormsController extends Controller
{
    /**
     * View method
     * @param string $storeId The store id to get the incident form for
     */
    public function view(string $storeId)
    {
        $incidentForm = IncidentForm::firstOrNew(['store_id' => $storeId], ['name' => 'Incident Form']);
        if (!$incidentForm->incident_form_version_id) {
            if (!$incidentForm->exists) {
                $incidentForm->save();
            }
            $incidentFormVersion = IncidentFormVersion::create([
                'version' => 1,
                'incident_form_id' => $incidentForm->id,
                'data' => [
                    'tabs' => [
                        [
                            'title' => 'Customer Information',
                            'mandatory' => true,
                            'controls' => [
                                'first_name',
                                'last_name',
                                'incident_datetime',
                                'report_datetime',
                                'customer_address',
                                'customer_city',
                                'customer_state',
                                'customer_zip',
                                'customer_email',
                            ]
                        ],
                        [
                            'title' => 'Vehicle Information',
                            'mandatory' => true,
                            'controls' => [
                                'vehicle_make',
                                'vehicle_model',
                                'vehicle_year',
                                'vehicle_color',
                                'vehicle_vin',
                                'vehicle_mileage',
                                'vehicle_plate',
                                'vehicle_state',
                                'affected_area_of_vehicle',
                                'customer_statement',
                                'prior_damage',
                                'prior_damage_description',
                                'employee_notes',
                            ]
                        ]
                    ],
                    'controls' => [
                        'first_name' => [
                            'uniqueId' => 'first_name',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Fist Name',
                            'columns' => 12
                        ],
                        'last_name' => [
                            'uniqueId' => 'last_name',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Last Name',
                            'columns' => 12
                        ],
                        'incident_datetime' => [
                            'uniqueId' => 'incident_datetime',
                            'mandatory' => true,
                            'type' => 'date',
                            'time' => true,
                            'label' => 'Incident Date/Time',
                            'columns' => 12
                        ],
                        'report_datetime' => [
                            'uniqueId' => 'report_datetime',
                            'mandatory' => true,
                            'type' => 'date',
                            'time' => true,
                            'label' => 'Report Date/Time',
                            'columns' => 12
                        ],
                        'customer_address' => [
                            'uniqueId' => 'customer_address',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Customer Address',
                            'columns' => 7
                        ],
                        'customer_city' => [
                            'uniqueId' => 'customer_city',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Customer City',
                            'columns' => 5
                        ],
                        'customer_state' => [
                            'uniqueId' => 'customer_state',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Customer State',
                            'columns' => 6
                        ],
                        'customer_zip' => [
                            'uniqueId' => 'customer_zip',
                            'mandatory' => true,
                            'type' => 'number',
                            'label' => 'Customer Zip',
                            'columns' => 6
                        ],
                        'customer_email' => [
                            'uniqueId' => 'customer_email',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Email',
                            'columns' => 12
                        ],
                        'vehicle_make' => [
                            'uniqueId' => 'vehicle_make',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Vehicle Make',
                            'columns' => 12
                        ],
                        'vehicle_model' => [
                            'uniqueId' => 'vehicle_model',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Vehicle Model',
                            'columns' => 12
                        ],
                        'vehicle_year' => [
                            'uniqueId' => 'vehicle_year',
                            'mandatory' => true,
                            'type' => 'number',
                            'label' => 'Vehicle Year',
                            'columns' => 12
                        ],
                        'vehicle_color' => [
                            'uniqueId' => 'vehicle_color',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Vehicle Color',
                            'columns' => 12
                        ],
                        'vehicle_vin' => [
                            'uniqueId' => 'vehicle_vin',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Vehicle VIN',
                            'columns' => 12
                        ],
                        'vehicle_mileage' => [
                            'uniqueId' => 'vehicle_mileage',
                            'mandatory' => true,
                            'type' => 'number',
                            'label' => 'Vehicle Mileage',
                            'columns' => 12
                        ],
                        'vehicle_plate' => [
                            'uniqueId' => 'vehicle_plate',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Vehicle Plate Number',
                            'columns' => 6
                        ],
                        'vehicle_state' => [
                            'uniqueId' => 'vehicle_state',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Vehicle State',
                            'columns' => 6
                        ],
                        'affected_area_of_vehicle' => [
                            'uniqueId' => 'affected_area_of_vehicle',
                            'mandatory' => true,
                            'type' => 'input',
                            'label' => 'Affected Area of Vehicle',
                            'columns' => 12
                        ],
                        'customer_statement' => [
                            'uniqueId' => 'customer_statement',
                            'type' => 'input',
                            'label' => 'Customer Statement',
                            'columns' => 12
                        ],
                        'prior_damage' => [
                            'uniqueId' => 'prior_damage',
                            'mandatory' => true,
                            'type' => 'checkbox',
                            'label' => 'Prior Damage',
                            'columns' => 12
                        ],
                        'prior_damage_description' => [
                            'uniqueId' => 'prior_damage_description',
                            'type' => 'text',
                            'label' => 'Prior Damage Description',
                            'columns' => 12
                        ],
                        'employee_notes' => [
                            'uniqueId' => 'employee_notes',
                            'type' => 'text',
                            'label' => 'Employee Notes',
                            'columns' => 12
                        ],

                    ]
                ]
            ]);
            $incidentForm->incident_form_version_id = $incidentFormVersion->id;
            $incidentForm->save();
        }

        $incidentForm->load('current_version');

        return response()->json(compact('incidentForm'));
    }

    /**
     * update method
     */
    public function update(string $storeId): JsonResponse
    {
        /** @var IncidentForm $incidentForm */
        $incidentForm = IncidentForm::whereStoreId($storeId)
            ->with('current_version')
            ->firstOrFail();

        $incidentFormVersion = IncidentFormVersion::create([
            'incident_form_id' => $incidentForm->id,
            'version' => $incidentForm->current_version->version + 1,
            'data' => request()->input('form')
        ]);

        $incidentForm->incident_form_version_id = $incidentFormVersion->id;
        $incidentForm->save();

        $incidentForm->refresh();

        return response()->json(compact('incidentForm'));
    }

    /**
     * Delete method
     * @param string $storeId
     * @return JsonResponse
     */
    public function revert(string $storeId): JsonResponse
    {
        /** @var IncidentForm $incidentForm */
        $incidentForm = IncidentForm::whereStoreId($storeId)
            ->with('current_version')
            ->firstOrFail();

        /** @var IncidentFormVersion $incidentFormVersion */
        $incidentFormVersion = IncidentFormVersion::where('incident_form_id', $incidentForm->id)
            ->where('version', request('version'))
            ->firstOrFail();

        $incidentFormVersion = IncidentFormVersion::create([
            'incident_form_id' => $incidentForm->id,
            'version' => $incidentForm->current_version->version + 1,
            'data' => $incidentFormVersion->data
        ]);

        $incidentForm->incident_form_version_id = $incidentFormVersion->id;
        $incidentForm->save();

        $incidentForm->refresh();
        return response()->json(compact('incidentForm'));
    }
}
