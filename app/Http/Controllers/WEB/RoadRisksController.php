<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Risk;
use App\Models\RiskType;
use File;

class RoadRisksController extends Controller
{
    //
    public function index()
    {
        return view('road_risks.index')
            ->with('risks',Risk::all())
            ->with('risks_count',Risk::all()->count())

            ;
    }

    public function changeStatus(Request $request)

    {

        $risk = Risk::find($request->user_id);

        $risk->status = $request->status;

        $risk->save();



        return response()->json(['success'=>'risk change status.']);

    }

    public function listRisks()
    {
        return view('road_risks.show')
            ->with('risks_type_count',RiskType::all()->count())
            ->with('risk_type',RiskType::all())
            ;
    }

    public function addRisk()
    {
        return view('road_risks.add_risk');
    }
    public function storeRisk(Request $request)
    {
        $this->validate($request,[
           'name_ar'=> 'required|min:3|max:191',
           'name_en'=> 'required|min:3|max:191',
            'icon' =>  'nullable'
        ]);
        $risk = new RiskType;
        $risk->name_ar = $request['name_ar'];
        $risk->name_en = $request['name_en'];
        if ($request['icon']) {
            $photo = $request->icon;
            $name = date('d-m-y') . time() . rand() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('uploads/riskTypes'), $name);
            $risk->icon = $name;

        }
        $risk->save();
        session()->flash('success','risk type added success');
        return redirect()->route('road.risks');

    }

    public function editRisk(Request $request, $id)
    {
        return view('road_risks.edit')
            ->with('risk',RiskType::find($id))
            ;

    }

    public function updateRisk(Request $request,$id)
    {
        $this->validate($request,[
            'name_ar'=> 'required|min:3|max:191',
            'name_en'=> 'required|min:3|max:191',
            'icon' =>  'nullable'
        ]);
        $risk = RiskType::find($id);
        $risk->name_ar = $request['name_ar'];
        $risk->name_en = $request['name_en'];
        if ($request['icon']) {
            $photo = $request->icon;
            if ($risk->icon != 'default_image.png') {
                File::delete('uploads/publishers/' . $risk->icon);
            }

            $name = date('d-m-y') . time() . rand() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('uploads/riskTypes'), $name);
            $risk->icon = $name;

        }
        $risk->save();
        session()->flash('success','risk type Updated success');
        return redirect()->route('road.risks');

    }

    public function deleteRisk(Request $request,$id)
    {
        $risk = RiskType::find($id);
        if ($risk->icon != 'default_image.png') {
            File::delete('uploads/riskTypes/' . $risk->icon);
        }
        $risk->delete();
        session()->flash('success','risk type deleted');
        return redirect()->back();

    }



}
