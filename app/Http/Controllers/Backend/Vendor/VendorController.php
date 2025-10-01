<?php

namespace App\Http\Controllers\Backend\Vendor;

use App\Http\Controllers\Controller;
use App\Imports\VendorsImport;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-vendors', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-vendors', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-vendors', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-vendors', ['only' => ['destroy']]);
        $this->middleware('permission:import-vendors-excel', ['only' => ['import', 'importStore']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //

        // ->addColumn('action', function ($row) {
        //     $btn = '';
        //     $btn .= '<a href="' . route('backend.vendors.edit', $row->id) . '" class="btn btn-outline-primary btn-xs"><i class="bi bi-pencil-square"></i></a> | <a href="' . route('backend.vendors.show', $row->id) . '" class="btn btn-outline-primary btn-xs"><i class="bi bi-eye"></i></a>';
        //     return $btn;
        // })
        $user = auth()->user();
        DB::connection()->enableQueryLog();
        // $sl_no_filter = Vendor::select('vendor_code')->where('active', 'Y')->groupBy('vendor_code')->get();
        $sl_no_filter = $user->hasRole('Admin') ? Vendor::select('vendor_code')->groupBy('vendor_code')->get() : Vendor::select('vendor_code')->where('active', 'Y')->groupBy('vendor_code')->get();
        if ($request->ajax()) {
            // $data = Vendor::where('active', 'Y')->get();
            $data = $user->hasRole('Admin') ? Vendor::all() : Vendor::where('active', 'Y')->get();
            return DataTables::of($data)
                // ->removeColumn('id')
                ->addIndexColumn()
                ->editColumn('active', function ($row) {
                    return $row->active == 'Y' ? '<span class="badge bg-success">Approved</span>' : '<span class="badge bg-warning">Pending</span>';
                })

                ->addColumn('action', function ($row) {
                    $btn = '';

                    // Agar user ke pass edit permission hai
                    if (auth()->user()->can('edit-vendors')) {
                        $btn .=
                            '<a href="' .
                            route('backend.vendors.edit', $row->id) .
                            '"
                 class="btn btn-outline-primary btn-xs">
                    <i class="bi bi-pencil-square"></i>
                 </a>';
                    }

                    // Show button sabke liye
                    $btn .=
                        ' | <a href="' .
                        route('backend.vendors.show', $row->id) .
                        '"
             class="btn btn-outline-primary btn-xs">
                <i class="bi bi-eye"></i>
             </a>';

                    return $btn;
                })
                ->rawColumns(['active', 'action'])
                ->make(true);
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        // dd($last_query);
        return view('backend.vendor.index', compact('sl_no_filter'));
    }

    /**
     * Display a listing of the resource.
     */
    public function getVendors(Request $request)
    {
        $records = Vendor::query();
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
        // $totalRecordswithFilter = Vendor::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();
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
        $records = Vendor::orderBy($columnName, $columnSortOrder)->select('id', 'sl_no', 'ref_no', 'date', 'project', 'amount')->skip($start)->take($rowperpage)->get();

        $data_arr = [];

        $data_arr = [];
        if ($request->ajax()) {
            foreach ($records as $index => $record) {
                $attributes = $record->getAttributes();
                $attributes = array_map(function ($value) {
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
                $actionBtn =
                    '<a href="javascript:;" data-id="' .
                    $attributes['id'] .
                    '" class="badge black winbid" lang="' .
                    $attributes['id'] .
                    '">Win</a> /
                            <a href="javascript:;" data-id="' .
                    $attributes['id'] .
                    '" class="badge black soldbid soldoverbid" lang="' .
                    $attributes['id'] .
                    '">Result</a> /
                            <a href="javascript:;" data-id="' .
                    $attributes['id'] .
                    '" class="badge black cancelbyAuction" lang="' .
                    $attributes['id'] .
                    '">Cancel by Auction</a> /
                            <a href="javascript:;" data-id="' .
                    $attributes['id'] .
                    '" class="badge black moveToArchive" lang="' .
                    $attributes['id'] .
                    '">Move To Archive</a> /
                            <a href="javascript:;" data-id="' .
                    $attributes['id'] .
                    '" class="badge red deletebidadmin" lang="' .
                    $attributes['id'] .
                    '">Delete</a>';
                if ($request->move_to_archived == 1) {
                    $actionBtn =
                        '<a href="javascript:;" data-id="' .
                        $attributes['id'] .
                        '" class="badge black winbid" lang="' .
                        $attributes['id'] .
                        '">Win</a> /
                            <a href="javascript:;" data-id="' .
                        $attributes['id'] .
                        '" class="badge black soldbid soldoverbid" lang="' .
                        $attributes['id'] .
                        '">Over Sold</a> /
                            <a href="javascript:;" data-id="' .
                        $attributes['id'] .
                        '" class="badge black cancelbyAuction" lang="' .
                        $attributes['id'] .
                        '">Cancel by Auction</a>';
                }
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
        $filteredItems = Vendor::selectRaw('id,project, COUNT(*) as total_records')->whereNotNull('project')->where('project', '!=', '')->groupBy('project')->orderBy('project', 'asc')->where('active', 'Y')->get();

        return view('backend.vendor.create', compact('filteredItems'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            's_no' => 'nullable|string|max:255',
            'from_account_type' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'parent' => 'nullable|string|max:255',
            'account_number' => 'required|string|max:255',
            'name_of_bank' => 'required|string|max:255',
            'ifsc_code_id' => 'nullable|string|max:255',
            'ifsc_code' => 'required|string|max:255|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/', // IFSC code pattern
            'vendor_type' => 'nullable|string|max:255',
            'vendor_code' => 'required|string|max:255|unique:vendors,vendor_code',
            'vendor_name' => 'required|string|max:255|unique:vendors,vendor_name',
            'vendor_email' => 'required|email|max:255|unique:vendors,vendor_email',
            'vendor_mobile' => 'nullable|string|digits:10|unique:vendors,vendor_mobile',
            'activity_type' => 'nullable|string|max:255',
            'vendor_nick_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|digits:10',
            'gstin' => 'nullable|string',
            'pan' => 'required|string',
            'pin' => 'nullable|string',
            'country_id' => 'nullable|string|max:255',
            'state_id' => 'nullable|string|max:255',
            'city_id' => 'nullable|string|max:255',
            'country_name' => 'nullable|string|max:255',
            'state_name' => 'nullable|string|max:255',
            'city_name' => 'nullable|string|max:255',
            'msme_classification' => 'nullable|string|max:255',
            'msme' => 'nullable|string|max:255',
            'msme_registration_number' => 'nullable|string|max:255',
            'msme_start_date' => 'nullable|date',
            'msme_end_date' => 'nullable|date',
            'material_nature' => 'nullable|string|max:255',
            'gst_defaulted' => 'nullable|string|max:255',
            'section_206AB_verified' => 'nullable|string|max:255',
            'benificiary_name' => 'required|string|max:255',
            'remarks_address' => 'nullable|string|max:255',
            'common_bank_details' => 'nullable|string|max:255',
            'income_tax_type' => 'nullable|string|max:255',
            'file_path.*' => 'file|max:10240000',
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $files = [];
        if ($request->hasFile('file_path')) {
            foreach ($request->file('file_path') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/vendorFile', $filename);
                $files[] = $filename;
            }
        }

        try {
            // Create vendor record
            // $vendor = Vendor::create($request->all());
            $vendor = Vendor::create(array_merge($request->all(), ['file_path' => json_encode($files)]));
            // dd($request->all());

            activity('Vendor Created')
                ->performedOn($vendor) // Vendor model entry
                ->causedBy(auth()->user()) // Logged-in user
                ->event('created')
                ->withProperties([
                    'vendor_id' => $vendor->id, // Vendor ID
                    'vendor_name' => $vendor->vendor_name, // Vendor Name
                    'created_by' => auth()->user()->name, // User who created
                    'created_by_email' => auth()->user()->email, // User Email
                ])
                ->log("Vendor '{$vendor->vendor_name}' created by " . auth()->user()->name);
            return redirect()->route('backend.vendors.index')->withSuccess('Vendor created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withError('Failed to create vendor. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        return view('backend.vendor.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        $filteredItems = Vendor::selectRaw('id,project, COUNT(*) as total_records')->whereNotNull('project')->where('project', '!=', '')->groupBy('project')->orderBy('project', 'asc')->where('active', 'Y')->get();

        return view('backend.vendor.edit', compact('vendor', 'filteredItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $user = auth()->user();

        $rules = [
            's_no' => 'nullable|string|max:255',
            'from_account_type' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'project' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'parent' => 'nullable|string|max:255',
            'account_number' => 'required|string|max:255',
            'name_of_bank' => 'required|string|max:255',
            'ifsc_code_id' => 'nullable|string|max:255',
            'ifsc_code' => 'required|string|max:255|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
            'vendor_type' => 'nullable|string|max:255',
            'vendor_code' => 'required|string|max:255|unique:vendors,vendor_code,' . $vendor->id,
            'vendor_name' => 'required|string|max:255|unique:vendors,vendor_name,' . $vendor->id,
            'vendor_email' => 'required|email|max:255|unique:vendors,vendor_email,' . $vendor->id,
            'vendor_mobile' => 'nullable|string|digits:10|unique:vendors,vendor_mobile,' . $vendor->id,
            'activity_type' => 'nullable|string|max:255',
            'vendor_nick_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|digits:10',
            'gstin' => 'nullable|string',
            'pan' => 'required|string',
            'pin' => 'nullable|string',
            'country_id' => 'nullable|string|max:255',
            'state_id' => 'nullable|string|max:255',
            'city_id' => 'nullable|string|max:255',
            'country_name' => 'nullable|string|max:255',
            'state_name' => 'nullable|string|max:255',
            'city_name' => 'nullable|string|max:255',
            'msme_classification' => 'nullable|string|max:255',
            'msme' => 'nullable|string|max:255',
            'msme_registration_number' => 'nullable|string|max:255',
            'msme_start_date' => 'nullable|date',
            'msme_end_date' => 'nullable|date',
            'material_nature' => 'nullable|string|max:255',
            'gst_defaulted' => 'nullable|string|max:255',
            'section_206AB_verified' => 'nullable|string|max:255',
            'benificiary_name' => 'required|string|max:255',
            'remarks_address' => 'nullable|string|max:255',
            'common_bank_details' => 'nullable|string|max:255',
            'income_tax_type' => 'nullable|string|max:255',
            'active' => 'required|in:Y,N',
        ];

        if (!$user->hasRole('Admin')) {
            $rules['file_path'] = 'required|array';
            $rules['file_path.*'] = 'file|max:10240000';
        } else {
            $rules['file_path'] = 'nullable|array';
            $rules['file_path.*'] = 'nullable|file|max:10240000';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $existingFiles = json_decode($vendor->file_path, true) ?? [];

        $files = $existingFiles;
        if ($request->hasFile('file_path')) {
            foreach ($request->file('file_path') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('public/vendorFile', $filename);
                $files[] = $filename; // Append new files
            }
        }

        try {
            // $vendor->update($request->except('_token', '_method'));
            $vendor->update(array_merge($request->except('_token', '_method'), ['file_path' => json_encode($files)]));

            activity('Vendor updated')
                ->performedOn($vendor) // Vendor model entry
                ->causedBy(auth()->user()) // Logged-in user
                ->event('created')
                ->withProperties([
                    'vendor_id' => $vendor->id, // Vendor ID
                    'vendor_name' => $vendor->vendor_name, // Vendor Name
                    'created_by' => auth()->user()->name, // User who created
                    'created_by_email' => auth()->user()->email, // User Email
                ])
                ->log("Vendor '{$vendor->vendor_name}' updated by " . auth()->user()->name);
            return redirect()->route('backend.vendors.index')->withSuccess('Vendor updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withError('Error updating vendor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        //
    }
    /**
     * Import payment file.
     */
    public function import(Request $request)
    {
        //
        return view('backend.vendor.import-file');
    }
    /**
     * Import payment file.
     */
    public function importStore(Request $request)
    {
        //
        // Excel::import(new VendorsImport, $request->import_payment);
        // Excel::import(new VendorsImport, $request->import_payment, 'local', \Maatwebsite\Excel\Excel::XLSX);

        try {
            \Excel::import(new VendorsImport(), $request->import_payment);
            activity('Vendor Import')
                ->performedOn(new Vendor())
                ->causedBy(auth()->user()->id)
                ->event(__METHOD__)
                ->withProperties([])
                ->log('File Imported Successfully');

            return redirect()->back()->with('success', 'File Imported Successfully');
        } catch (\Exception $e) {
            Log::info($e);
            activity('Vendor Import')
                ->performedOn(new Vendor())
                ->causedBy(auth()->user()->id)
                ->event(__METHOD__)
                ->withProperties($e)
                ->log($e->getMessage());
            return redirect()
                ->back()
                ->with('error', $e->getMessage() ?? 'Internal server error while import file');
        }
    }
}
