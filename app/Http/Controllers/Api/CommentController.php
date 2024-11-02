<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CommentResource;

class CommentController extends ApiBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request, $complaintId)
    {
        
        try {
        $request->validate([
            'comment' => 'required|string',
        ]);

        $user_id = Auth::user()->id;
        $comment = Comment::create([
            'complaint_id' => $complaintId,
            'comment' => $request->comment,
            'user_id'  => $user_id
        ]);

        return $this->sendSuccess(
            data: $comment,
            message: 'Comment Successfully Saved',
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

    public function complaintComments($id){
        try {
      $comments = Comment::with('commentby')->where('complaint_id',$id)->get();
      return $this->sendSuccess(
        data: CommentResource::collection($comments),
        message: 'Comment Fetched Successfully',
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
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
