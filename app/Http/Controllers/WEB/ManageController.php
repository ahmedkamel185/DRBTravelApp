<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Contact;
class ManageController extends Controller
{
    public function index()
    {


        return view('manage.index')->with('manages',Setting::first());
    }

    public function editTerms(Request $request)
    {
        $term = Setting::find($request->id);

        return view('manage.terms')->with('terms',$term);
    }

    public function updateTerms(Request $request)
    {
        $validator = $this->validate($request,[
           'terms_ar'   => 'required',
           'terms_en'   => 'required'
        ]);
        $term = Setting::find($request->id);
        $term->terms_ar = $request['terms_ar'];
        $term->terms_en = $request['terms_en'];
        $term->save();
        session()->flash('success','You have successfully edited the content. ');
        return redirect()->route('manage.index');


    }



    public function editContacts(Request $request)
    {
        $contact = Setting::find($request->id);

        return view('manage.contact')->with('terms',$contact);
    }

    public function updateContacts(Request $request)
    {
        $validator = $this->validate($request,[
            'mobile'   => 'required',
//            'contact_us_en'   => 'required'
        ]);
        $contact = Setting::find($request->id);
        $contact->mobile = $request['mobile'];
//        $contact->contact_us_en = $request['contact_us_en'];
        $contact->save();
        session()->flash('success','You have successfully edited the content. ');
        return redirect()->route('manage.index');


    }


    public function editAbout(Request $request)
    {
        $about = Setting::find($request->id);

        return view('manage.about')->with('terms',$about);
    }

    public function updateAbout(Request $request)
    {
        $validator = $this->validate($request,[
            'about_ar'   => 'required',
            'about_en'   => 'required'
        ]);
        $about = Setting::find($request->id);
        $about->about_ar = $request['about_ar'];
        $about->about_en = $request['about_en'];
        $about->save();
        session()->flash('success','You have successfully edited the content. ');
        return redirect()->route('manage.index');


    }

    public function allMessages()
    {
        return view('manage.contacts')->with('messages',Contact::all());
    }
    public function singleMessage($id)
    {
        $message = Contact::find($id);
        $message->seen = 1;
        $message->save();
        session()->flash('success','message read');
        return redirect()->back();
    }




}
