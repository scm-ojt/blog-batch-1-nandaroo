<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();
        return response([
            'data' => $posts
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        $imageName = time() . '.' . $request->image->extension();

        // Public Folder
        $request->image->move(public_path('img/posts'), $imageName);
        $post = Post::create([
            'user_id' => Auth::user()->id,
            'image' => $imageName,
            'title' => $request->title,
            'body' => $request->body
        ]);

        return response([
            'message' => 'Post created',
            'data' => $post
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if ($request->image) {
            if (File::exists('img/posts/' . $post->image)) {
                File::delete('img/posts/' . $post->image);
            }
            $imageName = time() . '.' . $request->image->extension();
            // Public Folder
            $request->image->move(public_path('img/posts'), $imageName);
            $post->image = $imageName;
        }

        $post->title = $request->title;
        $post->body = $request->body;
        $post->save();

        return response([
            'message' => 'Post Updated',
            'data' => $post
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if (File::exists('img/posts/' . $post->image)) {
            File::delete('img/posts/' . $post->image);
        }
        $post->delete();
        return response([
            'message' => 'Post deleted!'
        ]);
    }
}
