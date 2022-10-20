<?php

namespace App\Imports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoryImport implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        Validator::make($rows->toArray(), [
            '*.name' => 'required',
            '*.actions' => 'required'
        ])->validate();
        foreach ($rows as $row) {
            info($row);
            if ($row['actions'] == 'create') {
                Category::create([
                    'name'     => $row['name']
                ]);
            } else if ($row['actions'] == 'update') {
                $category = Category::find($row['id']);
                $category->name = $row['name'];
                $category->save();
            } else if ($row['actions'] == 'delete') {
                Category::where('id', $row['id'])->delete();
            }
        }
    }
}
