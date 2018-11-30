<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\AddressBook;
use App\Http\Resources\AddressBook as AddressBookResource;
use Illuminate\Support\Facades\Validator;

class AddressBookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:update,user');
    }

    public function index(Request $request)
    {
        return AddressBookResource::collection($request->user('api')->addressbooks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = $request->user('api');
        Validator::make($request->only('name', 'address', 'city', 'phone'), 
            [
                'name' => 'required||min:6|max:25',
                'address' => 'required',
                'city' => 'required',
                'phone' => 'required'
            ]
        )->validate();
        if($user->address_books()->create($request->all()))
            return response()->json(['status' => 'ok']);
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
    public function update(Request $request, AddressBook $addressbook)
    {

        Validator::make($request->all(), 
        [
            'name' => 'required|min:6|max:25',
            'address' => 'required',
            'city' => 'required',
            'phone' => 'required'
        ]
        )->validate();

        if($addressbook->update($request->all()))
            return response()->json(['status' => 'ok']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AddressBook $addressbook)
    {

        if($addressbook->delete())
            return response()->json(['status' => 'ok']);  
    }
}
