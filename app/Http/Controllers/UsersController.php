<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Users;
use App\Services\UsersService;
use App\Services\UniversalService;
use App\Services\SupplierService;
use App\Services\RolesPermissionService;
class UsersController extends Controller
{

    private $usersService;
    private $universalService;
    private $supplierService;
    private $rolePermissionService;

    public function __construct(UsersService $usersService, UniversalService $universalService, SupplierService $supplierService, RolesPermissionService $rolePermissionService)
    {
        $this->usersService = $usersService;
        $this->universalService = $universalService;
        $this->supplierService = $supplierService;
        $this->rolePermissionService = $rolePermissionService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $getData = $request->all();
        $data['role'] = ($getData ? $this->usersService->getUsers($getData) : []);
        $data['user'] = $this->universalService->getUser();
        $data['getData'] = $getData;
        return view('Backend.Users.list', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['suppliers'] = $this->supplierService->getSupplier();
        $data['roles'] = $this->rolePermissionService->getRoles("");
        return view('Backend.users.add', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->input();
        unset($input['_token']);
        $act = 'add';
        $route_name = 'users';
        $this->usersService->addUser($input, $act);
        return view('backend.success', compact('route_name', 'act'));

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Users::find($id);
        return view('Backend.Users.upd', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $parameter['user_name'] = $request->input('user_name');
        $parameter['user_email'] = $request->input('user_email');
        if ($request->input('password') != '') {
            $parameter['user_password'] = md5($request->input('password'));
        }
        $parameter['updated_by'] = session('users')['user_id'];
        Users::where('id', $id)->update($parameter);
        $route_name = 'profile';
        $act = 'upd';
        if ($request->input(['profile']) == 1) {
            return view('Backend.example');
        } else {
            return view('backend.success', compact('route_name', 'act'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function ajax(Request $request)
    {

        $user_account = $request->input('fieldValue');
        $validateId = $request->input('fieldId');

        $arrayToJs = array();
        $arrayToJs[0] = $validateId;
        $arrayToJs[1] = true;
        $userCount = Users::where('user_account', '=', $user_account)->count();
        if ($userCount > 0) {
            $arrayToJs[1] = false;
        }
        echo json_encode($arrayToJs);

    }

    public function profile ()
    {
        $user_id = Auth::user()->id;
        $data = Users::find($user_id);
        return view('Backend.Users.profile', compact('data'));
    }

    public function updateProfile(Request $request)
    {
        $user_id = Auth::user()->id;

        $parameter['user_name'] = $request->input('user_name');
        $parameter['user_email'] = $request->input('user_email');
        if ($request->input('password') != '') {
            $parameter['user_password'] = md5($request->input('password'));
        }
        $parameter['updated_by'] = session('users')['user_id'];
        Users::where('id', $user_id)->update($parameter);
        $route_name = 'profile';
        $act = 'upd';
        if ($request->input(['profile']) == 1) {
            return view('Backend.example');
        } else {
            return view('backend.success', compact('route_name', 'act'));
        }
    }
}
