<?php

namespace App\Http\Controllers;

use App\Http\Requests\ViewUsersRequest;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param ViewUsersRequest $request
     * @return JsonResponse
     */
    public function index(ViewUsersRequest $request): JsonResponse
    {
        return UserResource::collection(User::paginate())->response();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        return UserResource::make($request->user())->response();
    }
}
