<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Post;
use App\Http\Resources\Post as PostResource;

class PostController extends Controller
{

    // public function __construct()
    // {
    //     $this->middleware('auth', ['except' => ['index', 'show']]);
    // }

    /**
     * Display a listing of the post.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::join('users', 'users.id', '=', 'posts.user_id')
        ->join('tags', 'tags.id', '=', 'posts.tag_id')
        ->select('users.name', 'users.id as user_id', 'posts.id', 'posts.title', 'posts.body', 'posts.created_at', 'tags.title as tag', 'tags.id as tag_id')
        ->getQuery()->get();

        return PostResource::collection($posts);
    }

    /**
     * Store a newly created post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = $request->isMethod('put') ? Post::findOrFail($request->post_id) : new Post;

        $post->id = $request->input('post_id');
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = $request->input('user_id');
        $post->tag_id = $request->input('tag_id');

        if($post->save()) {
            return new PostResource($post);
        }
    }

    /**
     * Display the specified post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::join('users', 'users.id', '=', 'posts.user_id')
        ->join('tags', 'tags.id', '=', 'posts.tag_id')
        ->select('users.name', 'users.id as user_id', 'posts.id', 'posts.title', 'posts.body', 'posts.created_at', 'tags.title as tag', 'tags.id as tag_id')
        ->where('posts.id', '=', $id)->getQuery()->first();

        return new PostResource($post);
    }

    /**
     * Remove the specified post from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        if($post->delete()) {
            return new PostResource($post);
        }
    }
}
