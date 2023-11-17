<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Section, Theme, Paragraph, User, AdditionalPages, AdditionalPagesContents};

class PagesController extends Controller
{
    function getSections() {
        return Section::orderBy('sort', 'asc')->get();
    }

    function getThemesAndSectionBySectionUrl(Request $r) {
        $section = Section::where('url',$r->section_url)->select('id','name','image', 'url')->first();
        
        $themes = Theme::where('section',$section->id)->select('id','name','url','sort', 'emoji', 'image', 'description')->orderBy('sort', 'asc')->get();

        $user = auth()->user();

        foreach($themes as $theme) {
            if ($user) {
                $theme->isInFavourites = $theme->isFavourite($user->id);
            } else {
                $theme->isInFavourites = false;
            }
        }

        $data = [
            'section' => $section,
            'themes' => $themes
        ];

        return $data;
    }

    // URL get_section_name_and_theme_name_by_url
    // {
    //     "section_url":"section_5",
    //     "theme_url":"them_36"
    // }
    function getSectionNameAndThemeNameByUrl(Request $request) {

        $section = Section::where('url',$request->section_url)->select('name','id')->first();

        if ($section == null) {
            return ["status"=>"notFound"];
        } 

        $theme = Theme::where('section',$section->id)
                        ->where('url',$request->theme_url)
                        ->select('name', 'sort')
                        ->first();
        
        if ($theme == null) {
            return ["status"=>"notFound"];
        }

        return [
            'status'=>'success',
            'section_name' => $section->name,
            'theme_name' => $theme->sort.'. '.$theme->name
        ];
    }

    function getParagraphsBySectionAndThemeUrl(Request $request) {
        $status='';

        $section = Section::where('url',$request->section_url)->first();
        if ($section == null) {
            return ["status"=>"notFound"];
        }
    
        $theme = Theme::where('section',$section->id)
                        ->where('url',$request->theme_url)
                        ->select('id','name', 'sort','heading_image','emoji', 'description')
                        ->first();

        if ($theme == null) {
            return ["status"=>"notFound"];
        }

        $user = $request->user();

        if ($user == null) {
            $data = [
                'status' => 'notAuth',
                'section' => $section->name,
                'theme' => $theme->sort.". ".$theme->name
            ];
            return $data;
        }
        //Определяем разрешена ли тема
        $permitions = $user->allowed_themes;
        $permitions = json_decode($permitions);

        if ($permitions == null) {
            $data = [
                'status' => 'notAllowed',
                'section' => $section->name,
                'theme' => $theme->sort.". ".$theme->name
            ];
            return $data;
        }

        if (gettype($permitions)!="array") {
            $status = 'notAllowed';
        } else {
            $isAllowed=false;

            for ($i=0;$i<count($permitions);$i++) {
                if ($permitions[$i]->id == $theme->id and $permitions[$i]->allowed == 'true') {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                $data = [
                    'status' => 'notAllowed',
                    'section' => $section->name,
                    'theme' => $theme->sort.". ".$theme->name
                ];
                return $data;
            }
        }

        $paragraphs = Paragraph::where('theme',$theme->id)
                        ->orderBy('sort', 'asc')
                        ->get();

        foreach ($paragraphs as $item) {
            $item->isInFavorites = $item->isFavourite($user->id);
        }

        $navigation_params['left_src'] = null;
        $navigation_params['left_theme_name'] = null;
        $left_theme = Theme::where('section',$section->id)
                            ->where('sort',$theme->sort - 1)
                            ->select('url','name', 'sort')
                            ->first();
        if ($left_theme) {
            $navigation_params['left_src'] = '/'.$section->url.'/'.$left_theme->url;
            $navigation_params['left_theme_name'] = $left_theme->sort.'. '.$left_theme->name;
        }

        $navigation_params['right_src'] = null;
        $navigation_params['right_theme_name'] = null;
        $right_theme = Theme::where('section',$section->id)
                            ->where('sort',$theme->sort + 1)
                            ->select('url','name', 'sort')
                            ->first();
        if ($right_theme) {
            $navigation_params['right_src'] = '/'.$section->url.'/'.$right_theme->url;
            $navigation_params['right_theme_name'] = $right_theme->sort.'. '.$right_theme->name;
        }

        $data = [
            'navigation_params' => $navigation_params,
            'status' => 'success',
            'section' => $section->name,
            'theme' => $theme->sort.". ".$theme->name,
            'theme_isFavourite'=>$theme->isFavourite($user->id),
            'theme_id'=>$theme->id,
            'image' => $theme->heading_image,
            'emoji' => $theme->emoji,
            'description' => $theme->description,
            'paragraphs' => $paragraphs,

        ];
        return $data;

    }

    public function getAdditionalPagesUrls() {
        $pages = AdditionalPages::where('is_published', true)
                                    ->orderBy('sort')
                                    ->select('title','url','icon')
                                    ->get();
        return response()->json($pages,200);
    }

    public function getAdditionalPageByUrl(Request $request) {
        $page=AdditionalPages::where('url',$request->url)
                                ->where('is_published',true)
                                ->select('id','title','icon')
                                ->first();
        if ($page) {
            $contents = AdditionalPagesContents::where('additional_page_id',$page->id)
                                                    ->where('is_published',true)
                                                    ->orderBy('sort')
                                                    ->select('content')
                                                    ->get();
            if (!$contents) {
                $contents = [];
            }
            $data = [
                'page'=>$page,
                'contents'=>$contents,
            ];
            return response()->json($data,200);
        }

        return response()->json(['status'=>'notFound'],404);
    }
}
