<?php

namespace App\Http\Controllers\Backend\Import\Account;

use App\Http\Controllers\Controller;
use App\Imports\AccountsImport;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use Yajra\DataTables\Facades\DataTables;

class AccountController extends Controller
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
        $sl_no_filter = Account::select('sl_no')->groupBy('sl_no')->get();
        if ($request->ajax()) {
            $data = Account::get();
            return DataTables::of($data)->addIndexColumn()
                // ->removeColumn('id')
                // ->addColumn('action', function($row){
                //     $btn = '<a href="javascript:void(0)" class="btn btn-primary btn-sm">View</a>';
                //     return $btn;
                // })
                // ->rawColumns(['action'])
                ->make(true);
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        // dd($last_query);
        return view('backend.import.account.index', compact('sl_no_filter'));
    }

     /**
     * Display a listing of the resource.
     */
    public function getAccounts(Request $request)
    {

        $records = Account::query();
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // total number of rows per page

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
        // $totalRecordswithFilter = Account::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();
        // dd($totalRecords, $totalRecordswithFilter);

        
        if (!empty($searchValue)) {
            $filter = ['sl_no', 'ref_no', 'date', 'project', 'amount'];
            $records->where(function ($bids) use ($filter, $searchValue) {
                foreach ($filter as $key => $column) {
                    dd($column, $searchValue);
                    if ($key === 0) {
                        $bids->where($column, 'like', '%' . $searchValue . '%');
                    } else {
                        $bids->orWhere($column, 'like', '%' . $searchValue . '%');
                    }
                }
            });
        }

        // Get records, also we have included search filter as well
        $records = Account::orderBy($columnName, $columnSortOrder)
            ->select('id', 'sl_no', 'ref_no', 'date', 'project', 'amount')
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        

        $data_arr = array();

        
        $data_arr = array();
            if ($request->ajax()) {
                foreach ($records as $index => $record) {
                    $attributes = $record->getAttributes();
                    $attributes = array_map(function($value) {
                        return $value === null ? 'N/A' : $value; // Replace null values
                    }, $attributes);
                    $attributes['DT_RowIndex'] = $index + 1; // Add DT_RowIndex manually
                    /* if($attributes['show_front_status'] == 0){
                        $status = '1';
                        $checked = '';
                    }else{
                        $status = '0';
                        $checked = 'checked';
                    }; */
                    // $attributes['show_front_status'] =  '<label class="switch"><input type="checkbox" onchange="showFrontStatus(`'.$attributes["bid"].'`,`'.$status.'`);" data-id="' . $attributes["bid"] . '" data-status="' . $attributes["show_front_status"] . '" '.$checked.'><span class="slider round"></span></label>';
                    // $deleteUrl = route('admin.bids.destroy', $attributes['bid']);
                    $actionBtn = '<a href="javascript:;" data-id="' . $attributes['id'] . '" class="badge black winbid" lang="' . $attributes['id'] . '">Win</a> /
                            <a href="javascript:;" data-id="' . $attributes['id'] . '" class="badge black soldbid soldoverbid" lang="' . $attributes['id'] . '">Result</a> /
                            <a href="javascript:;" data-id="' . $attributes['id'] . '" class="badge black cancelbyAuction" lang="' . $attributes['id'] . '">Cancel by Auction</a> /
                            <a href="javascript:;" data-id="' . $attributes['id'] . '" class="badge black moveToArchive" lang="' . $attributes['id'] . '">Move To Archive</a> /
                            <a href="javascript:;" data-id="' . $attributes['id'] . '" class="badge red deletebidadmin" lang="' . $attributes['id'] . '">Delete</a>';
                    if ($request->move_to_archived == 1) {
                        $actionBtn = '<a href="javascript:;" data-id="' . $attributes['id'] . '" class="badge black winbid" lang="' . $attributes['id'] . '">Win</a> /
                            <a href="javascript:;" data-id="' . $attributes['id'] . '" class="badge black soldbid soldoverbid" lang="' . $attributes['id'] . '">Over Sold</a> /
                            <a href="javascript:;" data-id="' . $attributes['id'] . '" class="badge black cancelbyAuction" lang="' . $attributes['id'] . '">Cancel by Auction</a>';
                    }
                    $attributes['action'] =  $actionBtn;
                    
                    $data_arr[] = $attributes;
                }
                $response = array(
                    "draw" => intval($draw),
                    "iTotalRecords" => $totalRecords,
                    "iTotalDisplayRecords" => $totalRecords,
                    "aaData" => $data_arr,
                );
                return $response;
            }
        /* foreach ($records as $record) {

            $data_arr[] = array(
                "id" => $record->id,
                "name" => $record->name,
                "email" => $record->email,
                "mobile" => $record->mobile,
                "branch" => $record->branch,
            );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
        ); */

        // echo json_encode($response);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('backend.import.account.import-file');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            \Excel::import(new AccountsImport, $request->import_payment);
            activity("Account Import")
            ->performedOn(new Account())
            ->causedBy(auth()->user()->id)
            ->event(__METHOD__)
            ->withProperties([])
            ->log('File Imported Successfully');

            return redirect()->back()->with('success', 'File Imported Successfully');
        } catch (\Exception $e) {
            Log::info($e);
            activity("Account Import")
            ->performedOn(new Account())
            ->causedBy(auth()->user()->id)
            ->event(__METHOD__)
            ->withProperties($e)
            ->log($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage() ?? 'Internal server error while import file');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        //
    }
    /**
     * Import payment file.
     */
    public function import(Request $request)
    {
        //
        
    }
    /**
     * Import payment file.
     */
    public function importStore(Request $request)
    {
        //
        // Excel::import(new AccountsImport, $request->import_payment);
        // Excel::import(new AccountsImport, $request->import_payment, 'local', \Maatwebsite\Excel\Excel::XLSX);
        
        try {
            \Excel::import(new AccountsImport, $request->import_payment);
            activity("Account Import")
            ->performedOn(new Account())
            ->causedBy(auth()->user()->id)
            ->event(__METHOD__)
            ->withProperties([])
            ->log('File Imported Successfully');

            return redirect()->back()->with('success', 'File Imported Successfully');
        } catch (\Exception $e) {
            Log::info($e);
            activity("Account Import")
            ->performedOn(new Account())
            ->causedBy(auth()->user()->id)
            ->event(__METHOD__)
            ->withProperties($e)
            ->log($e->getMessage());
            return redirect()->back()->with('error', $e->getMessage() ?? 'Internal server error while import file');
        }
    }
}
