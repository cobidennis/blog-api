<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{

    /**
     * GET /api/posts
     */
    public function index(): JsonResponse
    {
        $posts = Post::latest()->paginate(10);
        return PostResource::collection($posts)
            ->additional(['meta'])
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * GET /api/posts/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        return response()->json(new PostResource($post), Response::HTTP_OK);
    }

    /**
     * POST /api/posts
     */
    public function store(PostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        $post = Post::create($data);

        if ($request->has('categories')) {
            $post->categories()->sync($request->input('categories'));
        }

        return (new PostResource($post))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * PUT/PATCH /api/posts/{slug}
     */
    public function update(PostRequest $request, string $slug): JsonResponse
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $data = $request->validated();

        if ($post->title !== $data['title']) {
            $data['slug'] = Str::slug($data['title']) . '-' . uniqid();
        }

        $post->update($data);

        if ($request->has('categories')) {
            $post->categories()->sync($request->input('categories'));
        }

        return response()->json(new PostResource($post), Response::HTTP_OK);
    }

    /**
     * DELETE /api/posts/{slug}
     */
    public function destroy(string $slug): JsonResponse
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], Response::HTTP_OK);
    }
}
