<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Subscribers;
use App\Mail\VerifySubscriberEmail;

class SubscriberController extends Controller
{
    public function index(Request $request): JsonResponse {
        $request->validate([
            'email' => ['required','email','unique:'.Subscribers::class,'max:255'],
        ]);

        $token = hash('sha256', $request->ip() . time());
        $subscriber = new Subscribers();
        $subscriber->email = $request->email;
        $subscriber->token = $token;
        $subscriber->status = 'Pending';

        if(!$subscriber->save()) {
            response()->json(['status' => 'Something went wrong, please try again.'], 500);
        }

        // Send email
        $subject = 'Please Comfirm Subscription';
        $verification_link = url('subscriber/verify/'.$token.'/'.$request->email);
        \Mail::to($request->email)->send(new VerifySubscriberEmail($subject, $verification_link));

        return response()->json(['status' => 'Thanks, please check your inbox to confirm subscription']);
    }

    public function verify($token, $email)
    {
        $subscriber_data = Subscribers::where('token', $token)->where('email', $email)->first();
        $redirect_url = env('FRONTEND_URL', 'http://localhost:3000');
        if($subscriber_data)
        {
            $subscriber_data->token = '';
            $subscriber_data->status = 'Active';
            $subscriber_data->update();

            return redirect("${redirect_url}/?verify=ok");
        } 
        else 
        {
            return redirect("${redirect_url}/?verify=error");
        }
    }
}
