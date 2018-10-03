<?php

namespace JianAstrero\JuggerAPI\Http\Controllers;

use App\Http\Controllers\Controller;
use function count;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use JianAstrero\JuggerAPI\Models\JuggerRoute;
use JianAstrero\JuggerAPI\Traits\CanMutate;
use JianAstrero\JuggerAPI\Traits\HasTable;
use function strlen;
use function substr;

class JuggerController extends Controller
{
    public function index(Request $request) {
        return view('jugger-api::index');
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->error(422, $validator->errors());
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token =  $user->createToken('JuggerAPI');
            return response()->json(['code'=> 200, 'token' => $token->accessToken], 200);
        } else {
            return $this->invalidInput();
        }
    }

    public function home(Request $request) {
        $path = app_path();

        $models = $this->getModels($path);
        $modelColumnPairs = array();

        foreach ($models as $model) {
            $cols = $this->getModelColumns($model);
            $modelColumnPairs[$model] = $cols;
        }

        $versions = JuggerRoute::select('version')->groupBy('version')->get();

        $versions[] = array(
            'version' => count($versions)+1
        );

        $data = array(
            'models' => $modelColumnPairs,
            'versions' => $versions
        );

        return view('jugger-api::home', $data);
    }

    public function logout(Request $request) {
        return view('jugger-api::logout');
    }

    public function logoutApi(Request $request)
    {
        $user = Auth::user();

        $revoked = $user->token()->revoke();
        foreach ($user->tokens() as $token) {
            $revoked = $revoked && $token->revoke();
        }

        return response()->json(['code'=> 200, 'message' => 'Logout successful'], 200);
    }

    private function getModels($path){
        $out = [];
        $results = scandir($path);
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = explode('.',$result)[0];
            $filepath = $path . '/' . $result;
            if (!is_dir($filepath)) {
                if ($this->isReadyForJuggerAPI($filename)) {
                    $out[] = $filename;
                }
            }
        }
        return $out;
    }

    private function isReadyForJuggerAPI($model) {
        if ($model == 'CanMutate' || $model == 'HasTable')
            return false;

        return in_array(CanMutate::class, array_keys((new \ReflectionClass('App\\'.$model))->getTraits())) &&
            in_array(HasTable::class, array_keys((new \ReflectionClass('App\\'.$model))->getTraits()));
    }

    private function getModelColumns($model) {
        return ('App\\'.$model)::getTableColumnsStatically();
    }

    private function notFound() {
        return response()->json($this->errorArray(404, 'Resource not found'), 404);
    }

    private function notAllowed() {
        return response()->json($this->errorArray(405, 'Method not allowed'), 404);
    }

    private function invalidInput() {
        return response()->json($this->errorArray(422, 'Missing or Invalid Input'), 422);
    }

    private function error($errorCode, $message) {
        return response()->json($this->errorArray($errorCode, $message), $errorCode);
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
