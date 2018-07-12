<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\DeletePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\ViewPostRequest;
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
    public function index(ViewPostRequest $request): JsonResponse
    {
        $posts = $request->has('my-posts') ? auth()->user()->posts : Post::all();

        return response()->json($posts);
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

        return response()->json($post, Response::HTTP_CREATED);
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
        return response()->json($post);
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

        return response()->json($post);
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
