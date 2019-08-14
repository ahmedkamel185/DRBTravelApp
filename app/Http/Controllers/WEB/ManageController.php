<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Manages;
class ManageController extends Controller
{
    public function index()
    {
        return view('manage.index')->with('manages',Manages::first());
    }

    public function editTerms(Request $request)
    {
        $term = Manages::find($request->id);

        return view('manage.terms')->with('terms',$term);
    }

    public function updateTerms(Request $request)
    {
        $validator = $this->validate($request,[
           'terms'   => 'required'
        ]);
        $term = Manages::find($request->id);
        $term->terms = $request['terms'];
        $term->save();
        session()->flash('success','You have successfully edited the content. ');
        return redirect()->route('manage.index');


    }



    public function editContacts(Request $request)
    {
        $contact = Manages::find($request->id);

        return view('manage.contact')->with('terms',$contact);
    }

    public function updateContacts(Request $request)
    {
        $validator = $this->validate($request,[
            'contact_us'   => 'required'
        ]);
        $contact = Manages::find($request->id);
        $contact->contact_us = $request['contact_us'];
        $contact->save();
        session()->flash('success','You have successfully edited the content. ');
        return redirect()->route('manage.index');


    }


    public function editAbout(Request $request)
    {
        $about = Manages::find($request->id);

        return view('manage.about')->with('terms',$about);
    }

    public function updateAbout(Request $request)
    {
        $validator = $this->validate($request,[
            'about_us'   => 'required'
        ]);
        $about = Manages::find($request->id);
        $about->about_us = $request['about_us'];
        $about->save();
        session()->flash('success','You have successfully edited the content. ');
        return redirect()->route('manage.index');


    }




}
