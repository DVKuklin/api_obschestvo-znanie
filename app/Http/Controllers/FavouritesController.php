<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Favourite;
use App\Models\Theme;
use App\Models\Paragraph;

class FavouritesController extends Controller
{
    public function getFavourites(Request $request) {
        $user_id = $request->user()->id;

        $sql = "
            SELECT 
                paragraphs.id as id,
                paragraphs.sort as paragraph_sort,
                paragraphs.content as content,
                themes.sort as theme_sort,
                themes.name as theme_name,
                themes.url as theme_url,
                null as theme_image,
                themes.emoji as theme_emoji,
                sections.sort as section_sort,
                sections.url as section_url,
                sections.name as section_name,
                sections.image as section_image,
                favourites.created_at as date_time,
                'paragraph' as type

            FROM `favourites` 

            join paragraphs on paragraphs.id = favourites.paragraph_theme_id
            join themes on themes.id = paragraphs.theme
            join sections on sections.id = themes.section
                

            WHERE user_id = $user_id and type = 'paragraph'                
            
            UNION
            
            SELECT 
                themes.id as id,
                themes.sort as paragraph_sort,
                themes.description as content,
                themes.sort as theme_sort,
                themes.name as theme_name,
                themes.url as theme_url,
                themes.image as theme_image,
                themes.emoji as theme_emoji,
                sections.sort as section_sort,
                sections.url as section_url,
                sections.name as section_name,
                sections.image as section_image,
                favourites.created_at as date_time,
                'theme' as type

            FROM `favourites` 

            join themes on themes.id = favourites.paragraph_theme_id
            join sections on sections.id = themes.section
            
            WHERE user_id = $user_id and type = 'theme'
        ";

        $total_count = DB::select("
            SELECT count(*) as count from ($sql) as count_table
        ")[0]->count;

        $page = 1;
        if(isset($request->page)) {
            $page = (int)$request->page;
            if ($page <= 0) {
                $page = 1;
            }
        }

        $filters = [];

        $limit = "5";
        if (isset($request->limit)) {
            if ($request->limit == "5") {
                $limit = 5;
            }
            if ($request->limit == "10") {
                $limit = 10;
            }
            if ($request->limit == "15") {
                $limit = 15;
            }
            if ($request->limit == "all") {
                $limit = "all";
                $page = 1;
            }
        }

        if ($limit != 5) {
            $filters['limit'] = $limit;
        }

        $total_page_count = 1;

        $pagination = "";
        if ($limit != "all") {
            $total_page_count = ceil($total_count / $limit);
            $offset = ($page - 1) * $limit;
            if (($offset+1) > $total_count) {
                $offset = intdiv($total_count,$limit) * $limit;
                $page = $total_page_count;
            }
            $pagination = "LIMIT $limit OFFSET $offset";
        }

        $order =  "DESC";

        if (isset($request->order)) {
            if ($request->order == "DESC") {
                $order = "DESC";
            }
            if ($request->order == "ASC") {
                $order = "ASC";
            }
        }

        if ($order != 'DESC') {
            $filters['order'] = "ASC";
        }

        $orderBy = "date_time $order";

        if (isset($request->orderBy)) {
            if ($request->orderBy == "date_time") {
                $orderBy = "date_time $order";
            }
            if ($request->orderBy == "sort") {
                $orderBy = "section_sort $order, theme_sort $order, paragraph_sort $order, type DESC    ";
                $filters['orderBy'] = "sort";
            }
        }

        $sql_filter = "
            ORDER BY $orderBy
            
            $pagination
        ";

        $linkToPage = null;
        if ($page > 1) {
            $linkToPage = $page - 1;
        }
        $links = [];
        $links[] = [
            'label'=>'<',
            'active'=>false,
            'page'=>$linkToPage
        ];

        for ($i=1;$i<=$total_page_count;$i++) {
            $active = false;
            if ($i == $page) {
                $active = true;
            }
            $links[] = [
                'label'=>$i,
                'active'=>$active,
                'page'=>$i,
            ];
        }
        $linkToPage = $page + 1;
        if ($page == $total_page_count) {
            $linkToPage = null;
        }
        $links[] = [
            'label'=>'>',
            'active'=>false,
            'page' => $linkToPage
        ];

        $response['status'] = 'success';        
        $response['favourites']['links'] = $links;
        $response['favourites']['query'] = $filters;
        $response['favourites']['data']= DB::select($sql.$sql_filter);
        $response['favourites']['current_page'] = $page;
        $response['favourites']['total_page_count'] = $total_page_count;

        return $response;
    }

    public function addToFavourites(Request $request) {
        $user_id = $request->user()->id;

        $request->validate([
            'id'=>'required|integer',
            'type'=>'required'
        ]);

        $is_exists = false;

        if ($request->type == 'theme') {
            $is_exists = Theme::where('id',$request->id)->exists();
        }

        if ($request->type == 'paragraph') {
            $is_exists = Paragraph::where('id',$request->id)->exists();
        }

        $res = false;

        if ($is_exists) {
            $res = Favourite::create([
                "user_id" => $user_id,
                "paragraph_theme_id" => $request->id,
                "type" => $request->type,
            ]);
        }

        if ($res) {
            return ['status'=>'success'];
        }
        return ['status'=>'error'];
    }

    public function removeFromFavourites(Request $request) {
        $user_id = $request->user()->id;
        $favourite = Favourite::where('user_id',$user_id)
                                ->where('paragraph_theme_id',$request->id)
                                ->where('type',$request->type)
                                ->first();

        if ($favourite) {
            $favourite->delete();
            return ['status' => 'success'];
        }
        return ['status' => 'error'];
    }
}
