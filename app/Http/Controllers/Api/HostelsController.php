<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Hostel;
use App\Models\School;
use App\Models\ApiAuth;
use App\Models\Booking;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
//use Illuminate\Support\Carbon;

class HostelsController extends Controller
{

    public function hostels()
    {
        $data = Hostel::with('rooms')->get();

        return $data;
    }

    public function getSingleHostel(Request $request, $id)
    {
        /*$validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }*/
        $data = Hostel::with('rooms')->find($id);
        return $data;
    }

    public function getSchools()
    {
        $data = School::with('hostels')->get();
        return $data;
    }

    public function getSingleSchool($id)
    {
        $data = School::with('hostels')->find($id);
        return $data;
    }

    public function bookHostel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'hostel' => 'required',
            'room_no' => 'required',
            'room_type' => 'required',
            'amount' => 'required',
            'room_id' => 'required',
            'hostel_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $data = [];


        $fname = $request->input('first_name');
        $lname = $request->input('last_name');
        $phone = $request->input('phone_number');
        $email = $request->input('email');
        $hostel = $request->input('hostel');
        $room_no = $request->input('room_no');
        $room_type = $request->input('room_type');
        $amt = $request->input('amount');
        $room_id = $request->input('room_id');
        $hostel_id = $request->input('hostel_id');
        $user_id = $request->input('user_id');
        $today = date("Y-m-d h:i:s a", time());
            //Carbon\Carbon::now();

        $checkIfBooked = DB::table('bookings')->where(['user_id' => $user_id, 'status' => 1])->get();


        if(sizeof($checkIfBooked) > 0){
            $data['status'] = 'Error';
            $data['success'] = false;
            $data['message'] = 'You\'ve Already Booked a Hostel';
            return $data;
        }


        $getRedirectUrl = json_decode($this->paystackCheckoutUrl($email, $amt));

        if ($getRedirectUrl->status == 1) {
            $booking_data = [
                'user_id' => $user_id,
                'hostel_id' => $hostel_id,
                'room_id' => $room_id,
                'amount' => $amt,
                'access_code' => $getRedirectUrl->data->access_code,
                'reference' => $getRedirectUrl->data->reference,
                'status' => 0,
                'expiry_date' => $today
                    //date('d F Y', strtotime($today->toDateTimeString(). " +1 year") )
            ];

            $saveBooking = DB::table('bookings')->insert($booking_data);

            if ($saveBooking) {
                DB::table('rooms')
                    ->where('id', $room_id)
                    ->update(['vacant' => 0]);
            }
            $user_bookings = Booking::with('hostels','rooms')->where(['user_id' => $user_id, 'status' => 1])->get();


            $getRedirectUrl->bookings = sizeof($user_bookings) > 0 ? $user_bookings[0] : null;

            return $getRedirectUrl;
        }

        return json_encode($data);
    }

    public function paystackCheckoutUrl($email, $amount)
    {
        //paystack implementation
        $url = "https://api.paystack.co/transaction/initialize";

        $fields = [
            'email' => $email,
            'amount' => $amount * 100
        ];

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer sk_test_9548bc54fd9666e0255186185101f22faad907eb",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        return $result;
    }

    public function paystackCallbackUrl(Request $request)
    {

        // echo $response;
        $reference = $request->input('reference');

        $payment_info = json_decode($this->paystackVerifyTransaction($reference));

        if ($payment_info->data->status == 'success') {

            $payments_data = [
                'reference' => $payment_info->data->reference,
                'mode' => $payment_info->data->channel,
                'amount' => $payment_info->data->amount / 100,
            ];

            $savePayment = DB::table('payments')->insert($payments_data);

            // echo json_encode($payments_data);
            if ($savePayment) {
                DB::table('bookings')
                    ->where('reference', $payment_info->data->reference)
                    ->update(['status' => 1]);
                return Redirect::to('https://privatehostels.yomodev.net/api/payment-success?message=success');
            }

        }

    }

    public function paystackVerifyTransaction($reference)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer sk_test_9548bc54fd9666e0255186185101f22faad907eb",
                "Cache-Control: no-cache",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return $response;
    }

    public function validateBooking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $data = [];

        $reference = $request->input('reference');

        $paid = json_decode($this->paystackVerifyTransaction($reference));


        if ($paid->data->status == 'abandoned') {
            $room_id = Booking::select('room_id')->where('reference', $reference)->get();
            $room_id = $room_id[0]['room_id'];

            $updateRoom = DB::table('rooms')
                ->where('id', $room_id)
                ->update(['vacant' => 1]);

            if ($updateRoom) {
                $data['status'] = 'success';
                $data['message'] = 'Room set to vacant';


            } else {
                $data['status'] = 'error';
                $data['message'] = 'Couldnt not set room to vacant 1';
            }
        } else {
            $data['status'] = 'error';
            $data['message'] = 'Couldnt not set room to vacant 2';
        }
        return json_encode($data);
    }

    public function paymentSuccess()
    {
        //return view('/content/pages/page-payment-success');
        return '';
    }

    public function sendOTP($receipient, $message)
    {
        $baseUrl = 'https://api.helliomessaging.com/v3/otp/send';

        $username = 'yahuza96';
        $password = 'I$TtHt@Admin57';
        $senderId = 'PRVT-HOSTEL'; // e.g HellioSMS (Max character for SenderId is 11 including space);
        $mobile_number = $receipient; // e.g 233242813656;
        $tokenlength = 4; //By default OTP lenght is 5;
        $recipient_email = 'info@yomodev.net'; // The OTP Recipient email address ;
        $messageType = 0; // Specify if you want to send OTP as a Flash Message or as a Text Message. 0 = Text Message and 1 = Flash Message, Or if you're sending messages in your native language e.g. Chinese, Arabic or languages that contains special characters, go with the following: 4 = Chinese or Arabic contents and 2 for other that contains special characters. ;
        $timeout = 10; // The duration you wish to expire the generated OTP;
        $message = $message; // This field is optional;

        $params = array(
            'username' => $username,
            'password' => $password,
            'senderId' => $senderId,
            'mobile_number' => $mobile_number,
            'tokenlength' => $tokenlength,
            'timeout' => $timeout,
            'recipient_email' => $recipient_email,
            'messageType' => $messageType,
            'message' => $message
        );

        // Send through CURL
        $ch = curl_init($baseUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Process your response here
        $result = curl_exec($ch);
        //echo var_export($result, true)
        curl_close($ch);
        return json_decode($result);

    }

    public function verifyNumberOTP($receipient, $otp)
    {
        $baseUrl = 'https://api.helliomessaging.com/v3/otp/verify?';

        $username = 'yahuza96';
        $password = 'I$TtHt@Admin57';
        $senderId = 'PRVT-HOSTEL';
        $mobile_number = $receipient; //Pass the mobile number to which the OTP was sent to;
        $otpCode = $otp; //Get the otp Received by the user here to validate it;

        $params = array(
            'username' => $username,
            'password' => $password,
            'mobile_number' => $mobile_number,
            'otp' => $otpCode
        );

// Send through CURL
        $ch = curl_init($baseUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Process your response here
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);

    }

    public function verifyNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $mobile_number = $request->input('mobile_number');
        // = $request->input('user_id');

        $verifyNumber = $room_id = User::select('mobile_number')->where('mobile_number', $mobile_number)->get();
        $user_id = User::select('id')->where('mobile_number', $mobile_number)->get();

        //print_r($verifyNumber);
        if (sizeof($verifyNumber) > 0) {
           $otp = $this->sendOTP($mobile_number, 'Test Message');
           $data['user_id'] = $user_id[0]['id'];

          // return $otp[0];


            $data = [
                'status' => $otp[0]->status,
                 'token' => $otp[0]->token,
                 'message' => $otp[0]->message,
                'user_id' => $user_id[0]->id
            ];
            //echo $data;
        } else {
            $data['status'] = "Error";
            $data['message'] = "Mobile Verification Failed";
        }
        return $data;
    }

//    public function verifyOTP(){
//        return "It Exists";
//    }

    public function confirmOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $receipient = $request->input('mobile_number');
        $otp = $request->input('otp');

       $data = $this->verifyNumberOTP($receipient, $otp);
       $data = $data[0];
       // $data = ['recipient' => $receipient, 'otp' => $otp];


        return json_encode($data);
    }

    public function getReceipt()
    {
        return view('/content/pages/page-receipt');
    }
    public function resetRooms(){
        // This is for testing purposes and not for implementation within the app
        $isRoomsReset =  DB::table('rooms')->update(['vacant' => 1]);
        $isBookingsReset =  DB::table('bookings')->update(['status' => 0]);

        $data = [];

        if($isRoomsReset || $isBookingsReset){
            $data['status'] = "success";
            $data['success'] = true;
            $data['message'] = "All Rooms and Bookings have been reset";
        } else {
            $data['status'] = "error";
            $data['success'] = false;
            $data['message'] = "All Rooms and Bookings could not be reset";
        }
        return $data;
    }

    public function addBookmark(Request $request){
        $validator = Validator::make($request->all(), [
            'hostel_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $data = [];
        $user_id = $request->input('user_id');
        $hostel_id = $request->input('hostel_id');

        $bookmark_data = [
            'user_id' => $user_id,
            'hostel_id' => $hostel_id
        ];

        $bookmark_exists = DB::table('bookmarks')->where(['user_id' => $user_id, 'hostel_id' => $hostel_id])->get();
        if(sizeof($bookmark_exists) > 0){
            $data['status'] = 'Error';
            $data['success'] = true;
            $data['message'] = 'Hostel Has Been Bookmarked Already';

            return $data;
        }

        $Bookmark = DB::table('bookmarks')->insert($bookmark_data);

        if ($Bookmark) {
            $data['status'] = 'Success';
            $data['success'] = true;
            $data['message'] = 'Hostel have been bookmarked';

        } else {
            $data['status'] = 'Error';
            $data['success'] = false;
            $data['message'] = 'Hostel could not be bookmarked';

        }

        return json_encode($data);


    }

    public function pastBookings(Request $request){
        $validator = Validator::make( $request->all(),[
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $user_id = $request->input('user_id');
        $past_bookings = Booking::with('hostels','rooms')->where(['status' => 0, 'user_id' => $user_id])->get();

        return json_encode($past_bookings);

    }

    public function getBookmarks(Request $request){
        $validator = Validator::make( $request->all(),[
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $user_id = $request->input('user_id');
        $bookmarks = Bookmark::with('hostels')->where('user_id', $user_id)->get();

        return json_encode($bookmarks);
    }

    public function deleteBookmark(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'hostel_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        $data = [];
        $user_id = $request->input('user_id');
        $hostel_id = $request->input('hostel_id');

        $deleted = DB::Table('bookmarks')->where(['user_id' => $user_id, 'hostel_id' => $hostel_id])->delete();

        if($deleted){
            $data['status'] = 'success';
            $data['success'] = true;
            $data['message'] = 'Bookmark Deleted Successfully';
        } else {
            $data['status'] = 'error';
            $data['success'] = false;
            $data['message'] = 'Error Deleting Bookmark';
        }

        return json_encode($data);
    }

}
