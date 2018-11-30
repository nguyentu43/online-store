<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Campaign;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Campaign as CampaignResource;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:create,App\Campaign')->only('store');
        $this->middleware('can:update,campaign')->only('update');
        $this->middleware('can:delete,campaign')->only('destroy');
    }

    public function index(Request $request)
    {
        $campaign = Campaign::query();

        if(!($request->skip_prop == 'enable' && $request->user('api')->can('create', 'App\Campaign')))
        {
            $campaign = $campaign->where('enable', 1)->whereRaw('start_datetime <= now() and end_datetime >= now()');
        }

        $campaign = $campaign->get();

        if($request->has('category'))
        {
            $campaign = $campaign->filter(function($item){ 
                global $request;
                return in_array($request->category, $item->categories);
            });
        }

        return CampaignResource::collection($campaign);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required'
        ]);

        $campaign = Campaign::create($request->all());

        if($request->has('products'))
        {
            foreach($request->products as $product)
            {
                $campaign->products()->attach($product['id']);
            }
        }

        if($campaign)
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
    public function show(Campaign $campaign)
    {
        if(!$campaign->enable)
            return response()->json([ 'status' => 'error', 'message' => 'Banner not found' ], 404);
        return new CampaignResource($campaign);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Campaign $campaign)
    {
        $campaign->slug = null;
        $result = $campaign->update($request->all());

        if($request->has('products'))
        {
            $campaign->products()->detach();

            foreach($request->products as $product)
            {
                $campaign->products()->attach($product['id']);
            }
        }

        if($result)
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
    public function destroy(Campaign $campaign)
    {
        Storage::disk('public')->delete($campaign->banner);
        if($campaign->delete()){
                return response()->json([
                'status' => 'ok'
            ]);
        }
    }
}
