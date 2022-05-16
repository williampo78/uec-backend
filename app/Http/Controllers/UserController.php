<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RolesPermissionService;
use App\Services\SupplierService;
use App\Services\UniversalService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $userService;
    private $supplierService;
    private $rolePermissionService;

    public function __construct(
        UserService $userService,
        SupplierService $supplierService,
        RolesPermissionService $rolePermissionService
    ) {
        $this->userService = $userService;
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
        $data['users'] = ($getData ? $this->userService->getUsers($getData) : []);

        return view('backend.user.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['suppliers'] = $this->supplierService->getSuppliers();
        $data['roles'] = $this->rolePermissionService->getRoles();

        return view('backend.user.create', $data);
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
        $this->userService->addUser($input);

        $act = 'add';
        $route_name = 'users';

        return view('backend.success', compact('route_name', 'act'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = $this->userService->getUserById($id);

        $payloads = [
            'user_account' => $user->user_account,
            'user_name' => $user->user_name,
            'user_active' => $user->active == 1 ? '啟用' : '關閉',
            'user_email' => $user->user_email,
            'supplier' => isset($user->supplier) ? $user->supplier->name : null,
            'roles' => isset($user->roles) ? $user->roles->implode('role_name', '、') : null,
        ];

        return response()->json($payloads);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['suppliers'] = $this->supplierService->getSuppliers();
        $data['roles'] = $this->rolePermissionService->getRoles();
        $data['user'] = $this->userService->getUserById($id);

        if (!isset($data['user'])) {
            abort(404);
        }

        return view('backend.user.edit', $data);
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
        $input = $request->input();
        $input['id'] = $id;
        $this->userService->updateUser($input);

        $act = 'upd';
        $route_name = 'users';

        return view('backend.success', compact('route_name', 'act'));
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

    /**
     * 檢查使用者帳號是否重複
     *
     * @param Request $request
     * @return boolean
     */
    public function isUserAccountRepeat(Request $request)
    {
        $user_account = $request->user_account;
        $status = false;

        $user_count = User::where('user_account', '=', $user_account)->count();

        if ($user_count < 1) {
            $status = true;
        }

        return response()->json([
            'status' => $status,
        ]);
    }

    public function profile()
    {
        $authUser = Auth::user();
        $data['user'] = $this->userService->getUserById($authUser->id);

        return view('backend.user.profile', $data);
    }

    public function updateProfile(Request $request)
    {
        $input = $request->input();
        $this->userService->updateProfile($input);

        return view('backend.home');
    }
}
