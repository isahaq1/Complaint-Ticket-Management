<?php

namespace App\Http\Controllers\Api;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class DashboardController extends ApiBaseController
{
    public function index(){

        try {
        $user = Auth::user();
        $roleName = $user->getRoleNames()->first();
 
        
         $tcsql = Complaint::with('user');
         if($roleName =='user'):
         $tcsql->where('user_id',$user->id);
         endif;
         $totalcomplaints = $tcsql->get();

        //total complaints
         $totalcomps = $totalcomplaints->count();

         $alldatacollection = collect($totalcomplaints);


        //total Open 
        $totalopencomplains = $alldatacollection->where('status', "Open")->values()->count();
        //total In Progress 
        $totalprogresscomplains = $alldatacollection->where('status', "In Progress")->values()->count();
        //total In Resolved 
        $totalresolvedcomplains = $alldatacollection->where('status', "Resolved")->values()->count();

        //total In Closed 
        $totalclosedcomplains = $alldatacollection->where('status', "Closed")->values()->count();

        $totlauser = User::count();

         return $this->sendSuccess(
            data: ['total_user'=> $totlauser,'totalComplaint'=>$totalcomps,'total_open' => $totalopencomplains,'totalprogess' => $totalprogresscomplains,'totalresolved'=>$totalresolvedcomplains,'totalclosed'=>$totalclosedcomplains],
            message: 'Dashboard data fetch Successfully ',
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
