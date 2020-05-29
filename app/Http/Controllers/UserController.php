<?php

namespace App\Http\Controllers;

use App\TableColumnsList;
use App\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $columnsData = TableColumnsList::where('slug','users')->where('user_id','1')->first();
        $checkedFields = explode(',',$columnsData->fields);
        if ($request->ajax()) {
            if(!empty($checkedFields)) {
                $checkedFields = array_diff($checkedFields,['DT_RowIndex','action']);
            }
            $data = User::select($checkedFields)->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">View</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('users',compact('checkedFields'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $columnsData = TableColumnsList::where('slug','users')->where('user_id','1')->first();
        $checkedFields = explode(',',$columnsData->fields);
        if ($request->ajax()) {
            if(!empty($checkedFields)) {
                $checkedFields = array_diff($checkedFields,['DT_RowIndex','action']);
            }
            $data = User::select($checkedFields)->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">View</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('export',compact('checkedFields'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function drags(Request $request)
    {
        $config = [
            'DT_RowIndex' => 'No',
            'name'=>'Name',
            'email'=> 'Email',
            'action' => 'Action',
        ];
        $columnsData = TableColumnsList::where('slug','users')->where('user_id','1')->first();
        if(empty($columnsData)) {
            $checkedFields = ['DT_RowIndex','name','email','action'];
            $fieldwidth = '118,118,118,118';
        } else {
            $checkedFields = explode(',',$columnsData->fields);
            $fieldwidth = $columnsData->fieldwidth;
        }
        if ($request->ajax()) {
            if(!empty($checkedFields)) {
                $checkedFields = array_diff($checkedFields,['DT_RowIndex','action']);
            }
            $data = User::select($checkedFields)->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">View</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('drag',compact('checkedFields','fieldwidth','config'));
    }
}
