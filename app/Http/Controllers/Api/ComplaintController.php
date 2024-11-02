<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ComplaintResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Jobs\SendEmailJob;
use Exception;

class ComplaintController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
             $user = Auth::user();
             $roleName = $user->getRoleNames()->first();
             $sql = Complaint::with('user');
             if($roleName =='user'){
                $sql->where('user_id',$user->id);
             }
           
            $complaints = $sql->get();
            return $this->sendSuccess(
                data: ComplaintResource::collection($complaints),
                message: 'Successfully Fetched Complaints ',
                status: true,
                statusCode: 200

            );
        } catch (Exception $ex) {

            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong ',
                code: 203

            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $admin = User::role('admin')->first();
        
        
        try {
            $request->validate([
                'title'       => 'required',
                'category'    => 'required',
                'priority'    => 'required',
            ]);

            if ($request->file('attachment')) {
            $file = $request->file('attachment');
            $filename = date('YmdHis') . '_' .  $file->getClientOriginalName();
            $request->file('attachment')->storeAs('public/attachment/', $filename);
            $attachment = 'public/attachment/' . $filename;
            }else{
            $attachment ="";
            }
            $complaint = new Complaint();
            $complaint->title       = $request->title;
            $complaint->category_id = $request->category;
            $complaint->description = $request->description;
            $complaint->priority    = $request->priority;
            $complaint->user_id     = Auth::user()->id;
            $complaint->attachment  = $attachment;
            $complaint->save();
 
            $header = [];
             $emailData['email']    = $admin->email;
             $emailData['title']    = "Ticket Complaints";
             $emailData['subject'] = "A Ticket Complaints Has been Created";
             $emailData['body']      = $complaint->title.' Complaint Created by'.Auth::user()->name;
             SendEmailJob::dispatch($emailData,$header);
            return $this->sendSuccess(
                data: $complaint,
                message: 'Complaints Successfully Submited',
                status: true,
                statusCode: 201

            );
        } catch (Exception $ex) {

            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong ',
                code: 203

            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $complaint = Complaint::with('comments')->findOrFail($id);

            return $this->sendSuccess(
                data: new ComplaintResource($complaint),
                message: 'Complaints Details Fetch Successfully',
                status: true,
                statusCode: 200

            );
        } catch (Exception $ex) {

            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong ',
                code: 203
    
            );
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complaint $complaint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        try {
        $request->validate([
            'status' => 'required',
        ]);
    
        $complaint = Complaint::findOrFail($id);
        $complaint->status = $request->status;
        $complaint->save();

    } catch (Exception $ex) {

        return $this->sendError(
            errors: $ex->getMessage(),
            message: 'Something went wrong ',
            code: 203

        );
    }
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Complaint $complaint)
    {
        //
    }

    public function changeStatus(Request $request,$id)
    {
        try {
        $request->validate([
            'status' => 'required',
        ]);
    
        $complaint = Complaint::findOrFail($id);
        $complaint->status = $request->status;
        if($request->status == 'Resolved'){
        $complaint->resolved_date = date('Y-m-d H:i:s');
        }
        $complaint->save();

        $header = [];
        $emailData['email']    = $complaint->user->email;
        $emailData['title']    = "Ticket Complaints Change Status";
        $emailData['subject'] = "A Ticket Complaints Status Has been Changed";
        $emailData['body']      ='your Complaint '. $complaint->title.' changed Status to '.$complaint->status.' by '.Auth::user()->name;
       
        SendEmailJob::dispatch($emailData,$header);

        return $this->sendSuccess(
            data: $complaint,
            message: 'Complaints status Successfully Changed',
            status: true,
            statusCode: 201

        );

    } catch (Exception $ex) {

        return $this->sendError(
            errors: $ex->getMessage(),
            message: 'Something went wrong ',
            code: 203

        );
    }
    
    }

    //complaints report
    public function report(Request $request){
        try {
            $user = Auth::user();
             $roleName = $user->getRoleNames()->first();
             $status   = $request->status;
             $priority = $request->priority;


             $sql = Complaint::with('user');
             if($status){
                $sql->where('status',$status);
             }
             if($priority){
                $sql->where('priority',$priority);
             }
             if($roleName =='user'){
                $sql->where('user_id',$user->id);
             }
           
            $complaints = $sql->get();
            return $this->sendSuccess(
                data: ComplaintResource::collection($complaints),
                message: 'Successfully Fetched Complaints Reports',
                status: true,
                statusCode: 200

            );
        } catch (Exception $ex) {

            return $this->sendError(
                errors: $ex->getMessage(),
                message: 'Something went wrong ',
                code: 203

            );
        }

    }
}
