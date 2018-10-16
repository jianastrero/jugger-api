<?php

namespace JianAstrero\JuggerAPI\Http\Controllers\API;

use Exception;
use function explode;
use function file_put_contents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use function is_string;
use JianAstrero\JuggerAPI\Models\JuggerAdmin;
use JianAstrero\JuggerAPI\Models\JuggerRoute;
use function json_decode;
use function json_encode;
use function response;
use function str_plural;
use function str_singular;
use function substr;

class JuggerAPIController extends Controller
{
    public function __construct()
    {
        $user = Auth::guard('juggeradmin-api')->user();

        if ($user !== null && $user instanceof JuggerAdmin) {
            if (!config('app.debug')) {
                $this->notFound()->send();
                die();
            }
            $this->middleware('auth:juggeradmin-api');
        } else {
            $this->middleware('auth:api');
        }
    }

    public function getList(Request $request, $version, $slug) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->where('version', preg_replace("/[^0-9,.]/", "", $version))->first();

        if ($juggerRoute == null || !$juggerRoute->list || $slug != str_plural($slug)) {
            return $this->notFound();
        }

        $modelName = $juggerRoute->model_name;

        $selectColumns = $juggerRoute->columns;
        if ($juggerRoute->column_override && $request->has('cols')) {
            $selectColumns = explode(',', $request->cols);
        }
        $list = $modelName::select(...$selectColumns);

        if ($request->has('q')) {
            $queries = explode(',', $request->q);
            $tableColumns = $modelName::getTableColumnsStatically();
            $first = true;
            foreach ($tableColumns as $column) {
                foreach ($queries as $query) {
                    $mCol = $column;
                    $mQuery = $query;
                    if (strpos($query, ':') !== false) {
                        $mAsd = explode(':', $query);
                        $mCol = $mAsd[0];
                        $mQuery = $mAsd[1];
                    }
                    if ($first) {
                        $list = $list->where($mCol, 'LIKE', '%'.$mQuery.'%');
                        $first = false;
                    } else
                        $list = $list->orWhere($mCol, 'LIKE', '%'.$mQuery.'%');
                }
            }
        }

        $sortColumn = $juggerRoute->sort;
        $sortAscending = 'asc';
        if ($juggerRoute->sort_override && $request->has('sort')) {
            $sortColumn = $request->sort;
        }

        if ($sortColumn[0] == '-') {
            $sortAscending = 'desc';
            $sortColumn = substr($sortColumn, 1);
        } else if ($sortColumn[0] == '+') {
            $sortAscending = 'asc';
            $sortColumn = substr($sortColumn, 1);
        }

        $list = $list->orderBy($sortColumn, $sortAscending);

        // override pagination
        $paginateCount = $juggerRoute->paginate_item_count;
        if($request->has('items')) {
            $paginateCount = $request->items;
        }

        $list = $list->paginate($paginateCount);

        foreach ($list as $item) {
            $item->mutate();
        }

        return response()->json($list, 200);
    }

    public function item(Request $request, $version, $slug, $id) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->where('version', preg_replace("/[^0-9,.]/", "", $version))->first();

        if ($juggerRoute == null || !$juggerRoute->read || $slug != str_singular($slug)) {
            return $this->notFound();
        }

        $modelName = $juggerRoute->model_name;

        $selectColumns = $juggerRoute->columns;
        if ($juggerRoute->column_override && $request->has('cols')) {
            $selectColumns = explode(',', $request->cols);
        }

        $item = $modelName::select(...$selectColumns)->find($id);

        if ($item == null) {
            return $this->notFound();
        }

        $item->mutate();

        return response()->json($item, 200);
    }

    public function create(Request $request, $version, $slug) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->where('version', preg_replace("/[^0-9,.]/", "", $version))->first();

        if ($juggerRoute == null || !$juggerRoute->create || $slug != str_plural($slug)) {
            return $this->notFound();
        }

        $modelName = $juggerRoute->model_name;

        try {
            $item = $modelName::createMutated($request);
        } catch (Exception $e) {
            return $this->invalidInput();
        }

        $selectColumns = $juggerRoute->columns;
        if ($juggerRoute->column_override && $request->has('cols')) {
            $selectColumns = explode(',', $request->cols);
        }

        $item = $modelName::select(...$selectColumns)->find($item->id)->mutate();

        return response()->json($item, 201);
    }

    public function updateMultiple(Request $request, $version, $slug) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->where('version', preg_replace("/[^0-9,.]/", "", $version))->first();

        if ($juggerRoute == null || !$juggerRoute->update || $slug != str_plural($slug)) {
            return $this->notFound();
        }

        $modelName = $juggerRoute->model_name;

        if (!$request->has('list')) {
            return $this->invalidInput();
        }

        $list = $request->list;
        if (is_string($request->list)) {
            $list = json_decode($request->list);
        } else {
            return $this->invalidInput();
        }

        $updateCount = 0;
        $rows = array();

        foreach ($list as $item) {
            try {
                $row = $modelName::find($item->id);
                if ($row == null) {
                    return $this->notFound();
                }
                $row->fillMutatedMultiple((array)$item);
                $rows[] = $row;

                $updateCount++;
            } catch (Exception $e) {
                return $this->invalidInput();
            }
        }

        foreach ($rows as $row) {
            $row->save();
        }

        return response()->json(array('update_count'=>$updateCount), 202);
    }

    public function update(Request $request, $version, $slug, $id) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->where('version', preg_replace("/[^0-9,.]/", "", $version))->first();

        if ($juggerRoute == null || !$juggerRoute->update || $slug != str_singular($slug)) {
            return $this->notFound();
        }

        $modelName = $juggerRoute->model_name;

        $item = $modelName::find($id);
        if ($item == null) {
            return $this->notFound();
        }

        try {
            $item->fillMutated($request);
            $item->save();
        } catch (Exception $e) {
            return $this->invalidInput();
        }

        $selectColumns = $juggerRoute->columns;
        if ($juggerRoute->column_override && $request->has('cols')) {
            $selectColumns = explode(',', $request->cols);
        }

        $item = $modelName::select(...$selectColumns)->find($id)->mutate();

        return response()->json($item, 202);
    }

    public function deleteMultiple(Request $request, $version, $slug) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->where('version', preg_replace("/[^0-9,.]/", "", $version))->first();

        if ($juggerRoute == null || !$juggerRoute->delete || $slug != str_plural($slug)) {
            return $this->notFound();
        }

        $modelName = $juggerRoute->model_name;

        if (!$request->has('ids')) {
            return $this->invalidInput();
        }

        $ids = $request->ids;
        if (is_string($request->ids)) {
            $ids = json_decode($request->ids);
        }

        $deleteCount = 0;
        $rows = array();

        foreach ($ids as $id) {
            $row = $modelName::find($id);
            if ($row == null) {
                return $this->notFound();
            }
            $rows[] = $row;

            $deleteCount++;
        }

        foreach ($rows as $row) {
            $row->delete();
        }

        return response()->json(array('delete_count'=>$deleteCount), 204);
    }

    public function delete(Request $request, $version, $slug, $id) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->where('version', preg_replace("/[^0-9,.]/", "", $version))->first();

        if ($juggerRoute == null || !$juggerRoute->delete || $slug != str_singular($slug)) {
            return $this->notFound();
        }

        $modelName = $juggerRoute->model_name;

        $row = $modelName::find($id);
        if ($row == null) {
            return $this->notFound();
        }
        $row->delete();

        return response()->json($row, 204);
    }

    public function notFound() {
        return response()->json($this->errorArray(404, 'Resource not found'), 404);
    }

    public function notAllowed() {
        return response()->json($this->errorArray(405, 'Method not allowed'), 404);
    }

    public function invalidInput() {
        return response()->json($this->errorArray(422, 'Input is invalid or missing'), 422);
    }

    private function errorArray($errorCode, $message) {
        return [
            'errors'=>
                [
                    'message' => $message,
                    'code' => $errorCode
                ]
        ];
    }
}
