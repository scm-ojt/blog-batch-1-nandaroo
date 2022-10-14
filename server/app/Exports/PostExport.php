<?php

namespace App\Exports;

use App\Models\Post;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PostExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        return Post::where('title','like', '%'. $this->keyword .'%')->get();
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array
    {
        return ["id", "user_id", "image", "title", "body", "created_at", "updated_at"];
    }
}
