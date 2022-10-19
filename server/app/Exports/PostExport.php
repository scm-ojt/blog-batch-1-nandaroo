<?php

namespace App\Exports;

use App\Models\Post;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PostExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    use Exportable;

    public function __construct($keyword){
        $this->keyword = $keyword;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Post::where('title','like', '%'. $this->keyword .'%')->with('categories')->with('images')->get();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["id", "user_id", "images", "title", "body","categories", "created_at", "updated_at"];
    }

    
    /**
    * @var Invoice $invoice
    */
    public function map($post): array
    {
        return [
            $post->id,
            $post->user_id,
            $post->images()->implode('image',', '),
            $post->title,
            $post->body,
            $post->categories()->implode('name', ', '),
            $post->created_at,
            $post->updated_at            
        ];
    }
}
