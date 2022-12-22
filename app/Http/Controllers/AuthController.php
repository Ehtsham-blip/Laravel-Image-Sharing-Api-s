<?php

namespace App\Http\Controllers;


use App\Http\Requests\ForgotRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateRequest;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\VerifyToken;

class AuthController extends Controller
{
    public function register(SignupRequest $request){
       
        if ($request->hasFile('image')) {
        $image= $request->File("profile_photo")->store('Images');
        }
        User::create([
            'name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'password'=>Hash::make($request->input('password')),
            'image'=>$image,
            'age'=>$request->input('age'),
        ]);
        $token = Str::random(64);
		$user->user_token()->create(['token' => $token]);
		Mail::send('emailVerificationEmail', ['token' => $token],
			function ($message) use ($request) {
				$message->to($request->email);
				$message->subject('Email Verification Mail');
			});
        return response()->json([
            "message"=>"User Registered Successfully, Please verify you email."
        ]);
    }

    public function emailVerify(Request $request)
	{
		
		$verifyUser = UserVerify::where('token', $token)->first();
		if (!is_null($verifyUser)) {
	
			if ($verifyUser->is_email_verified) {
				$verifyUser->is_email_verified = 1;
				$verifyUser->email_verified_at = "true";
				$verifyUser->save();
				$message = "Your e-mail is verified";
			} else {
				$message = "Your e-mail is already verified";
			}
            return response()->json([
                "message"=>$message
            ]);
		}
	}

    public function login(LoginRequest $request){
        $user=User::where('email',$request->email)->first();
        if ($user) {
            if(Hash::check($request->password,$user->password)){
                $token = Str::random(10); 
                $get=VerifyTokens::create([
                    'token' => $token,
                    'user_id' => $user->id 
                 ]); 
                 $message=$user.$get;
                return response()->json([
                    'status'=>'true','message'=>'Login Successfully','data'=>$user 
                ]);
            }
            else {
                return response()->json([
                    'status'=>'false','message'=>'Invalid Password','data'=>[]
                ]);
            }
        }
        else {
            return response()->json([
                'status'=>'false','message'=>"Email does not exist",'data'=>[]
            ]);
        }  
    }

    public function viewProfile(Request $request){
        $user_id=$request->user()->id;
        $user=User::find($user_id);
        return response()->json([
            'status'=>'true',
            'message'=>"User Profile",
            'data'=>$user
        ]);
    }

    public function forgotPassword(ForgotRequest $request){
        $check = User::where('email', $request->email)->exists();
        if($check){
            $code = random_int(100000, 999999);   //unique 6 digit code 
            DB::table('reset_passwords')->insert([  //inserting data in table
                'email' => $request->email,
                'code' => $code,
                'created_at' => Carbon::now()
            ]);
              $data['email'] = $request->email;
              $data['code'] = $code;
              $data['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
              $data['subject'] = 'Reset E-mail Code';   //mail subject
            Mail::send('forgotPasswordMail',['data' => $data], function($message) use($data){
                $message->to($data['email'])->subject($data['subject']);
            });  
         return $this->sendResponse(true, 'Code sended successfully');
        }
        else {
            return $this->sendResponse(false, 'Email does not exists.',[]);
        }
    }

    public function verifyPin(Request $request){
        $user = DB::table('reset_passwords')->where([
            ['email', $request->all()['email']],
            ['code', $request->all()['code']],
        ]);
        if($user->exists()){
            $difference = Carbon::now()->diffInSeconds($user->first()->created_at);
            if ($difference > 60) {
            return  $this->sendResponse(['success' => false, 'message' => 'Code Expired'], 400);
            }
            $user = DB::table('reset_passwords')->where([
                ['email', $request->all()['email']],
                ['code', $request->all()['code']],
            ])->delete();
            return  $this->sendResponse(['success'=>true,'message'=>'You can reset your Passsword'], 400);
        }
        else{
            return  $this->sendResponse(['success' => false, 'message' => 'Invalid Code'], 400);
        }
    }

    public function resetPassword(ResetRequest $request){
        $user = User::where('email',$request->email);
        $user->update([
            'password'=>Hash::make($request->password)
        ]);
        return $this->sendResponse(true,"Your password has been reset",);
    }

    public function updateProfile(UpdateRequest $request){
        $user=VerifyToken::where('token',$request->token)->first->user();
        $user=User::find($request->user()->id);
        $user->name=$request->input('name');
        $user->email=$request->input('email');
        $user->password=Hash::make($request->input('name'));
        $user->age=$request->input('age');
        if ($reuest->image=$request->hasFile('image')) {
        $image= $request->File("profile_photo")->store('Images');
        }
        $user->update();
        return response()->json([
            'status'=>'true',
            'message'=>"Profile Updated Successfully",
            'data'=>$user
        ]);
    }

    public function logout(Request $reuest){
       $user=VerifyToken::where('token',$request->token);
       $user->delete();
       return response()->json([
        'status'=>'true',
        'message'=>"User Logout Successfully"
       ]);
    }

   
}
