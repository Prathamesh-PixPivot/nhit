<?php

namespace App\Http\Controllers\Backend\Beneficiary;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BeneficiaryController extends Controller
{
    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-beneficiary|create-beneficiary|edit-beneficiary|delete-beneficiary', ['only' => ['index','show']]);
        $this->middleware('permission:create-beneficiary', ['only' => ['create','store']]);
        $this->middleware('permission:edit-beneficiary', ['only' => ['edit','update']]);
        $this->middleware('permission:delete-beneficiary', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        DB::connection()->enableQueryLog();
        if ($request->ajax()) {
            $data = User::orderBy('id', 'DESC')->get();
            return DataTables::of($data)->addIndexColumn()
                // ->removeColumn('id')
                ->editColumn('roles', function($row){
                    // edit-user
                    
                    $btn = '';
                    foreach($row->roles as $roles){
                        $btn .= '<span class="badge rounded-pill bg-dark">'.$roles->name.'&nbsp;&nbsp;</span>';
                    }
                    return $btn;
                })
                ->addColumn('action', function($row){
                    $btn = '<form action="'.route('backend.users.destroy', $row->id).'" method="post">
                            '.csrf_field().'
                            <input type="hidden" name="_method" value="DELETE">
                            <a href="'.route('backend.users.edit', $row->id).'" class="btn btn-outline-primary btn-xs"><i class="bi bi-pencil-square"></i></a>
                            <button type="submit" class="btn btn-outline-danger btn-xs" onclick="return confirm(\'Do you want to delete this role?\');"><i class="bi bi-trash"></i></button>
                        </form>';
                    return $btn;
                })
                ->rawColumns(['action', 'roles'])
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
        return view('backend.users.create', [
            'roles' => Role::pluck('name')->all()
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

        $user = User::create($input);
        $user->assignRole($request->roles);

        return redirect()->route('backend.users.index')
                ->withSuccess('New user is added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): RedirectResponse
    {
        return redirect()->route('backend.users.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, User $user): View
    {
        // Check Only Super Admin can update his own Profile
        if ($user->hasRole('Super Admin')){
            if($user->id != auth()->user()->id){
                abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
            }
        }
        return view('backend.users.edit', [
            'user' => $user,
            'roles' => Role::pluck('name')->all(),
            'userRoles' => $user->roles->pluck('name')->all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $roles = $request->roles;
        if(!empty($request->password)){
            $input['password'] = $request->password;
        }else{
            $input = $request->except('_token', '_method', 'roles', 'password', 'password_confirmation');
        }
        
        $user->update($input);

        $user->syncRoles($roles);

        return redirect()->back()
                ->withSuccess('User is updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // About if user is Super Admin or User ID belongs to Auth User
        if ($user->hasRole('Super Admin') || $user->id == auth()->user()->id)
        {
            abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
        }

        $user->syncRoles([]);
        $user->delete();
        return redirect()->route('backend.users.index')
                ->withSuccess('User is deleted successfully.');
    }
}