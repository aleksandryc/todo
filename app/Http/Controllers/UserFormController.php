<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserFormController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->all();

        Mail::raw(print_r($data, true), function($message) use ($data) {
            $message->to($data['email'])->subject('New form');
        });
        return redirect('/form_module')->with('message', 'Form was sent');
    }
}
