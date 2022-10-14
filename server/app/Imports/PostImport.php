<?php

namespace App\Imports;

use App\Models\Post;
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
        $post= Post::create([
            'user_id'=> $row['user_id'],
            'image' => $row['image'],
            'title' => $row['title'],
            'body' => $row['body']
        ]);
        $post->categories()->sync(explode(",",$row['categories']));
        return $post;
    }
}
