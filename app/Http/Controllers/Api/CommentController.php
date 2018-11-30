<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Product;
use App\Comment;
use App\Campaign;
use App\Http\Resources\Comment as CommentResource;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:create,App\Comment')->only('store');
        $this->middleware('can:update,comment')->only('update');
        $this->middleware('can:delete,comment')->only('destroy');
    }

    public function index(Request $request)
    {
        $per_page = $request->get('per_page', 10);

        $comment = Comment::query();

        if($request->has('type'))
        {
            if($request->type == 'product')
            {
                $product = Product::findBySlugOrFail($request->slug);
                $comment = $product->comments()->whereNull('parent_id');
            }

            if($request->type == 'campaign')
            {
                $campaign = Campaign::findBySlugOrFail($request->slug);
                $comment = $campaign->comments()->whereNull('parent_id');
            }
        }

        if($request->has('order'))
        {
            $order = $request->order;
            $type = $order[0] == '-' ? 'desc' : 'asc';
            $order = str_replace('-', '', $order);
            switch ($order) {
                case 'date':
                    $comment = $comment->orderBy('updated_at', $type);
                    break;
                default:
                    break;
            }
        }

        return CommentResource::collection($comment->paginate($per_page));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->has('type'))
        {
            if($request->type == 'product')
            {
                $product = Product::findBySlugOrFail($request->slug);
                $result = $product->comments()->create($request->all());
            }

            if($request->type == 'campaign')
            {
                $campaign = Campaign::findBySlugOrFail($request->slug);
                $result = $campaign->comments()->create($request->all());
            }
        }

        if($result)
            return response()->json([
                'status' => 'ok'
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        if($comment->update($request->all()))
            return response()->json([
                'status' => 'ok'
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        if($comment->delete())
        {
            return response()->json([
                'status' => 'ok'
            ]);
        }
    }
}
