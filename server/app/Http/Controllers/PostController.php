<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Exports\PostExport;
use App\Imports\PostImport;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Requests\ImportRequest;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PostUpdateRequest;
use App\Models\PostImage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Post::with('user')->with('categories')->with('images');
        if ($request->search) {
            $query->where('posts.title', 'like', '%' . request()->input('search') . '%');
        }
        $posts = $query->get();
        return response()->json(PostResource::collection($posts));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $post = Post::create([
            'user_id' => Auth::user()->id,
            'title' => $request->title,
            'body' => $request->body
        ]);
        foreach ($request->file('image') as $image) {
            $imageName=time() .'_'. $image->getClientOriginalName();
            // Storage Folder
            $image->move(storage_path('app/public/img/posts'), $imageName);
            PostImage::create([
                'post_id'=>$post->id,
                'image'=>$imageName
            ]);
        }
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
        $post = Post::with('user')->with('categories')->with('images')->find($id);
        return response()->json($post);
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
        $post = Post::with('images')->find($id);
        info($post->images);
        //authorize user to update the post
        /* if (! Gate::allows('post-actions', $post)) {
            abort(403);
        } */
        if ($request->user()->cannot('update', $post)) {
            abort(403);
        }
        if ($request->file('image')) {
            foreach($post->images as $item){
                if (File::exists(storage_path('app/public/img/posts/') . $item->image)) {
                    File::delete(storage_path('app/public/img/posts/') . $item->image);
                }
            }
            PostImage::where('post_id', $id)->delete();
            foreach ($request->file('image') as $image) {
                $imageName=time() .'_'. $image->getClientOriginalName();
                // Storage Folder
                $image->move(storage_path('app/public/img/posts'), $imageName);
                PostImage::create([
                    'post_id'=>$post->id,
                    'image'=>$imageName
                ]);
            }           
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
    public function destroy(Request $request, $id)
    {
        $post = Post::with('images')->find($id);
        /* if (! Gate::allows('post-actions', $post)) {
            abort(403);
        } */

        //authorize user to delete the post
        if ($request->user()->cannot('delete', $post)) {
            abort(403);
        }
        foreach($post->images as $item){
            if (File::exists(storage_path('app/public/img/posts/') . $item->image)) {
                File::delete(storage_path('app/public/img/posts/') . $item->image);
            }
        }

        $post->categories()->sync([]);
        Comment::where('post_id', $post->id)->delete();
        $post->delete();
        return response([
            'message' => 'Post deleted!'
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new PostExport($request->keyword), 'posts.xlsx');
    }

    public function import(ImportRequest $request)
    {
        Excel::import(new PostImport, request()->file('file'));
        return response(200);
    }
}
