<?php

namespace App\Http\Controllers\Backend\Template;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use PDF;

class TemplateController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Return view templateSBI
     */
    public function templateCommon(Request $request, $tpl = null, $slno = null)
    {
        // dd($slno);
        $data = [];
        $groupRow = null;
        $sl_no = $slno ?? ($request->slno ?? null);
        if (!is_null($sl_no)) {
            $data = Payment::where('sl_no', $sl_no)->latest()->take(12)->orderBy('id', 'asc')->get();

            $groupRow = Payment::select('ref_no', 'sl_no')->where('sl_no', $sl_no)->latest()->take(1)->orderBy('id', 'desc')->first();
        }
        // dd(['data' => $data, 'groupRow' => $groupRow], $tpl);

        if ($request->ajax()) {
            $viewHtml = view('backend.templates.pdf.' . $tpl, ['data' => $data, 'groupRow' => $groupRow])->render();
            return response()->json([
                'success' => true,
                'message' => 'Data loaded',
                'data' => [
                    'view' => $viewHtml,
                    'requestData' => $request->except('_token'),
                ],
            ]);
        }
        return view('backend.templates.tplCommon', ['tpl' => $tpl]);
        // return view('backend.templates.show', ['tpl' => $tpl]);
    }

    /**
     * Return view templateBulkrtgs
     */
    public function templateBulkrtgs(Request $request, $slno = null)
    {
        return view('backend.templates.bulk-rtgs', []);
    }
    /**
     * Return view templateMFAxis
     */
    public function templateMFAxis(Request $request, $slno = null)
    {
        return view('backend.templates.mf-axis', []);
    }
    /**
     * Return view templateMFKotak
     */
    public function templateMFKotak(Request $request, $slno = null)
    {
        return view('backend.templates.mk-kotak', []);
    }
    /**
     * Return view templateMFSbi
     */
    public function templateMFSbi(Request $request, $slno = null)
    {
        return view('backend.templates.mf-sbi', []);
    }
    /**
     * Return view templateRTGS
     */
    public function templateRTGS(Request $request, $slno = null)
    {
        return view('backend.templates.rtgs', []);
    }
    /**
     * Return view templateSalary
     */
    public function templateSalary(Request $request, $slno = null)
    {
        return view('backend.templates.salary', []);
    }
    /**
     * Return view templateSBI
     */
    public function templateSBI(Request $request, $slno = null)
    {
        return view('backend.templates.sbi', []);
    }

    /**
     * Return view templateBulkrtgsGeneratePdf
     */
    public function templateBulkrtgsGeneratePdf(Request $request, $slno = null)
    {
        $data = [];
        $pdf = PDF::loadView('backend.templates.pdf.bulk-rtgs');
        return $pdf->stream('bulk-rtgs.pdf');

        // $html = view('backend.templates.pdf.bulk-rtgs')->render();
        // return Pdf::loadHTML($html)->setPaper('a4', 'landscape')->setWarnings(false)->download('myfile.pdf');

        /* $pdf = Pdf::loadView('backend.templates.pdf.bulk-rtgs', $data);
         return $pdf->download('invoice.pdf'); */
        // dd($request->all());
        // return view('backend.templates.bulk-rtgs', []);
    }
    /**
     * Return view templateMFAxisGeneratePdf
     */
    public function templateMFAxisGeneratePdf(Request $request, $slno = null)
    {
        return view('backend.templates.mf-axis', []);
    }
    /**
     * Return view templateMFKotakGeneratePdf
     */
    public function templateMFKotakGeneratePdf(Request $request, $slno = null)
    {
        return view('backend.templates.mk-kotak', []);
    }
    /**
     * Return view templateMFSbiGeneratePdf
     */
    public function templateMFSbiGeneratePdf(Request $request, $slno = null)
    {
        return view('backend.templates.mf-sbi', []);
    }
    /**
     * Return view templateRTGSGeneratePdf
     */
    public function templateRTGSGeneratePdf(Request $request, $slno = null)
    {
        return view('backend.templates.rtgs', []);
    }
    /**
     * Return view templateSalaryGeneratePdf
     */
    public function templateSalaryGeneratePdf(Request $request, $slno = null)
    {
        return view('backend.templates.salary', []);
    }
    /**
     * Return view templateSBIGeneratePdf
     */
    public function templateSBIGeneratePdf(Request $request, $slno = null)
    {
        return view('backend.templates.sbi', []);
    }

    /**
     * Return view templateSBI
     */
    public function templateCommonGenPdf(Request $request, $tpl = null)
    {
        $data = [];
        $tpl = $tpl ?? request()->route('tpl');
        $sl_no = $request->slno ?? 4171;
        // dd($request->all());
        if (!is_null($sl_no)) {
            $data = Payment::where('sl_no', $sl_no)->latest()->take(12)->orderBy('id', 'asc')->get();

            $groupRow = Payment::select('ref_no', 'sl_no')->where('sl_no', $sl_no)->latest()->take(1)->orderBy('id', 'desc')->first();
        }

        $pdf = PDF::loadView('backend.templates.pdf.' . $tpl, ['data' => $data, 'groupRow' => $groupRow]);
        // return $pdf->stream($sl_no . '.pdf');
        return $pdf->download($sl_no . '.pdf');
    }
}
