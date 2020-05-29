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
            $fieldwidth = [];
            $input = $request->all();
            foreach ($input['fields'] as $key=>$va) {
                if(isset($input['widthData'][$key]) && !empty($input['widthData'][$key])) {
                    $fieldwidth[] = $input['widthData'][$key];
                } else {
                    $fieldwidth[] = '118';
                }
            }
            $input['fields'] = implode(',',array_unique($input['fields']));
            $input['fieldname'] = implode(',',array_unique($input['fieldname']));
            $input['fieldwidth'] = implode(',',$fieldwidth);
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
