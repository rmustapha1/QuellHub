<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\HostelsController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

//use App\Http\Controllers\Api\Hostels;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'email' => 'string|unique:users,email',
            'username' => 'string|unique:users,username',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([
            'username' => $request->username,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile_number' => $request->mobile_number,
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role' => $request->role,
        ]) ;

        $token = $user->createToken('myapptoken')->plainTextToken;
        $response = [
            'data' => $user,
            'token' => $token,
        ];
        return response($response, 201);
    }


    #Login
    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if (auth()->attempt(array($fieldType => $input['username'], 'password' => $input['password']))) {
            $user = Auth::user();
            $token = $user->createToken('myapptoken')->plainTextToken;
            $response = [
                "data" => $user,
                "token" => $token
            ];

            $response["bookings"] = $this->getUserBookings($user->id);

            return response($response);
        } else {
            return response("Invalid Login Credentials");
        }

    }

    public function resetPassword(Request $request){
        $validator = Validator::make( $request->all(),[
            'old_password' => 'required',
            'new_password' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }


     $old_pwd = $request->input('old_password');
     $new_pwd = bcrypt($request->input('new_password'));
     $user_id = $request->input('user_id');
     $data = [];

       $updatePwd =  DB::table('users')
            ->where('id', $user_id)
            ->update(['password' => $new_pwd]);

       if($updatePwd){
           $data['success'] = true;
           $data['status']  = 'success';
           $data['message'] = 'Password Updated Succesfully';

       } else{
           $data['success'] = false;
           $data['status']  = 'error';
           $data['message'] = 'Error Updating Password';
       }

     return json_encode($data);
    }

    #getting user bookings history
    public function getUserBookings($id){

        $data = Booking::with('hostels','rooms')->where(['user_id' => $id, 'status' => 1])->get();
        return $data;
    }

    public function profileUpdate(Request $request){
        $validator = Validator::make( $request->all(),[
            'username' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required',
            'image' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        print_r($request->file('image'));
        exit;

        $username = $request->input('username');
        $first_name = $request->input('first_name');
        $last_name = $request->input('last_name');
        $mobile_number = $request->input('mobile_number');
        $email = $request->input('email');
        $user_id = $request->input('user_id');
        $imageName = time().'.'.$request->file('image')->extension();
        $request->file('image')->storeAs('images', $imageName);

        $updatedProfile =  DB::table('users')
            ->where('id', $user_id)
            ->update([
                'username' => $username,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'mobile_number' => $mobile_number,
                'email' => $email,
                'img_url' => $imageName
                ]);

        if($updatedProfile){
            $data['success'] = true;
            $data['status']  = 'success';
            $data['message'] = 'Profile Updated Succesfully';

            $user = DB::table('users')->find($user_id);
            $user_bookings = Booking::with('hostels','rooms')->where(['user_id' => $user_id, 'status' => 1])->get();

            $data['user_info']['data'] = $user;
            $data['user_info']['bookings'] = $user_bookings[0];

        } else{
            $data['success'] = false;
            $data['status']  = 'error';
            $data['message'] = 'Error Updating Profile';
        }

        return $data;

    }


}
