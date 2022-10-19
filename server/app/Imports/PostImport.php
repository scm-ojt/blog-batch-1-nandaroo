<?php

namespace App\Imports;

use App\Models\Post;
use App\Models\Category;
use App\Models\PostImage;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PostImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $post = Post::create([
            'user_id' => $row['user_id'],
            'title' => $row['title'],
            'body' => $row['body']
        ]);
        $categories_arr = explode(", ", $row['categories']);
        $id_arr = Category::whereIn('name', $categories_arr)->pluck('id');
        $post->categories()->sync($id_arr);

        $img_arr=explode(", ", $row['images']);
        foreach($img_arr as $image){
            PostImage::create([
                'post_id'=> $post->id,
                'image' => $image
            ]);
        }
        return $post;
    }
}
