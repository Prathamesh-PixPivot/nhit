<?php namespace App\Http\Controllers\Backend\Ratio;

use App\Models\Ratio;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Schema;

class RatioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        DB::connection()->enableQueryLog();
        $sl_no_filter = Payment::select('sl_no')->groupBy('sl_no')->get();
        if ($request->ajax()) {
            $data = Payment::groupBy('sl_no')->orderBy('id', 'desc')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '';
                    $btn .=
                        '<form action="' .
                        route('backend.templates.templateCommon', $row->template_type) .
                        '" method="post">
                    ' .
                        csrf_field() .
                        '
                    <input type="hidden" name="slno" value="' .
                        $row->sl_no .
                        '">
                    <button type="submit" class="btn btn-outline-info btn-xs" onclick="return confirm(\'Do you want to preview/generate PDF??\');"><i class="bi bi-eye
                    "></i></button>
                </form>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        return view('backend.ratio.index', compact('sl_no_filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $filteredItems = Vendor::orderBy('id', 'asc')->get(); // Get all vendor items
        // Group the filtered items by the 'project' field, replacing null with 'N/A'
        $groupedItems = $filteredItems->groupBy(function ($item) {
            if (!is_null($item->project) && ($item->vendor_name == 'Escrow A/c' || $item->vendor_name == 'ONMExp')) {
                return $item->project ?? 'N/A'; // If 'project' is null, use 'N/A'
            }
        });

        // Ratios for each project
        $ratios = [
            'Abu Road – Swaroopganj' => 0.07272,
            'Palanpur- Abu Road' => 0.1124,
            'Kothakota Bypass – Kurnool' => 0.1902,
            'Belgaum – Karnataka Border' => 0.2283,
            'Chittorgarh – Kota' => 0.117,
            'Agra By pass' => 0.10097,
            'Shivpuri Jhansi' => 0.0445,
            'Borekhedi Wadner' => 0.1339,
        ];

        $projects = ['Abu Road – Swaroopganj', 'Palanpur- Abu Road', 'Kothakota Bypass – Kurnool', 'Belgaum – Karnataka Border', 'Chittorgarh – Kota', 'Agra By pass', 'Shivpuri Jhansi', 'Borekhedi Wadner'];
        $vendorItems = [];
        $totalAmount = 0;
        foreach ($ratios as $project => $ratio) {
            // Check if the project exists in the grouped items
            if (isset($groupedItems[$project])) {
                // Create a new array for the project to hold vendor items
                $vendorItems[$project] = [];

                foreach ($groupedItems[$project] as $vendor) {
                    // dd($vendor);
                    // Set the ratio and calculate the amount for the current vendor
                    $vendor->setAttribute('ratio', $ratio); // Set the ratio
                    $vendor->setAttribute('calculatedAmount', $totalAmount * $ratio); // Calculate the amount

                    // Add the vendor item to the project group in the $vendorItems array
                    $vendorItems[$project][] = $vendor;
                }
            }
        }
        $columns = Schema::getColumnListing('vendors');
        $resultsarray = [];
        $query = Vendor::query(); // Get all data of the class
        $query->select('id', 'project', 's_no', 'short_name', 'account_name', 'vendor_code', 'vendor_name', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type', 'ifsc_code', 'name_of_bank');

        $result = $query->whereNotNull('project')->take(18)->get();

        $result = DB::select("SELECT v1.* FROM vendors AS v1 JOIN vendors AS v2 ON v2.id = v1.id WHERE v1.from_account_type = 'Internal' group by s_no;");

        return view('backend.ratio.create', compact('vendorItems', 'result'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $from_ac = $request->has('from_ac') ? $request->from_ac : 'Escrow A/c';
        $to_ac = $request->has('to_ac') ? $request->to_ac : 'ONMExp';

        $filteredItems = Vendor::orderBy('id', 'asc')->get(); // Get all vendor items

        $groupedItems = $filteredItems->groupBy(function ($item) use ($from_ac, $to_ac) {
            if (!is_null($item->project) && ($item->s_no == $from_ac || $item->s_no == $to_ac)) {
                // if(!is_null($item->project) && ($item->account_name == $from_ac || $item->vendor_nick_name == $to_ac)){
                return $item->project ?? 'N/A';
            }
        });
        $ratios = [
            'Abu Road – Swaroopganj' => 0.0727152116466397,
            'Palanpur- Abu Road' => 0.112401227117261,
            'Kothakota Bypass – Kurnool' => 0.190204640846226,
            'Belgaum – Karnataka Border' => 0.228302431389416,
            'Chittorgarh – Kota' => 0.116999236320749,
            'Agra By pass' => 0.100970909568546,
            'Shivpuri Jhansi' => 0.0445032955566068,
            'Borekhedi Wadner' => 0.133903047554555,
        ];

        $projects = ['Abu Road – Swaroopganj', 'Palanpur- Abu Road', 'Kothakota Bypass – Kurnool', 'Belgaum – Karnataka Border', 'Chittorgarh – Kota', 'Agra By pass', 'Shivpuri Jhansi', 'Borekhedi Wadner'];
        $totalAmount = $request->input('amount');
        $remainingAmount = $totalAmount; // Track the remaining amount
        $vendorItems = [];
        $totalDistributed = 0;

        foreach ($ratios as $project => $ratio) {
            if (isset($groupedItems[$project])) {
                $vendorItems[$project] = [];

                // Calculate amount for the project (without decimals)
                $calculatedAmount = $totalAmount * $ratio;
                $roundedAmount = round($calculatedAmount, 0); // Round to nearest whole number
                $remainingAmount -= $roundedAmount; // Deduct rounded amount
                $totalDistributed += $roundedAmount; // Track distributed amount

                foreach ($groupedItems[$project] as $vendor) {
                    $vendor->setAttribute('ratio', $ratio);
                    $vendor->setAttribute('calculatedAmount', intval($roundedAmount)); // Assign as integer
                    $vendorItems[$project][] = $vendor;
                }
            }
        }

        // Adjust remaining amount in the last project (without decimals)
        $lastProject = array_key_last($ratios);
        if ($remainingAmount != 0 && isset($vendorItems[$lastProject])) {
            $lastVendor = end($vendorItems[$lastProject]); // Last vendor of the last project
            $adjustedAmount = $lastVendor->calculatedAmount + $remainingAmount; // Adjust remaining amount
            $lastVendor->calculatedAmount = intval(round($adjustedAmount, 0)); // Update as integer
            $totalDistributed = $totalAmount; // Ensure total matches input
        }

        // Prepare output
        $totalDistribution = [
            'totalAmount' => $totalAmount,
            'totalDistributed' => round($totalDistributed, 2),
            'remainingAmount' => 0,
        ];

        //  'remainingAmount' => round($remainingAmount, 2),
        // dd($totalDistribution);

        $columns = Schema::getColumnListing('vendors');
        $resultsarray = [];
        $query = Vendor::query(); // Get all data of the class
        $query->select('id', 'project', 's_no', 'short_name', 'account_name', 'vendor_code', 'vendor_name', 'account_number', 'vendor_nick_name', 'benificiary_name', 'from_account_type', 'ifsc_code', 'name_of_bank');

        $result = $query->whereNotNull('project')->take(18)->get();
        // dd($vendorItems);
        return view('backend.ratio.create', compact('vendorItems', 'result', 'totalDistribution'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ratio $ratio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ratio $ratio) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ratio $ratio)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ratio $ratio)
    {
        //
    }
    public function amount(Request $request)
    {
        // dd(1);
        $filteredItems = Vendor::all()->toArray();

        $desiredOrder = ['Abu Road – Swaroopganj', 'Palanpur- Abu Road', 'Kothakota Bypass – Kurnool', 'Belgaum – Karnataka Border', 'Chittorgarh – Kota', 'Agra By pass', 'Shivpuri Jhansi', 'Borekhedi Wadner'];

        $ratios = [
            'Abu Road – Swaroopganj' => 0.0727152116466397,
            'Palanpur- Abu Road' => 0.112401227117261,
            'Kothakota Bypass – Kurnool' => 0.190204640846226,
            'Belgaum – Karnataka Border' => 0.228302431389416,
            'Chittorgarh – Kota' => 0.116999236320749,
            'Agra By pass' => 0.100970909568546,
            'Shivpuri Jhansi' => 0.0445032955566068,
            'Borekhedi Wadner' => 0.133903047554555,
        ];

        $vendorItems = [];
        $totalAmount = $request->input('amount');

        foreach ($desiredOrder as $project) {
            $item = collect($filteredItems)->firstWhere('project', $project);
            if ($item) {
                $item['ratio'] = isset($ratios[$project]) ? $ratios[$project] : 0;
                $item['calculatedAmount'] = $totalAmount * $item['ratio'];
                $vendorItems[] = $item;
            }
        }

        while (count($vendorItems) < 8) {
            $vendorItems[] = [
                'template_type' => 'N/A',
                'project' => 'N/A',
                'account_full_name' => 'N/A',
                'from_account_type' => 'N/A',
                'full_account_number' => 'N/A',
                'to' => 'N/A',
                'to_account_type' => 'N/A',
                'benificiary_name' => 'N/A',
                'account_number' => 'N/A',
                'name_of_bank' => 'N/A',
                'ifsc_code' => 'N/A',
                'amount' => 'N/A',
                'purpose' => 'N/A',
            ];
        }
        return view('backend.ratio.create', compact('vendorItems'));
    }
}
