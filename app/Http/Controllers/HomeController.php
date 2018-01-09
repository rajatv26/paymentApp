<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Redirect;
use App\Membership;
use App\Customers;
use Auth;
use DB;

class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        
        $customers = Membership::where('id','=','1')->first()->customers;
        dd($customers);
//        $users = DB::table('users')
//            ->join('stripe_customers', 'users.id', '=', 'stripe_customers.user_id')
//            ->select('users.*', 'stripe_customers.customer_id')
//            ->where('plan_id','=',1)
//            ->get();
//        dd($users);
        return view('home');
    }

    public function showform() {
        $membership = Membership::first();
        
        return view('pay', compact('membership'));
    }

    public function makepayment(Request $request) {
        $user = Auth::user();
        $membership_price = Membership::select('*')->where('id', '=', $request->input('membership_id'))->firstOrFail();

        \Stripe\Stripe::setApiKey('sk_test_KCwU7jVLIoQtECJsXAEXJg1q');
        try {

            // Creating customer in stripe
            $stripe_customer = \Stripe\Customer::create(array(
                        "email" => $user->email,
                        'source'  => $request->input('stripeToken')
            ));
            
            // Charge payment for first time
            $pay = \Stripe\Charge::create ( array (
                        'customer' => $stripe_customer->id,
                        'amount'   => $membership_price->price * 100,
                        'currency' => 'usd',
                        "description" => "Test payment." 
                        ));
                
            // Customer book keeping
            $customer = new Customers;
            $customer->customer_id = $stripe_customer->id;
            $customer->plan_id = $membership_price->id;
            $customer->created_at = $stripe_customer->created;
            $customer->user_id = $user->id;
            $customer->updated_at = $stripe_customer->created;
            $customer->save();
            
            //Subscribing Customer to the plan
             $subscription = \Stripe\Subscription::create(array(
                "customer" => $stripe_customer->id,
                "items" => array(
                    array(
                        "plan" => "daily_charge",
                    ),
                ),
            ));

            Session::flash('success-message', 'Payment done successfully !');
            return Redirect::back();
        } catch (\Exception $e) {
            dd($e);
            Session::flash('fail-message', "Error! Please Try again.");
            return Redirect::back();
        }
//        return view('pay');
    }

    public function subscribe(Request $request) {
        try {
            \Stripe\Stripe::setApiKey("sk_test_KCwU7jVLIoQtECJsXAEXJg1q");

            $subscription = \Stripe\Subscription::create(array(
                "customer" => "cus_C08xT6XSKC33tY",
                "items" => array(
                    array(
                        "plan" => "daily_charge",
                    ),
                ),
            ));
            
            dd($subscription);
        } catch (Exception $ex) {
            dd($e);
        }
    }
    
    public function listenwebhook(Request $request){
        
        return view('listenwebhook');
    }

}
