<?php

namespace App\Livewire;

use App\Models\Verification as ModelsVerification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Verification extends Component
{
    use WithPagination;
    public $mdn;
    public $message;
    public function render()
    {
        $verification = ModelsVerification::latest()->where('user_id', Auth::id())->paginate(10);
        foreach ($verification as $data) {
            $sms =  ModelsVerification::where('phone', $data->phone)->first()->sms ?? null;
            $phone =  ModelsVerification::where('phone', $data->phone)->first()->phone ?? null;
            $order_id =  ModelsVerification::where('phone', $data->phone)->first()->order_id ?? null;
            check_tella_sms($phone);


            $originalString = 'waiting for sms';
            $processedString = str_replace('"', '', $originalString);


            if ($sms == null) {
                $this->message = '';
            } else {
                $this->message = $sms;
            }
        }
        return view('livewire.verification', ['verification' => $verification]);
    }

    public function mount()
    {
    }
}
