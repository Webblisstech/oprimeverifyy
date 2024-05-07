<?php

namespace App\Livewire;

use App\Models\Verification;
use Livewire\Component;

class Message extends Component
{
    public $message = '';
    public $mdn;
    public function render()
    {
        return view('livewire.message');
    }



    public function  get_tella_smscode()
    {
        $sms =  Verification::where('phone', $this->mdn)->first()->sms ?? null;
        $phone =  Verification::where('phone', $this->mdn)->first()->phone ?? null;
        $order_id =  Verification::where('phone', $this->mdn)->first()->order_id ?? null;
        check_tella_sms($phone);


        $originalString = 'waiting for sms';
        $processedString = str_replace('"', '', $originalString);


        if ($sms == null) {
            $this->message = '';
        } else {
            $this->message = $sms;
        }
    }
}
