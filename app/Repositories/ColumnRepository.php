<?php

namespace App\Repositories;

use App\Repositories\BaseRepository;
use App\TableColumnsList;
use Log,DB,Validator;
use Illuminate\Support\Facades\Storage;
class ColumnRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = TableColumnsList::class;

    public function updateColumns($request,$slug)
    {
        try {
            $input = $request->all();
            $input['fields'] = implode(',',$input['fields']);
            $input['fieldname'] = implode(',',$input['fieldname']);
            $input['slug'] = $slug;
            $input['status'] = '1';
            $columnData = TableColumnsList::where('slug',$slug)->where('user_id','1')->first();
            DB::beginTransaction();
            if(empty($columnData)) {
                $data = TableColumnsList::create($input);
            } else {
                $data = $columnData->fill($input)->save();
            }
            if(!empty($data)) {
                DB::commit();
                return $data;
            }
            return false;
        } catch (\Exception $ex) {
            dd($ex->getMessage());
            Log::error($ex->getMessage());
            DB::rollBack();
        }
        return [];
    }
}
