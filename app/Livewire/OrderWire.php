<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderWire extends Component
{
    public $tellbot_services;
    public $get_rate2;
    public $margin2;
    public $price;
    public $cost;
    public $service;
    public function render()
    {
        return view('livewire.order-wire');
    }

    public function mount()
    {
        $this->tellbot_services = get_tellbot_service();
        $this->get_rate2 = Setting::where('id', 1)->first()->rate_2;
        $this->margin2 = Setting::where('id', 1)->first()->margin_2;

    }

    public function tellabot_order_now($service, $price, $cost)
    {

        // dd($service, $price, $cost);

        if (Auth::user()->wallet < $cost) {
            session()->flash('error', "Insufficient Funds");
            return false;
        }


        User::where('id', Auth::id())->decrement('wallet', $cost);

        $service = $service;
        $price = $price;
        $cost = $cost;

        $order = create_tellbot_order($service, $price, $cost);


        //dd($order);

        if ($order == 9) {

            $ver = Verification::where('status', 1)->first() ?? null;
            if($ver != null){

                $data['sms_order'] = $ver;
                $data['order'] = 1;

                return view('receivesms', $data);

            }
            return redirect('home');
        }

        if ($order == 0) {
            User::where('id', Auth::id())->increment('wallet', $cost);
            session()->flash('error', 'Number Currently out of stock, Please check back later');
        }

        if ($order == 0) {
            User::where('id', Auth::id())->increment('wallet', $cost);
            $message = "TWBNUMBER | Low balance";
            send_notification($message);
            

            session()->flash('error', 'Error occurred, Please try again');
        }

        if ($order == 0) {
            User::where('id', Auth::id())->increment('wallet', $cost);
            $message = "TWBNUMBER | Error";
            send_notification($message);
            

            session()->flash('error', 'Error occurred, Please try again');
        }

        if ($order == 1) {

            session()->flash('success', 'Order Placed Successfully');
        }
    }
}
