<?php

namespace App\Http\Controllers\Backend\Role;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Mail\UpdateRoleMail;
use Brian2694\Toastr\Toastr;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RoleController extends Controller
{
    protected $toastOptions;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:create-role|edit-role|delete-role', ['only' => ['index', 'show']]);
        $this->middleware('permission:create-role', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-role', ['only' => ['destroy']]);

        $this->toastOptions = [
            'closeButton' => true,
            'debug' => false,
            'newestOnTop' => true,
            'progressBar' => true,
            'positionClass' => 'toast-top-right',
            'preventDuplicates' => true,
            'onclick' => null,
            'showDuration' => '300',
            'hideDuration' => '1000',
            'timeOut' => '5000',
            'extendedTimeOut' => '1000',
            'showEasing' => 'swing',
            'hideEasing' => 'linear',
            'showMethod' => 'fadeIn',
            'hideMethod' => 'fadeOut',
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        DB::connection()->enableQueryLog();
        if ($request->ajax()) {
            $data = Role::with('permissions')->orderBy('id', 'DESC')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                // ->removeColumn('id')
                ->editColumn('permissions', function ($row) {
                    $btn = '';
                    foreach ($row->permissions as $permission) {
                        $btn .= '<span class="badge rounded-pill bg-dark">' . $permission->name . '&nbsp;&nbsp;</span>';
                    }
                    return $btn;
                })
                ->addColumn('action', function ($row) {
                    $btn =
                        '<form action="' .
                        route('backend.roles.destroy', $row->id) .
                        '" method="post">
                            ' .
                        csrf_field() .
                        '
                            <input type="hidden" name="_method" value="DELETE">
                            <a href="' .
                        route('backend.roles.edit', $row->id) .
                        '" class="btn btn-outline-primary btn-xs"><i class="bi bi-pencil-square"></i></a>
                            <a href="' .
                        route('backend.roles.show', $row->id) .
                        '" class="btn btn-outline-primary btn-xs"><i class="bi bi-eye"></i></a>
                            <button type="submit" class="btn btn-outline-danger btn-xs" onclick="return confirm(\'Do you want to delete this role?\');"><i class="bi bi-trash"></i></button>
                        </form>';
                    return $btn;
                })
                ->rawColumns(['action', 'permissions'])
                ->make(true);
        }
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        return view('backend.roles.index');
        /* return view('backend.roles.index', [
            'roles' => Role::with('permissions')->orderBy('id', 'DESC')->paginate(3)
        ]); */
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('backend.roles.create', [
            'permissions' => Permission::get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create(['name' => $request->name]);

        $permissions = Permission::whereIn('id', $request->permissions)
            ->get(['name'])
            ->toArray();

        $role->syncPermissions($permissions);
        activity('Role Created')
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->event('created')
            ->withProperties([
                'role_id' => $role->id,
                'role_name' => $role->name,
                'created_by' => auth()->user()->name,
                'created_by_email' => auth()->user()->email,
            ])
            ->log("Role '{$role->name}' created by " . auth()->user()->name);
        return redirect()->route('backend.roles.index')->withSuccess('New role is added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): View
    {
        // return redirect()->route('backend.roles.index');
        return view('backend.roles.show', [
            'role' => $role,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        if ($role->name == 'Super Admin') {
            abort(403, 'SUPER ADMIN ROLE CAN NOT BE EDITED');
        }

        $rolePermissions = DB::table('role_has_permissions')->where('role_id', $role->id)->pluck('permission_id')->all();

        return view('backend.roles.edit', [
            'role' => $role,
            'permissions' => Permission::get(),
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $input = $request->only('name');

        $role->update($input);

        $permissions = Permission::whereIn('id', $request->permissions)
            ->get(['name'])
            ->toArray();

        $role->syncPermissions($permissions);

        $userRole = auth()->user();

        if ($userRole->id === '1' && $userRole->hasRole('Super Admin Live')) {
            // $recipients = ['daliyatimmy@nhit.co.in', 'ravinderkumar@nhit.co.in', 'rinkal@nhit.co.in'];
            $recipients = ['dharmendrameel@nhit.co.in', 'ravinderkumar@nhit.co.in', '	ravivij@nhit.co.in'];

            $data = [
                'role_name' => $role->name,
                'updated_by' => auth()->user()->email,
            ];

            Mail::to($recipients)->send(new UpdateRoleMail($data));
        }
        activity('Role Updated')
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->event('updated')
            ->withProperties([
                'role_id' => $role->id,
                'role_name' => $role->name,
                'updated_by' => auth()->user()->name,
                'updated_by_email' => auth()->user()->email,
            ])
            ->log("Role '{$role->name}' updated by " . auth()->user()->name);
        toastr()->success('Role is updated successfully.', 'Success', $this->toastOptions);
        return redirect()->back()->withSuccess('Role is updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($role->name == 'Super Admin') {
            abort(403, 'SUPER ADMIN ROLE CAN NOT BE DELETED');
        }
        if (auth()->user()->hasRole($role->name)) {
            abort(403, 'CAN NOT DELETE SELF ASSIGNED ROLE');
        }
        $role->delete();
        return redirect()->route('backend.roles.index')->withSuccess('Role is deleted successfully.');
    }
}
