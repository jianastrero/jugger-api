<?php

namespace JianAstrero\JuggerAPI\Http\Controllers\API;

use Exception;
use function explode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use function is_string;
use JianAstrero\JuggerAPI\Models\JuggerRoute;
use function json_decode;
use function response;
use function str_plural;
use function str_singular;

class JuggerAPIController extends Controller
{
    public function getList(Request $request, $slug) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->first();

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
                    if ($first) {
                        $list = $list->where($column, 'LIKE', '%'.$query.'%');
                        $first = false;
                    } else
                        $list = $list->orWhere($column, 'LIKE', '%'.$query.'%');
                }
            }
        }

        $sortColumns = $juggerRoute->sort;
        if ($juggerRoute->sort_override && $request->has('sort')) {
            $sortColumns = explode(',', $request->sort);
        }

        for ($i = 0; $i < count($sortColumns); $i++) {
            $list = $list->orderBy($sortColumns[$i], $sortColumns[++$i]);
        }

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

    public function item(Request $request, $slug, $id) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->first();

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

    public function create(Request $request, $slug) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->first();

        if ($juggerRoute == null || !$juggerRoute->create || $slug != str_plural($slug)) {
            return $this->notFound();
        }

        $modelName = $juggerRoute->model_name;

        $inputs = $request->input();

        try {
            $item = $modelName::createMutated($inputs);
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

    public function updateMultiple(Request $request, $slug) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->first();

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
                $row->fillMutated((array)$item);
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

    public function update(Request $request, $slug, $id) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->first();

        if ($juggerRoute == null || !$juggerRoute->update || $slug != str_singular($slug)) {
            return $this->notFound();
        }

        $modelName = $juggerRoute->model_name;

        $item = $modelName::find($id);
        if ($item == null) {
            return $this->notFound();
        }

        try {
            $item->fillMutated((array)$request->input());
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

    public function deleteMultiple(Request $request, $slug) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->first();

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

    public function delete(Request $request, $slug, $id) {
        $juggerRoute = JuggerRoute::where('slug', str_plural($slug))->first();

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
