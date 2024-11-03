<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ComplaintResource;
use App\Http\Resources\ResoultionReport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\DB;
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
            if ($roleName == 'user') {
                $sql->where('user_id', $user->id);
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
            } else {
                $attachment = "";
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
            $emailData['body']      = $complaint->title . ' Complaint Created by' . Auth::user()->name;
            SendEmailJob::dispatch($emailData, $header);
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
    public function update(Request $request, $id)
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

    public function changeStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required',
            ]);

            $complaint = Complaint::findOrFail($id);
            $complaint->status = $request->status;
            if ($request->status == 'Resolved') {
                $complaint->resolved_at = date('Y-m-d H:i:s');
            }

            if ($request->status == 'Closed') {
                $complaint->closed_at = date('Y-m-d H:i:s');
            }
            $complaint->save();

            $header = [];
            $emailData['email']    = $complaint->user->email;
            $emailData['title']    = "Ticket Complaints Change Status";
            $emailData['subject']  = "A Ticket Complaints Status Has been Changed";
            $emailData['body']     = 'your Complaint ' . $complaint->title . ' changed Status to ' . $complaint->status . ' by ' . Auth::user()->name;

            SendEmailJob::dispatch($emailData, $header);

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
    public function report(Request $request)
    {
        try {
            $user = Auth::user();
            $roleName = $user->getRoleNames()->first();
            $status   = $request->status;
            $priority = $request->priority;


            $sql = Complaint::with('user');
            if ($status) {
                $sql->where('status', $status);
            }
            if ($priority) {
                $sql->where('priority', $priority);
            }
            if ($roleName == 'user') {
                $sql->where('user_id', $user->id);
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

    public function priorityReport()
    {
        try {
            $totalcomplaints = Complaint::all();

            $alldatacollection = collect($totalcomplaints);
            $totallow          = $alldatacollection->where('priority', "Low")->values()->count();
            $totalmedium       = $alldatacollection->where('priority', "Medium")->values()->count();
            $totalhigh         = $alldatacollection->where('priority', "High")->values()->count();

            return $this->sendSuccess(
                data: ['total_low' => $totallow, 'total_medium' => $totalmedium, 'total_high' => $totalhigh],
                message: 'Priority Summary Report fetched Successfully ',
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

    public function statusReport()
    {
        try {
            $totalcomplaints = Complaint::all();

            $alldatacollection = collect($totalcomplaints);
            $totalopen         = $alldatacollection->where('status', "Open")->values()->count();
            $totalinprogress   = $alldatacollection->where('status', "In Progress")->values()->count();
            $totalresolved     = $alldatacollection->where('status', "Resolved")->values()->count();
            $totalclosed       = $alldatacollection->where('status', "Closed")->values()->count();

            return $this->sendSuccess(
                data: ['total_open' => $totalopen, 'total_progress' => $totalinprogress, 'total_resolved' => $totalresolved, 'total_closed' => $totalclosed],
                message: 'Status Summary Report fetch Successfully ',
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

    public function categoryReport()
    {
        try {
            $categories = Category::all();
            $totalcomplaints = Complaint::with('user')->get();
            $alldatacollection = collect($totalcomplaints);
            $result = [];
            foreach ($categories as $category) {
                $totalcomplaint = $alldatacollection->where('category_id', $category->id)->values()->count();
                $resp = [
                    'category' => $category->name,
                    'total'   => $totalcomplaint,
                ];

                array_push($result, $resp);
            }

            return $this->sendSuccess(
                data: $result,
                message: 'Category Summary Report fetch Successfully ',
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

    public function resolutionReport()
    {
        try {

            $averages = Complaint::with('category')->select('category_id')
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, resolved_at)) as average_resolution_time_in_seconds')
                ->whereNotNull('resolved_at')
                ->groupBy('category_id')
                ->get()
                ->map(function ($item) {
                    // Convert seconds into days, hours, and minutes
                    $totalSeconds = (int)$item->average_resolution_time_in_seconds;
                    $days = floor($totalSeconds / (60 * 60 * 24));
                    $hours = floor(($totalSeconds % (60 * 60 * 24)) / (60 * 60));
                    $minutes = floor(($totalSeconds % (60 * 60)) / 60);

                    $item->average_resolution_time = "{$days}d {$hours}h {$minutes}m";

                    return $item;
                });

            return $this->sendSuccess(
                data: ResoultionReport::collection($averages),
                message: 'Resolution Summary Report fetch Successfully ',
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

    public function complaintTrend(Request $request)
    {
        try {
            $start_date = ($request->start_date ? $request->start_date : date('Y-m-1 H:i:s'));
            $end_date   = ($request->end_date ? $request->end_date : now());

            $startDate = new Carbon($start_date);
            $endDate = new Carbon($end_date);

            // Get complaint trends
            $trends = Complaint::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_submitted'),
                DB::raw('SUM(CASE WHEN resolved_at IS NOT NULL THEN 1 ELSE 0 END) as total_resolved'),
                DB::raw('SUM(CASE WHEN closed_at IS NOT NULL THEN 1 ELSE 0 END) as total_closed')
            )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return $this->sendSuccess(
                data: $trends,
                message: 'Complaint trends Report fetch Successfully ',
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
