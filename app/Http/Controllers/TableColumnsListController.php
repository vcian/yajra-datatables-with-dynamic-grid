<?php

namespace App\Http\Controllers;

use App\Http\Requests\ColumnFormRequest;
use App\Repositories\ColumnRepository;

class TableColumnsListController extends Controller
{

    /**
     * @var columnRepository
     */
    protected $facilityRepository;

    /**
     * @param columnRepository $columnRepository
     */
    public function __construct(ColumnRepository $columnRepository)
    {

        $this->columnRepository = $columnRepository;

    }
    /**
     * get a column list of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getColumnList($slug)
    {
        $columns =  \App\TableColumnsList::whereSlug($slug)->where('user_id',1)->active()->first();
        return response()->json(['success' => true, 'columns' => $columns],200);
    }

    /**
     * get a column list of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateColumns(ColumnFormRequest $request,$slug)
    {
        try {
            $columns = $this->columnRepository->updateColumns($request,$slug);
            if ($columns) {
                $response = [
                    'status' =>'columns successfully updated',
                    'code' => 200,
                    'data' => $columns
                ];
            } else {
                $response = [
                    'status' => 'something wrong',
                    'code' => 500,
                    'message' => 'Error',
                    'data' => []
                ];
            }
            return response()->json($response, $response['code']);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
            return [];
        }
    }
}
