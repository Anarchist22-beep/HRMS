<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Models\User;
use App\Http\Resources\ShiftResource;
use App\Http\Requests\ShiftRequest;
use App\Http\Requests\ShiftStatusUpdateRequest;
use App\Http\Requests\AssignShiftRequest;

class ShiftController extends Controller
{
    //
    public function index(Request $request)
    {
        try {

            $perPage = $request->get('per_page', 10);

            $search = $request->get('search');

            $all = $request->boolean('all');

            $query = Shift::query();

            // Search
            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }

            // Latest first
            $query->latest();

            if ($all) {

                $query->where('status', 1);

                $shifts = $query->get();

                return ApiResponse::success([
                    'shifts' => ShiftResource::collection($shifts),
                ], 'Shifts fetched successfully');
            }

            // Paginated
            $shifts = $query->paginate($perPage);

            return ApiResponse::success([
                'shifts' => ShiftResource::collection($shifts)->response()->getData(true),
            ], 'Shifts fetched successfully');
        } catch (\Exception $e) {

            return ApiResponse::error(
                'Failed to fetch shifts: ' . $e->getMessage(),
                500
            );
        }
    }

    public function store(ShiftRequest $request)
    {
        try {
            $shift = Shift::create([
                'name' => $request->name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'breaK_minute' => $request->breaK_minute,
                'hours' => $request->hours,
                'grace_minutes' => $request->grace_minutes
            ]);
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'shift added successfully',
                'data' => $shift

            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to create shift: ' . $e->getMessage(),
                500
            );
        }
    }

    public function edit($id)
    {
        try {

            $shift = Shift::find($id);

            return ApiResponse::success([
                'shift' => $shift
            ], 'Shift fetched successfully');
        } catch (\Exception $e) {

            return ApiResponse::error(
                'Error fetching shift: ' . $e->getMessage(),
                500
            );
        }
    }

    public function update($id, ShiftRequest $request)
    {
        try {
            $shift = Shift::find($id);
            $shift->update([
                'name' => $request->name,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'breaK_minute' => $request->breaK_minute,
                'hours' => $request->hours,
                'grace_minutes' => $request->grace_minutes

            ]);
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'shift added successfully',
                'data' => $shift

            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to create shift: ' . $e->getMessage(),
                500
            );
        }
    }

    public function updateStatus($id, ShiftStatusUpdateRequest $request)
    {
        try {
            $shift = Shift::findorfail($id);
            $shift->update([
                'status' => $request->status

            ]);

            return ApiResponse::success([
                'message' => 'Shift status updated successfully',
                'data' => $shift
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to update shift status: ',
                500
            );
        }
    }

    public function delete($id)
    {
        try {
            $shift = Shift::find($id);
            $shift->delete();
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Shift Deleted Successfully',

            ]);
        } catch (\Exception $e) {
            return ApiResponse::error([
                'status' => 'error',
                'message' => 'Error deleting shift' . $e->getMessage()

            ], 500);
        }
    }

    public function list_employee(Request $request)
    {
        try {

            $employees = User::whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Admin');
                })
                ->where('status', 1)
                ->select('id', 'name', 'depart_id')
                ->with('roles:id,name')
                ->get()
                ->map(function ($employee) {
                    return [
                        'id'   => $employee->id,
                        'name' => $employee->name,
                        'depart_id' => $employee->depart_id,
                        'role' => $employee->roles->pluck('name')->first(),
                    ];
                });

            return ApiResponse::success([
                'status'   => 'success',
                'employee' => $employees,
            ], 'employees fetched successfully');
        } catch (\Exception $e) {

            return ApiResponse::error(
                'Failed to fetch employees: ' . $e->getMessage(),
                500
            );
        }
    }

    public function list_schedule(Request $request)
    {
        try {

            $perPage = $request->get('per_page', 10);

            $search = $request->get('search');

            $all = $request->boolean('all');

            $query = ShiftSchedule::with([
                'user:id,name',
                'shift:id,name,start_time,end_time'
            ]);

            // Search
            if ($search) {

                $query->where(function ($q) use ($search) {

                    $q->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })

                        ->orWhereHas('shift', function ($shiftQuery) use ($search) {
                            $shiftQuery->where('name', 'like', "%{$search}%");
                        });
                });
            }

            // Latest first
            $query->latest();

            if ($all) {

                $schedules = $query->get()->map(function ($schedule) {

                    // Convert time to 12-hour format
                    if ($schedule->shift) {

                        $schedule->shift->start_time =
                            \Carbon\Carbon::parse($schedule->shift->start_time)
                            ->format('h:i A');

                        $schedule->shift->end_time =
                            \Carbon\Carbon::parse($schedule->shift->end_time)
                            ->format('h:i A');
                    }

                    return $schedule;
                });
            } else {

                $schedules = $query->paginate($perPage);

                $schedules->getCollection()->transform(function ($schedule) {

                    // Convert time to 12-hour format
                    if ($schedule->shift) {

                        $schedule->shift->start_time =
                            \Carbon\Carbon::parse($schedule->shift->start_time)
                            ->format('h:i A');

                        $schedule->shift->end_time =
                            \Carbon\Carbon::parse($schedule->shift->end_time)
                            ->format('h:i A');
                    }

                    return $schedule;
                });
            }

            return ApiResponse::success([
                'schedules' => $schedules,
            ], 'Shift schedules fetched successfully');
        } catch (\Exception $e) {

            return ApiResponse::error(
                'Failed to fetch shift schedules: ' . $e->getMessage(),
                500
            );
        }
    }

    public function store_schedule(AssignShiftRequest $request)
    {
        try {
            $assign_shift = ShiftSchedule::create([
                'user_id' => $request->user_id,
                'shift_id' => $request->shift_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'monday' => $request->monday,
                'tuesday' => $request->tuesday,
                'wednesday' => $request->wednesday,
                'thursday' => $request->thursday,
                'friday' => $request->friday,
                'saturday' => $request->saturday,
                'sunday' => $request->sunday,



            ]);

            return ApiResponse::success([
                'status' => 'success',
                'data' => $assign_shift,
                'message' => 'Shift Successfully Assigned to user'

            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to assign shift: ' . $e->getMessage(),
                500
            );
        }
    }

    public function schedule_edit($id){
        try{
            $schedule = ShiftSchedule::find($id);
            return ApiResponse::success([
                'status'=>'success',
                'data'=>$schedule,
                'message'=>'Shift Schedule fetched successfully'

            ]);


        }catch(\Exception $e){
            return ApiResponse::error(
                'Error fetching schedule: ' . $e->getMessage(),
                500
            );


        }
    }

    public function update_schedule(AssignShiftRequest $request,$id){
        try{
            $schedule = ShiftSchedule::find($id);
            $schedule->update([
                'user_id' => $request->user_id,
                'shift_id' => $request->shift_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'monday' => $request->monday,
                'tuesday' => $request->tuesday,
                'wednesday' => $request->wednesday,
                'thursday' => $request->thursday,
                'friday' => $request->friday,
                'saturday' => $request->saturday,
                'sunday' => $request->sunday,


            ]);
            return ApiResponse::success([
                'status' => 'success',
                'data' => $schedule,
                'message' => 'Shift Successfully Updated and Assigned to user'

            ]);

        }catch(\Exception $e){
            return ApiResponse::error(
                'Failed to assign shift: ' . $e->getMessage(),
                500
            );

        }

    }

    public function delete_schedule($id){
        try{
            $schedule = ShiftSchedule::find($id);
            $schedule->delete();
            return ApiResponse::success([
                'status'=>'success',
                'message'=>'schedule deleted successfully'

            ]);

        }catch(\Exception $e){
            return ApiResponse::error([
                'status' => 'error',
                'message' => 'Error deleting shift' . $e->getMessage()

            ], 500);

        }

    }
}
