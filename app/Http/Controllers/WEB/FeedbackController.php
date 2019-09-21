<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Contact;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Contact::orderby('created_at','DESC')->paginate(3);

        return view('feedback.index')->with('feedbacks', $feedbacks);
    }
}
