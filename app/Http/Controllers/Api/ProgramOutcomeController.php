<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

use App\ProgramOutcome;
use App\Imports\ProgramOutcomeImport;

use App\Log;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Validator;

class ProgramOutcomeController extends ApiController
{
  public function uploadedFile(Request $request)
  {
    $import = new ProgramOutcomeImport();
    $import->import($request->fileUrl);

    return $this->apiResponse(ResaultType::Error, $import->err, 'hatalar', 403);
  }

    public function index(Request $request)
    {
      $user = User::find(Auth::id());
      $offset = $request->offset ? $request->offset : 0;
      $limit = $request->limit ? $request->limit : 99999999999999;
      $query = ProgramOutcome::query();
      switch ($user->level) {
        case 3:
          $query->join('department','department.id','=','program_outcome.department_id');

          $query->where('department.faculty','=',$user->faculty_id);

          $query->select('program_outcome.*');
        break;
  			case 4:
          $query->where('program_outcome.department_id', '=', $user->department_id);

          $query->select('program_outcome.*');
        break;
        case 5:
          $query->where('program_outcome.department_id','=',$user->department_id);

          $query->select('program_outcome.*');
        break;
        case 6:
          $query->where('program_outcome.department_id','=',$user->department_id);

          $query->select('program_outcome.*');
        break;
        default:
          $query->select('program_outcome.*');
        break;
      }

      if ($request->has('code'))
        $query->where('code', '=', $request->query('code'));
      if ($request->has('department'))
        $query->where('department_id', '=', $request->query('department'));

      $length = count($query->get());
      $data = $query->offset($offset)->limit($limit)->get();
      if ($data) {
        return $this->apiResponse(ResaultType::Success, $data, 'Listing: '.$offset.'-'.$limit, $length, 200);
      } else {
        return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcome Not Found', 0, 404);
      }
    }

    public function store(Request $request)
    {
			$user = User::find(Auth::id()); // oturum açan kişinin bilgilerini buradan alıyoruz.
			switch ($user->level) {
				case 1:
					$validator = Validator::make($request->all(), [
            'explanation' => 'required',
            'code' => 'required',
            'department_id' => 'required',
						'year_and_term' => 'required',
          ]);
					if ($validator->fails()) {
							return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
					}
					$data = new ProgramOutcome();
					$data->explanation = request('explanation');
					$data->code = request('code');
					$data->department_id = request('department_id');
					$data->year_and_term = request('year_and_term');
					$data->save();
					if ($data) {
							$log = new Log();
							$log->area = 'ProgramOutcome';
							$log->areaid = $data->id;
							$log->user = Auth::id();
							$log->ip = \Request::ip();
							$log->type = 1;
							$log->info = 'ProgramOutcome '.$data->id.' Created for the University '.$data->university;
							$log->save();
							return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcome Created', 201);
					} else {
							return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcome not saved', 500);
					}
					break;
					default:
						return $this->apiResponse(ResaultType::Error, 403, 'Authorization Error', 0, 403);
					break;
				}
    }


    public function show($id)
    {
        $data = ProgramOutcome::find($id);
        if ($data) {
            return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcome Detail', 201);
        } else {
            return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcome Not Found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'explanation' => 'nullable',
            'code' => 'nullable',
            'department_id' => 'nullable',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse(ResaultType::Error, $validator->errors(), 'Validation Error', 422);
        }
        $data = ProgramOutcome::find($id);

        if ($data) {
            if (request('explanation') != '') {
                $data->explanation = request('explanation');
            }
            if (request('code') != '') {
                $data->code = request('code');
            }
            if (request('department_id') != '') {
                $data->department_id = request('department_id');
            }
            $data->save();

            if ($data) {
                $log = new Log();
                $log->area = 'ProgramOutcome';
                $log->areaid = $data->id;
                $log->user = Auth::id();
                $log->ip = \Request::ip();
                $log->type = 2;
                $log->info = 'ProgramOutcome '.$data->id;
                $log->save();

                return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcome Updated', 200);
            } else {
                return $this->apiResponse(ResaultType::Error, null, 'ProgramOutcome not updated', 500);
            }
        } else {
            return $this->apiResponse(ResaultType::Warning, null, 'Data not found', 404);
        }
    }

    public function destroy($id)
    {
        $data = ProgramOutcome::find($id);
        if ($data) {
            $data->delete();
            return $this->apiResponse(ResaultType::Success, $data, 'ProgramOutcome Deleted', 200);
        } else {
            return $this->apiResponse(ResaultType::Error, $data, 'Deleted Error', 500);
        }
    }
}

