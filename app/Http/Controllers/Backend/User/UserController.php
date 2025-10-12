<?php

namespace App\Http\Controllers\Backend\User;

use App\Events\UserUpdated;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\NoteStatusChangeMail;
use App\Mail\SendNotificationMails;
use App\Mail\UpdateRoleMail;
use App\Mail\UpdateUserMail;
use App\Mail\UserNotifyEmail;
use App\Models\Department;
use App\Models\Designation;
use App\Notifications\SendNotificationMail;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-user|edit-user|delete-user', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-user', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-user', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-user', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        DB::connection()->enableQueryLog();
        if ($request->ajax()) {
            $data = User::orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                // ->removeColumn('id')
                ->editColumn('roles', function ($row) {
                    // edit-user

                    $btn = '';
                    foreach ($row->roles as $roles) {
                        $btn .= '<span class="badge rounded-pill bg-dark">' . $roles->name . '&nbsp;&nbsp;</span>';
                    }
                    return $btn;
                })
                ->addColumn('active', function ($row) {
                    if ($row->active === 'Y') {
                        return '<span class="badge bg-success">Active</span>';
                    } else {
                        return '<span class="badge bg-danger">Inactive</span>';
                    }
                })

                ->addColumn('action', function ($row) {
                    $btn =
                        '<form action="' .
                        route('backend.users.destroy', $row->id) .
                        '" method="post">
                            ' .
                        csrf_field() .
                        '
                            <input type="hidden" name="_method" value="DELETE">
                            <a href="' .
                        route('backend.users.edit', $row->id) .
                        '" class="btn btn-outline-primary btn-xs"><i class="bi bi-pencil-square"></i></a>
                            <a href="' .
                        route('backend.users.show', $row->id) .
                        '" class="btn btn-outline-primary btn-xs"><i class="bi bi-eye"></i></a>
                            <button type="submit" class="btn btn-outline-danger btn-xs" onclick="return confirm(\'Do you want to delete this role?\');"><i class="bi bi-trash"></i></button>
                        </form>';
                    return $btn;
                })
                ->rawColumns(['action', 'roles', 'active'])
                ->make(true);
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);

        return view('backend.users.index');
        /* return view('backend.users.index', [
            'users' => User::latest('id')->paginate(3)
        ]); */
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $designations = Designation::all();
        $departments = Department::all();
        return view('backend.users.create', [
            'roles' => Role::pluck('name')->all(),
            'designations' => $designations,
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $roles = $request->roles;
        $input = $request->except(['_token', 'password_confirmation', 'roles']);
        $input['password'] = $request->password;

        if ($request->hasFile('file')) {
            $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
            $filePath = $request->file('file')->move(public_path('uploads'), $fileName);
            $input['file'] = $fileName;
        }

        $user = User::create($input);

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'active' => ($user->active == 'Y' ? 'Active' : 'Inactive') ?? '-',
            'password' => $request->password,
            'updated_by' => auth()->user()->email,
        ];
        activity('User Created')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->event('created')
            ->withProperties([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'Active' => $user->active,
                'created_by' => auth()->user()->name,
                'created_by_email' => auth()->user()->email,
            ])
            ->log("User '{$user->name}' updated by " . auth()->user()->name . ' | Active - ' . ($user->active == 'Y' ? 'Active' : 'Inactive') . ' | Email - ' . $user->email . ' | Account Holder - ' . $user->account_holder . ' | Bank Name - ' . $user->bank_name . ' | Bank Account - ' . $user->bank_account . ' | IFSC Code - ' . $user->ifsc_code);
        // Mail::to('kenepe2798@erapk.com')->send(new UpdateUserMail($data));
        Mail::to($user->email)->send(new UpdateUserMail($data));
        $user->assignRole($request->roles);
        return redirect()->route('backend.users.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        // return redirect()->route('backend.users.index');
        if ($user->hasRole('Super Admin')) {
            if ($user->id != auth()->user()->id) {
                abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
            }
        }

        return view('backend.users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, User $user): View
    {
        // Check Only Super Admin can update his own Profile
        if ($user->hasRole('Super Admin')) {
            if ($user->id != auth()->user()->id) {
                abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
            }
        }
        $designations = Designation::all();
        $departments = Department::all();
        return view('backend.users.edit', [
            'user' => $user,
            'designations' => $designations,
            'departments' => $departments,
            'roles' => Role::pluck('name')->all(),
            'userRoles' => $user->roles->pluck('name')->all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $roles = $request->roles;
        
        // Get all input except tokens and password confirmation
        $input = $request->except('_token', '_method', 'roles', 'password', 'password_confirmation');
        
        // If password is provided, add it to input and delete sessions
        if (!empty($request->password)) {
            $input['password'] = $request->password;
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }
        
        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            if ($user->file && file_exists(public_path('uploads/' . $user->file))) {
                unlink(public_path('uploads/' . $user->file));
            }

            // Save the new file
            $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
            $filePath = $request->file('file')->move(public_path('uploads'), $fileName);
            $input['file'] = $fileName;
        }
        $user->update($input);

        $user->syncRoles($roles);

        activity('User Updated')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->event('updated')
            ->withProperties([
                'user_id' => $user->id,
                'vendor_name' => $user->name,
                'Active' => $user->active,
                'updated_by' => auth()->user()->name,
                'updated_by_email' => auth()->user()->email,
            ])
            ->log("User '{$user->name}' updated by " . auth()->user()->name . ' | Active - ' . ($user->active == 'Y' ? 'Active' : 'Inactive') . ' | Email - ' . $user->email . ' | Account Holder - ' . $user->account_holder . ' | Bank Name - ' . $user->bank_name . ' | Bank Account - ' . $user->bank_account . ' | IFSC Code - ' . $user->ifsc_code);
        $data = [
            'name' => $user->name ?? '-',
            'email' => $user->email ?? '-',
            'active' => ($user->active == 'Y' ? 'Active' : 'Inactive') ?? '-',
            'password' => $request->filled('password') ? $request->password : null,
            'updated_by' => auth()->user()->email,
        ];
        Mail::to($user->email)->send(new UpdateUserMail($data));

        $userRole = auth()->user();

        if ($userRole->id === '1' && $userRole->hasRole('Super Admin Live')) {
            $recipients = ['dharmendrameel@nhit.co.in', 'ravinderkumar@nhit.co.in', '	ravivij@nhit.co.in'];
            $dataAll = [
                'role_name' => $user->name,
                'updated_by' => auth()->user()->email,
            ];
            Mail::to($recipients)->send(new UpdateRoleMail($dataAll));
        }

        /* $fileName = $getPdfRow->invoice_number . '.pdf';
        $pdfFileName = str_replace('/', '_', $fileName);
        $attachment = storage_path() . '/app' . DIRECTORY_SEPARATOR . $request->qtype . DIRECTORY_SEPARATOR . $pdfFileName; */
        $fileName = null;
        $pdfFileName = null;
        $attachment = null;
        $subject = 'New User Registraition';
        // $user = User::find(2);

        // $notifyData = [
        //     'user' => $user,
        //     'emailTemplate' => 'mails.backend.notify',
        //     'subject' => $subject,
        //     'message' => 'New user register',
        //     'body' => 'Dear ' . $user->name . ' ' . $user->last_name . ',<br>SP  request is send for settled.',
        //     'fileToAttachment' => $attachment,
        //     'attachmentName' => $fileName,
        //     'user_cc' => '',
        //     'user_bcc' => [],
        // ];
        // array_push($notifyData['user_bcc'], 'ksharma.sharma27@gmail.com');
        /* if (!empty($user)) {
            $notifyData['user_cc'] = $user->email;
        }
        if (!empty($quotation->saleshead_id)) {
            $user = User::find($quotation->saleshead_id);
            $notifyData['user_bcc'] = $user->email;
        } else {
            // array_push($notifyData['user_bcc'], 'ksharma.sharma27@gmail.com');
            // $users = \Users::getUsersList('saleshead', 'all');
            // foreach ($users as $singleUser) {
            //     array_push($notifyData['user_bcc'], $singleUser->email);
            // }
        } */

        // $admin = User::find(1);
        // $user->notify(new SendNotificationMail($admin, $notifyData));

        return redirect()->route('backend.users.index')->withSuccess('User is updated successfully.');

        // return redirect()->back()->withSuccess('User is updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // About if user is Super Admin or User ID belongs to Auth User
        if ($user->hasRole('Super Admin') || $user->id == auth()->user()->id) {
            abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
        }

        $user->syncRoles([]);
        $user->delete();
        return redirect()->route('backend.users.index')->withSuccess('User is deleted successfully.');
    }
    public function profile(Request $request, User $user): View
    {
        $authUser = auth()->user();

        // Prevent non-admin from viewing others' profiles
        if ($authUser->id !== $user->id && !$authUser->hasRole('Super Admin')) {
            abort(403, 'Unauthorized access.');
        }
        $designations = Designation::all();
        $departments = Department::all();
        return view('backend.users.profile', [
            'user' => $user,
            'designations' => $designations,
            'departments' => $departments,
            'roles' => Role::pluck('name')->all(),
            'userRoles' => $user->roles->pluck('name')->all(),
        ]);
    }
    public function updateProfile(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:250',
            'email' => 'required|string|max:250|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'designation_id' => 'nullable|exists:designations,id',
            'department_id' => 'nullable|exists:departments,id',
            'file' => 'nullable|mimes:png|max:2048',
        ]);

        $roles = $request->roles;
        
        // Get all input except tokens and password confirmation
        $input = $request->except('_token', '_method', 'roles', 'password', 'password_confirmation');
        
        // If password is provided, add it to input and delete sessions
        if (!empty($request->password)) {
            $input['password'] = $request->password;
            DB::table('sessions')->where('user_id', $user->id)->delete();
        }
        
        if ($request->hasFile('file')) {
            // Delete the old file if it exists
            if ($user->file && file_exists(public_path('uploads/' . $user->file))) {
                unlink(public_path('uploads/' . $user->file));
            }

            // Save the new file
            $fileName = time() . '_' . $request->file('file')->getClientOriginalName();
            $filePath = $request->file('file')->move(public_path('uploads'), $fileName);
            $input['file'] = $fileName;
        }
        $user->update($input);

        $userRole = auth()->user();

        if ($userRole->id === '1' && $userRole->hasRole('Super Admin Live')) {
            $recipients = ['dharmendrameel@nhit.co.in', 'ravinderkumar@nhit.co.in', '	ravivij@nhit.co.in'];

            $data = [
                'role_name' => $user->name,
                'updated_by' => auth()->user()->email,
            ];

            Mail::to($recipients)->send(new UpdateRoleMail($data));
        }
        return redirect()->back()->withSuccess('User is updated successfully.');
    }
}