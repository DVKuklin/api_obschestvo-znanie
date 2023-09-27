<?php

namespace App\Http\Controllers\Admin;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Backpack\CRUD\app\Library\Widget;

use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

use App\Models\{Section, Paragraph, Theme};

class ParagraphController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup() {
        $this->crud->setModel(\App\Models\Paragraph::class);
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/myparagraph');
        $this->crud->setEntityNameStrings('абзац', 'абзацы');

        $this->crud->setListView('custom_admin.paragraphs-edit');

        Widget::add()->type('style')->content('admin_assets/css/paragraphs-edit.css');
        Widget::add()->type('style')->content('css/css_for_paragraphs.css');
     }

         /**
     * Define which routes are needed for this operation.
     *
     * @param  string  $segment  Name of the current entity (singular). Used as first URL segment.
     * @param  string  $routeName  Prefix of the route name.
     * @param  string  $controller  Name of the current CrudController.
     */
    protected function setupListRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/', [
            'as'        => $routeName.'.index',
            'uses'      => $controller.'@index',
            'operation' => 'list',
        ]);

        Route::post($segment.'/get_data_for_paragraphs_edit', [
            'as'        => $routeName.'.getDataForParagraphsEdit',
            'uses'      => $controller.'@getDataForParagraphsEdit',
            'operation' => 'list',
        ]);

        Route::post($segment.'/add_paragraph', [
            'as'        => $routeName.'.addParagraph',
            'uses'      => $controller.'@addParagraph',
            'operation' => 'list',
        ]);

        Route::post($segment.'/delete_paragraph', [
            'as'        => $routeName.'.deleteParagraph',
            'uses'      => $controller.'@deleteParagraph',
            'operation' => 'list',
        ]);

        Route::post($segment.'/save_paragraphs', [
            'as'        => $routeName.'.saveParagraphs',
            'uses'      => $controller.'@saveParagraphs',
            'operation' => 'list',
        ]);
    }

    public function getDataForParagraphsEdit(Request $request) {
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

        $sections = Section::orderBy('sort','asc')->select('id','name')->get();

        //Верификация current_theme
        $current_theme = (int)$request->current_theme;

        if (gettype($current_theme)!="integer") {
            $currentTheme = Theme::select('id')
                                    ->where('section',$current_section)
                                    ->get();            
            $current_theme = $currentTheme[0]->id; 
        } else {
            $currentTheme = Theme::where('id',$current_theme)
                                    ->where('section',$current_section)
                                    ->select('id')
                                    ->first();
            if ($currentTheme) {
                $current_theme = $currentTheme->id;
            } else {
                $currentTheme = Theme::select('id')
                                ->where('section',$current_section)
                                ->orderBy('sort')
                                ->get();
                if ($currentTheme) {
                    $current_theme = $currentTheme[0]->id;
                } else {
                    $current_theme = null;
                }
            }
        }
        
        $themes = Theme::where('section','=',$current_section)
                        ->select('id','sort','name')
                        ->orderBy('sort')
                        ->get();

        //Достаем параграфы

        if ($current_theme == null) {
            $paragraphs = null;
        } else {
            $paragraphs = Paragraph::where('theme',$current_theme)
                                    ->select('id','content','sort')
                                    ->orderBy('sort','asc')
                                    ->get();
            if (count($paragraphs)==0) $paragraphs = null;
        }

        // Результирующий набор данных
        $data = [
            'status'=>'success',
            'current_section'=>$current_section,
            'current_theme'=>$current_theme,
            'themes'=>$themes,
            'sections' => $sections,
            'paragraphs' => $paragraphs
        ];
        return $data;
    }

    public function addParagraph(Request $request) {

        $theme = Theme::where('id',$request->theme)->select('id')->first();

        if (!$theme) {
            return [
                'status'=>'error',
                'message'=>'Theme not found'
            ];
        }

        $new_paragraph_id = null;

        try {
            $new_paragraph_id = Paragraph::insertGetId([
                'theme'=>$theme->id,
            ]);
            if (!$new_paragraph_id) {
                return [
                    'status'=>'BDError'
                ];
            }
        }catch(\Exception $e){
            return [
                'status'=>'BDException'
            ];
        }


        $paragraphs = Paragraph::where('theme',$request->theme)
                                ->select('id')
                                ->orderBy('sort','asc')
                                ->get();

        if (!$paragraphs) {
            return [
                'status'=>'error',
                'message'=>'Some thing went wrong.'
            ];
        }

        try {
            $index = 1;

            foreach ($paragraphs as $paragraph) {

                if ($new_paragraph_id == $paragraph->id) {
                    continue;
                }
    
                if ($index == (int)$request->sort) {
                    if ($request->position == "above") {
                        Paragraph::where('id',$new_paragraph_id)->update(['sort'=>$index]);
                        $index++;
                        Paragraph::where('id',$paragraph->id)->update(['sort'=>$index]);
                    } else {
                        Paragraph::where('id',$paragraph->id)->update(['sort'=>$index]);
                        $index++;
                        Paragraph::where('id',$new_paragraph_id)->update(['sort'=>$index]);
                    }
                } else {
                    Paragraph::where('id',$paragraph->id)->update(['sort'=>$index]);
                }
    
                $index++;
            }
    
            return [
                'status'=>'success'
            ];
    
        } catch (\Exception $e) {
            return [
                'status'=>'error',
                'message'=>'Ошибка базы данных'
            ];
        }
    }

    public function deleteParagraph(Request $request) {

        $paragraph = Paragraph::where('id',$request->paragraph_id)->select('theme')->first();

        $theme_id = $paragraph->theme;

        try {
            $res = Paragraph::where('id',$request->paragraph_id)->delete();
            if (!$res) {
                return [
                    'status' => 'error',
                    'message' => "Paragraph was not deleted."
                ];
            }
        }catch(\Exception $e){
            return [
                'status' => 'exception',
                'message' => "Some exception.",
                // 'ecxeption' => $e
            ];
        }

        //Переделываем сортировку
        $paragraphs = Paragraph::where('theme',$theme_id)
                                ->select('id')
                                ->orderBy('sort','asc')
                                ->get();

        if (!$paragraphs) {
            return [
                'status'=>'error',
                'message'=>'Some thing went wrong.'
            ];
        }

        try {
            $index = 1;

            foreach ($paragraphs as $paragraph) {
                Paragraph::where('id',$paragraph->id)->update(['sort'=>$index]);
                $index++;
            }
        } catch (\Exception $e) {
            return [
                'status'=>'error',
                'message'=>'Ошибка базы данных'
            ];
        }

        return [
            'status' => 'success',
            'message' => "Paragraph was deleted."
        ];
    }

    public function saveParagraphs(Request $request) {
        //Валидация
        $paragraphs = $request->paragraphs;

        if (gettype($paragraphs)!='array') {
            return [
                'status'=>'badData',
            ];
        }

        for ($i=0;$i<count($paragraphs);$i++) {
            if (gettype((int)$paragraphs[$i]['id']) != 'integer' or (int)$paragraphs[$i]['id'] == 0 ) {
                return [
                    'status'=>'badData'
                ];
            }

            if (!isset($paragraphs[$i]['content'])) {
                $paragraphs[$i]['content'] = null;
            }
        }

        try {
            foreach($paragraphs as $paragraph) {
                $res = Paragraph::where('id',$paragraph['id'])->update(['content'=>$paragraph['content']]);
                if (!$res) {
                    return [
                        'status'=>'error',
                        'message'=>'Ошибка БД'
                    ];
                }
            }

            return [
                'status'=>'success'
            ];
        }catch(\Exception $e) {
            return [
                'status'=>'exception',
                'message' =>$e  
            ];
        }
    }

}
