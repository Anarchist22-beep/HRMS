<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Http\Requests\LeadRequest;
use App\Http\Requests\LeadUpdateRequest;
use App\Helpers\ApiResponse;

class LeadController extends Controller
{
    //
    public function index(Request $request)
    {
        try {

            $perPage = $request->get('per_page', 10);
            $search  = $request->get('search');
            $all     = $request->boolean('all');
            $qualified = $request->get('qualified'); // true / false / null

            $user = auth()->user();

            $query = Lead::query();


            if ($user->hasRole('Sales')) {
                $query->where('user_id', $user->id);
            }

            // Admin sees everything (no restriction)


            if (!is_null($qualified)) {
                $query->where('qualified', filter_var($qualified, FILTER_VALIDATE_BOOLEAN));
            }


            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('phone_no', 'like', "%{$search}%");
                });
            }


            $query->orderBy('id', 'desc');


            if ($all) {
                $leads = $query->get();
            } else {
                $leads = $query->paginate($perPage);
            }

            return ApiResponse::success([
                'leads' => $leads,
            ], 'Leads fetched successfully');
        } catch (\Exception $e) {

            return ApiResponse::error(
                'Failed to fetch leads: ' . $e->getMessage(),
                500
            );
        }
    }

    public function store(LeadRequest $request){
        try{
             $user = auth()->user();
             $lead = Lead::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'phone_no'=>$request->phone_no,
                'description'=>$request->description,
                'location'=>$request->location,
                'user_id'=>$user->id,
                'profile_link'=>$request->profile_link
             ]);

             return ApiResponse::success([
                'status'=>'success',
                'data'=>$lead,
                'message'=>'Lead added successfully'
             ]);


        }catch(\Exception $e){
           return ApiResponse::error(
                'Failed to add lead: ' . $e->getMessage(),
                500
            );

        }

    }

    public function edit($id){
        try{
            $lead = Lead::find($id);
            return ApiResponse::success([
                'status'=>'success',
                'data'=>$lead,
                'message'=>'lead fetched successfully'
            ]);

        }catch(\Exception $e){
            return ApiResponse::error('Failed to fetch lead:'.$e->getMessage());

        }
    }

    public function update($id,LeadRequest $request){
        try{
            $lead = Lead::find($id);
            $user = auth()->user();
            $lead->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'phone_no'=>$request->phone_no,
                'description'=>$request->description,
                'location'=>$request->location,
                'user_id'=>$user->id,
                'profile_link'=>$request->profile_link
            ]);

            return ApiResponse::success([
                'status'=>'success',
                'data'=>$lead,
                'message'=>'lead updated successfully'


            ]);


        }catch(\Exception $e){
            return ApiResponse::error('Failed to update lead:'.$e->getMessage());

        }

    }

    public function updateStatus($id,LeadUpdateRequest $request){
        try{
            $lead = Lead::find($id);
            $lead->update([
                'qualified'=> $request->qualified,
                'amount'=>$request->amount
            ]);

            return ApiResponse::success([
                'status'=>'success',
                'data'=>$lead,
                'message'=>'Lead status updated successfully'
            ]);

        }catch(\Exception $e){
            return ApiResponse::error('Failed to update lead:'.$e->getMessage());

        }

    }
    public function delete($id){
        try{
            $lead = Lead::find($id);
            $lead->delete();
            return ApiResponse::success([
                'status'=>'success',
                'message'=>'lead deleted successfully'
            ]);

        }catch(\Exception $e){
            return ApiResponse::error('Failed to delete lead:'.$e->getMessage());


        }
    }
}
