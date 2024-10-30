<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class ComplaintController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $complaints = Complaint::all();
            return $this->sendSuccess(
                data: $complaints,
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
        try {
            $complaint = new Complaint();
            $complaint->title = $request->title;
            $complaint->category_id = $request->category;
            $complaint->description = $request->description;
            $complaint->priority    = $request->priority;
            $complaint->user_id     = Auth::user()->id;
            $complaint->save();

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
    public function show(Complaint $complaint)
    {
        //
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
    public function update(Request $request, Complaint $complaint)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Complaint $complaint)
    {
        //
    }
}
