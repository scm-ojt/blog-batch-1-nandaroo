<?php

namespace App\Imports;

use App\Models\Post;
use App\Models\Category;
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
        if($row['actions'] == 'create'){

        } else if($row['actions'] == 'update'){

        } else if($row['actions'] == 'delete'){
            
        }
        $post = Post::create([
            'user_id' => $row['user_id'],
            'image' => $row['image'],
            'title' => $row['title'],
            'body' => $row['body']
        ]);
        $categories_arr = explode(", ", $row['categories']);
        $id_arr = Category::whereIn('name', $categories_arr)->pluck('id');
        $post->categories()->sync($id_arr);
        return $post;
    }
}
