<?php

namespace App\Http\Controllers\Backend\Activity;

use App\Http\Controllers\Controller;
use App\Imports\PaymentsImport;
use App\Models\User;
use App\Models\UserLoginHistory;
use Spatie\Activitylog\Models\Activity as ActivityModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel;
use Yajra\DataTables\Facades\DataTables;

class ActivityController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-payment|edit-payment|delete-payment', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-payment', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-payment', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-payment', ['only' => ['destroy']]);
        $this->middleware('permission:import-payment-excel', ['only' => ['import', 'importStore']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        DB::connection()->enableQueryLog();
        if ($request->ajax()) {
            $data = ActivityModel::with('causer')->orderByDesc('id')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->diffForHumans();
                })
                ->make(true);
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        // dd($last_query);
        return view('backend.activity.index');
    }
    /**
     * Display a listing of the resource.
     */
    public function loginHistory(Request $request, User $user = null)
    {
        //UserLoginHistory
        DB::connection()->enableQueryLog();
        if ($request->ajax()) {
            $data = UserLoginHistory::orderByDesc('id')->get();
            if (!is_null($user)) {
                $data = $user->userLoginHistory()->orderByDesc('id')->get();
            }
            return DataTables::of($data)
                ->addIndexColumn()
                // ->removeColumn('id')
                ->addColumn('name', function ($row) {
                    return User::find($row->user_id)->name;
                    // return \Carbon\Carbon::parse($row->created_at )->isoFormat('d-m-Y');
                })
                ->editColumn('created_at', function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->diffForHumans();
                    // return \Carbon\Carbon::parse($row->created_at )->isoFormat('d-m-Y');
                })
                ->rawColumns(['name'])
                ->make(true);
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        // dd($last_query);
        return view('backend.activity.login-history');
    }

    /**
     * Display a listing of the resource.
     */
    public function getPayments(Request $request)
    {
        $records = ActivityModel::query();
        $draw = $request->get('draw');
        $start = $request->get('start');
        $rowperpage = $request->get('length'); // total number of rows per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = $records->select('count(*) as allcount')->count();
        // $totalRecordswithFilter = ActivityModel::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();
        // dd($totalRecords, $totalRecordswithFilter);

        if (!empty($searchValue)) {
            $filter = ['sl_no', 'ref_no', 'date', 'project', 'amount'];
            $records->where(function ($bids) use ($filter, $searchValue) {
                foreach ($filter as $key => $column) {
                    if ($key === 0) {
                        $bids->where($column, 'like', '%' . $searchValue . '%');
                    } else {
                        $bids->orWhere($column, 'like', '%' . $searchValue . '%');
                    }
                }
            });
        }

        // Get records, also we have included search filter as well
        $records = ActivityModel::orderBy($columnName, $columnSortOrder)->select('id', 'sl_no', 'ref_no', 'date', 'project', 'amount')->skip($start)->take($rowperpage)->get();

        $data_arr = [];

        $data_arr = [];
        if ($request->ajax()) {
            foreach ($records as $index => $record) {
                $attributes = $record->getAttributes();
                $attributes = array_map(function ($value) {
                    return $value === null ? 'N/A' : $value; // Replace null values
                }, $attributes);
                $attributes['DT_RowIndex'] = $index + 1; // Add DT_RowIndex manually
                $editUrl = route('backend.payments.edit', $attributes['id']);
                $actionBtn = '<a href="javascript:;" data-id="' . $attributes['id'] . '" class="badge black winbid" lang="' . $attributes['id'] . '">Win</a>';

                $attributes['action'] = $actionBtn;

                $data_arr[] = $attributes;
            }
            $response = [
                'draw' => intval($draw),
                'iTotalRecords' => $totalRecords,
                'iTotalDisplayRecords' => $totalRecords,
                'aaData' => $data_arr,
            ];
            return $response;
        }
    }
}
