<?php

namespace App\Http\Controllers;

use App\Filters\PostFilters;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\ViewPostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostResourceCollection;
use App\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param ViewPostRequest $request
     * @return JsonResponse
     */
    public function index(ViewPostRequest $request, PostFilters $filters): JsonResponse
    {
        $posts = Post::filter($filters)->paginate();

        return PostResource::collection($posts)->response($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreatePostRequest $request
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(CreatePostRequest $request): JsonResponse
    {
        $post = Post::make($request->all());
        $post->author()->associate($request->user());
        $post->saveOrFail();

        return PostResource::make($post)->response($request);
    }

    /**
     * Display the specified resource.
     *
     * @param ViewPostRequest $request
     * @param  Post $post
     * @return JsonResponse
     */
    public function show(ViewPostRequest $request, Post $post): JsonResponse
    {
        return PostResource::make($post)->response($request);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdatePostRequest $request
     * @param  Post $post
     * @return JsonResponse
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $post->update($request->all());

        return PostResource::make($post)->response($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeletePostRequest $request
     * @param  Post $post
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(DeletePostRequest $request, Post $post): JsonResponse
    {
        $post->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
