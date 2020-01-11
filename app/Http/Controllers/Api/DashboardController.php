<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Company;
use App\Project;
use App\Section;
use App\Devices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Validator;
use Carbon\Carbon;

class DashboardController extends ApiController
{
    public function index(Request $request)
    {

        $companyall = Company::count();
        $companyactive = Company::where('status','=',1)->count();
        $companypassive = Company::where('status','=',0)->count();

        // $response = [];
        // $response['success'] = true;
        // $params = [];
        //     $params['company'] = $company->count();
        //     $params['companyactive'] = $company->where('status','=',1)->get()->count();
        //     $params['companypassive'] = $company->where('status','=',0)->get()->count();
        //     $params['project'] = $project->count();
        //     $params['projectactive'] = $project->where('status','=',1)->get()->count();
        //     $params['projectpassive'] = $project->where('status','=',0)->get()->count();
        //     $params['projectcomplete'] = $project->where('status','=',2)->get()->count();
        //     $params['section'] = $section->count();
        //     $params['sectionactive'] = $section->where('status','=',1)->get()->count();
        //     $params['sectionpassive'] = $section->where('status','=',0)->get()->count();
        //     $params['devices'] = $devices->count();
        //     $params['devicesactive'] = $devices->where('status','=',1)->get()->count();
        //     $params['devicespassive'] = $devices->where('status','=',0)->get()->count();
        // $response['data'] = $params;


        $response = [];
        $response['success'] = true;
        $company = [];
            $company['company'] = $companyall;
            $company['companyactive'] = $companyactive;
            $company['companypassive'] = $companypassive;
            $company['series'] = [$companyactive,$companypassive];
        $params = [];
            $params['company'] = $company;
        $response['data'] = $params;

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
