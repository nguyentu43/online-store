<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Http\Requests\UserRequest;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Role;
use App\PasswordReset;
use App\Notifications\UserPasswordReset;
use Socialite;
use App\LoginProvider;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword as ResetPasswordMail;
use App\Mail\Welcome as WelcomeMail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('can:create,App\User')->only('store');
        $this->middleware('can:update,user')->only('update');
        $this->middleware('can:delete,user')->only('destroy');
        $this->middleware('can:view,App\User')->only('index');
    }

    public function index(Request $request)
    {
        $users = User::query();
        
        $filter = $request->get('filter', '');

        if($request->type == 'customer')
        {
            $users = $users->whereHas('roles', function($query){
                $query->where('name', 'Khách hàng');
            });
        }

        $users = $users->where(function($query){
            global $filter;
            $query->where('users.email', 'like', '%'.$filter.'%')
                  ->orWhere('users.name', 'like', '%'.$filter.'%');
        });

        return UserResource::collection($users->get());
    }

    public function store(CreateUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        if($user)
        {
            $user->roles()->sync($data['role_id']);
            $user->login_providers()->attach(LoginProvider::where('name', 'email')->first()->id);
            $user->cart()->create([]);
            
            return response()->json([
                'status' => 'ok'
            ]);
        }
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function getUserCurrent()
    {
        $user = request()->user();

        if($user->enable)
        {
            return new UserResource($user);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Tài khoản đã bị khoá' 
        ], 401);
    }

    public function update(Request $request, User $user)
    {

        $user->update($request->all());

        if($request->role_id)
            $user->roles()->sync($request->role_id);

        return response()->json([
            'status' => 'ok'
        ]);


    }

    public function updateUserCurrent(Request $request)
    {

        $user = request()->user();

        Validator::make($request->only('name', 'password'), 
        [
            'name' => 'required'
        ]
        )->validate();

        if($request->input('password'))
        {
            $password = DB::table('users')->where('id', $user->id)->get()->first()->password;

            if(!Hash::check($request->input('oldPassword'), $password))
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Mật khẩu cũ không chính xác, thông tin chưa được thay đổi'
                ], 401);
            }

            $user->password = Hash::make($request->input('password'));
            $user->save();
        }

        if($user->update($request->all()))
            return response()->json([
                'status' => 'ok'
            ]);
    }

    public function destroy(User $user)
    {
        if($user->delete())
        {
            return response()->json([
                'status' => 'ok'
            ]);
        }
    }

    public function login(Request $request)
    {

        Validator::make($request->only('email', 'password'), 
        [
            'email' => 'required|email',
            'password' => 'required'
        ]
        )->validate();

        $user = User::where('email', $request->email)->whereHas('login_providers', function ($query){
            $query->where('name', 'email');
        })->first();

        if($user)
        {
            if(!$user->enable)
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tài khoản đã bị khoá' 
                ], 401);
            }

            if(Auth::attempt($request->only('email', 'password')))
            {
                if(empty($token))
                    $token = $user->createToken(env('APP_NAME'))->accessToken;

                return response()->json([
                    'user' => new UserResource($user),
                    'token' => $token
                ]);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Địa chỉ email, mật khẩu không đúng' 
        ], 401);
    }

    public function register(UserRequest $request)
    {
        $data = $request->validated();

        $check_email = User::whereHas('login_providers', function($query){
            $query->where('name', 'email');
            })->where('email', $request->email)->first();

        if($check_email)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'The email has already been taken.'
            ], 422);
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::has('login_providers')
                ->where('email', $request->email)
                ->first();

        if(!$user)
        {
            $user = User::create($data);
            $user->enable = true;
            $user->save();
            $user->roles()->attach(Role::where('name', 'Khách hàng')->first()->id);
            Mail::to($user)->queue(new WelcomeMail($user));
        }
        else
        {
            $user->update($data);
        }

        $user->login_providers()->attach(LoginProvider::where('name', 'email')->first()->id);
        $user->cart()->create([]);

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken(env('APP_NAME'))->accessToken
        ]);
    }

    public function requestResetPassword(Request $request)
    {
        $email = $request->email;
        $user = User::whereHas('login_providers', function($query){
            $query->where('name', 'email');
        })->where('email', $email)->first();

        if($user)
        {
            if($user->password_reset)
                $user->password_reset->delete();
            $token = str_random(60);
            
            Mail::to($user)->queue(new ResetPasswordMail($token));

            $user->password_reset()->create(compact('token'));
            return response()->json([
                'status' => 'ok'
            ]);
        }
        else
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Địa chỉ email không tồn tại'
            ], 404);
        }
    }

    public function resetPassword(Request $request)
    {
        $token = $request->token;
        $password_reset = PasswordReset::where('token', $token)->first();
        if($password_reset)
        {
            $user = $password_reset->user;
            $user->password = Hash::make($request->password);
            $user->save();

            $password_reset->delete();

            return response()->json([
                'status' => 'ok'
            ]);
        }
        else
        {
            return response()->json([
                'status' => 'error',
                'message' => 'Mã token không tồn tại'
            ], 404);
        }
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
    }

    public function handleLoginCallback($provider)
    {
        try
        {
            $data = Socialite::driver($provider)->stateless()->user();
        }
        catch(\GuzzleHttp\Exception\ClientException $e)
        {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }

        $role = Role::where('name', 'Khách hàng')->first();
        $data = [
            'name' => $data->getName(),
            'email' => $data->getEmail(),
            'role_id' => $role->id,
            'enable' => true
        ];

        $user = User::with('role')->where('email', $data['email'])->first();

        if(!$user)
        {
            $user = User::create($data);
            $user->roles()->sync($data['role_id']);
            Mail::to($user)->queue(new WelcomeMail($user));
            $user->cart()->create([]);
        }

        if(!User::whereHas('login_providers', function($query){
            global $provider;
            $query->where('name', $provider);
        })->where('email', $data['email'])->first())
        {
            $user->login_providers()->attach(LoginProvider::where('name', $provider)->first()->id);
        }

        $user = new UserResource($user);

        if(!$user->enable)
            return 'Tài khoản đã bị khoá'; 

        $token = $user->createToken(env('APP_NAME'))->accessToken;

        return view('login_provider', compact('user', 'token'));
    }

    public function checkEmail(Request $request)
    {
        $email = $request->get('value');
        $user = User::where('email', $email)->whereHas('login_providers', function($query){
                $query->where('name', 'email');
        })->first();
        if($user)
            return response()->json(['match' => 1]);
        else
            return response()->json(['match' => 0]);
    }
}
