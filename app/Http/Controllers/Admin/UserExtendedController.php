<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Models\{User, Section, Theme};

class UserExtendedController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup() {
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/user_extended');
        $this->crud->setEntityNameStrings('Расширенная панель управления пользователями пользователей', 'Расширенная панель управления пользователями');
        $this->crud->setListView('custom_admin.user-extended');
    }

    protected function setupListRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/', [
            'as'        => $routeName.'.index',
            'uses'      => $controller.'@index',
            'operation' => 'list',
        ]);

        Route::post($segment.'/search', [
            'as'        => $routeName.'.search',
            'uses'      => $controller.'@search',
            'operation' => 'list',
        ]);

        Route::get($segment.'/{id}/details', [
            'as'        => $routeName.'.showDetailsRow',
            'uses'      => $controller.'@showDetailsRow',
            'operation' => 'list',
        ]);

        Route::get($segment.'/test', [
            'as'        => $routeName.'.test',
            'uses'      => $controller.'@test',
            'operation' => 'list',
        ]);

        Route::post($segment.'/get_data_for_user_extended', [
            'as'        => $routeName.'.getDataForUserExtended',
            'uses'      => $controller.'@getDataForUserExtended',
            'operation' => 'list',
        ]);

        Route::post($segment.'/set_permition', [
            'as'        => $routeName.'.setPermition',
            'uses'      => $controller.'@setPermition',
            'operation' => 'list',
        ]);

        Route::post($segment.'/set_permitions', [
            'as'        => $routeName.'.setPermitions',
            'uses'      => $controller.'@setPermitions',
            'operation' => 'list',
        ]);
    }

    public function getDataForUserExtended(Request $request) {
        //Верифицируем current_user
        $user_id = (int)$request->current_user;
        if (gettype($user_id)!="integer") {
            $currentUser = User::select('id')->orderBy('id', 'asc')->get();
            $user_id = $currentUser[0]->id; 
        } else {
            $currentUser = User::where('id',$user_id)->select('id')->first();
            if ($currentUser) {
                $user_id = $currentUser->id;
            } else {
                $currentUser = User::select('id')->orderBy('id', 'asc')->get();
                $user_id = $currentUser[0]->id; 
            }
        }

        //Верификация current_section
        $current_section = (int)$request->current_section;
        if (gettype($current_section)!="integer") {
            $currentSection = Section::select('id')->orderBy('id', 'asc')->get();
            $current_section = $currentSection[0]->id; 
        } else {
            $currentSection = Section::where('id',$current_section)->select('id')->first();
            if ($currentSection) {
                $current_section = $currentSection->id;
            } else {
                $currentSection = Section::select('id')->orderBy('id', 'asc')->get();
                $current_section = $currentSection[0]->id; 
            }
        }
        
        //Пользователи
        $users = User::select('id','name','email')->get();

        //Разделы
        $sections = Section::select('id','name')->orderBy('id', 'asc')->get();

        //Темы с разрешениями по пользователю
        $user = User::where('id',$user_id)->select('allowed_themes')->first();
        $permitions = json_decode($user->allowed_themes);

        $themes = Theme::where('section',$current_section)
                        ->select('id','sort','name')
                        ->orderBy('sort','asc')
                        ->get();

        $themes_with_permissions = [];

        for ($i=0;$i<count($themes);$i++) {
            $permition=false;
            
            if ($permitions) {
                for ($j=0;$j<count($permitions);$j++) {
                    if ($permitions[$j]->id == $themes[$i]->id) {
                        $permition = $permitions[$j]->allowed;
                        break;
                    }
                }
            }


            $themes_with_permissions[$i] = [
                'theme_id'=>$themes[$i]->id,
                'theme'=>$themes[$i]->name,
                'sort'=>$themes[$i]->sort,
                'permition'=>$permition
            ];
        }

        //Результирующий набор данных
        $data = [
            'current_user'=>$user_id,
            'current_section'=>$current_section,
            'status'=>'success',
            'users'=>$users,
            'sections'=>$sections,
            'themes'=>$themes_with_permissions,
        ];
        return response()->json($data, 200);
    }

    public function setPermition(Request $request) {

        $res = User::where('id',$request->current_user)->select('allowed_themes')->first();

        if (!$res) {
            return [
                'status'=>'error',
                'message'=>'Something went wrong.'
            ];
        }
        $permitions = json_decode($res->allowed_themes);
        $isDone = false;//Установили разрешение или нет
        
        if (gettype($permitions)=='array') {
            for ($i=0;$i<count($permitions);$i++) {
                if ($permitions[$i]->id == $request->theme_id) {
                    $permitions[$i]->allowed = $request->permition;
                    $isDone = true;
                    break;
                }
            }
    
            if (!$isDone) {
                array_push($permitions,[
                    "id"=>$request->theme_id,
                    "allowed"=>$request->permition
                ]);
            }            
        } else {
            $permitions = [
                [
                    "id"=>$request->theme_id,
                    "allowed"=>$request->permition
                ]
            ];
        }

        $jsonData = json_encode($permitions);

        $res = User::where('id',$request->current_user)->update(['allowed_themes'=>$jsonData]);

        if ($res) {
            $status = 'success';
            $message = 'That is ok';
        } else {
            $status = 'error';
            $message = 'Somethink went wrong';  
        }

        $data = [
            'status'=>$status,
            'message'=>$message
        ];

        return $data;
    }

    public function setPermitions(Request $request) {
        
        $user = User::where('id',$request->current_user)->select('allowed_themes')->first();

        if (!$user) {
            return [
                'status'=>'error',
                'message'=>'Something went wrong.'
            ];
        }

        $permitions = json_decode($user->allowed_themes);

        $themes = $request->themes;

        for ($j=0;$j<count($themes);$j++) {

            $isDone = false;//Установили разрешение или нет
        
            if ($permitions!=null) {
                for ($i=0;$i<count($permitions);$i++) {

                    if (isset($permitions[$i]->id)) {
                        if ($permitions[$i]->id == $themes[$j]) {
                            $permitions[$i]->allowed = $request->permition;
                            $isDone = true;
                            break;
                        }
                    } else {
                        if ($permitions[$i]['id'] == $themes[$j]) {
                            $permitions[$i]['allowed'] = $request->permition;
                            $isDone = true;
                            break;
                        }
                    }
                }
        
                if (!$isDone) {
                    array_push($permitions,[
                        "id"=>$themes[$j],
                        "allowed"=>$request->permition
                    ]);
                }            
            } else {
                $permitions = [
                    (object)[
                        "id"=>$themes[$j],
                        "allowed"=>$request->permition
                    ]
                ];
            }    
        }

        $jsonData = json_encode($permitions);

        $user = User::where('id',$request->current_user)->update(['allowed_themes'=>$jsonData]);

        if ($user) {
            $status = 'success';
            $message = 'That is ok';
        } else {
            $status = 'error';
            $message = 'Somethink went wrong';  
        }

        $data = [
            'status'=>$status,
            'message'=>$message
        ];

        return $data;

    }
}
