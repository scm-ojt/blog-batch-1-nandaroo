<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\PostUpdateRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::with('user')->with('categories')->get();
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        info('request____');
        info($request);
        $imageName = time() . '.' . $request->image->extension();

        // Storage Folder
        $request->image->move(storage_path('app/public/img/posts'), $imageName);
        $post = Post::create([
            'user_id' => Auth::user()->id,
            'image' => $imageName,
            'title' => $request->title,
            'body' => $request->body
        ]);
        $post->categories()->sync($request->categories);
        return response([
            'message' => 'success',
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
        $post = Post::find($id);
        return response()->json(new PostResource($post));
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
    public function update(PostUpdateRequest $request, $id)
    {
        $post = Post::find($id);
        if ($request->file('image')) {
            if (File::exists(storage_path('app/public/img/posts/') . $post->image)) {
                //File::delete('storage/img/posts/' . $post->image);
                File::delete(storage_path('app/public/img/posts/') . $post->image);
            }
            $imageName = time() . '.' . $request->file('image')->extension();

        // Storage Folder
        $request->image->move(storage_path('app/public/img/posts'), $imageName);
            $post->image = $imageName;
        }

        $post->title = $request->title;
        $post->body = $request->body;
        $post->categories()->sync($request->categories);
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

        if (File::exists(storage_path('app/public/img/posts/') . $post->image)) {
            //File::delete('storage/img/posts/' . $post->image);
            File::delete(storage_path('app/public/img/posts/') . $post->image);
        }
        
        
        $post->categories()->sync([]);
        $post->delete();
        return response([
            'message' => 'Post deleted!'
        ]);
    }
}
