<?php

use App\Models\Seo;
use \App\Models\PanditService;

/**
 * ! Please change the helper functions correctly, because change in any helper function can be a
 * ! result of breaking website functionality.
 * ? Please write all your helpers here and if you want to write a logic which is gonna work
 * ? in many controller, then write in Service section. Create helpers for only blade logics
 * * To create your own service class then these are the below steps, please follow accordingly:
 * . 1. php artisan make:service ServiceName // Change ServiceName with your desired name.
 * . 2. Then go to App\Service here you will find your service class and write your login there.
 */


    /**
     * Return Active if current url matches
     *
     * @param string $url
     * @return string
     */
    function set_active(string $url): string
    {
        return request()->path() == str_replace(env('APP_URL'), '', $url) ? 'active' : '';
    }


    /**
     * Function to determine the current active menu
     * and dropdown menu active
     *
     * @param $url
     * @param int $type
     * @return string
     */
    function set_drop_active($url, int $type): string
    {
        if (is_array($url)) {
            if ($type == 2) {
                return in_array(request()->segment(2), $url) ? 'active' : '';
            } elseif ($type == 1) {
                return in_array(request()->segment(2), $url) ? 'menu-open' : '';
            }
        } else {
            if ($type == 2) {
                return request()->segment(2) == $url ? 'active' : '';
            } elseif ($type == 1) {
                return request()->segment(2) == $url ? 'menu-open' : '';
            }
        }
    }


    /**
     * Get SEO id from slug provided
     *
     * @return string
     */
    function getIdFromSlug($slug): string
    {
        return Seo::wherePermalink($slug)->first()->id ?? "";
    }

    /**
     * User Status color according to status provided
     *
     * @param string $data
     * @return string
     */
    function user_status_color(string $data): string
    {
        if ($data) return "badge-success";
        return "badge-danger";
    }

    /**
     * User Status Text according to status provided
     *
     * @param string $data
     * @return string
     */
    function user_status_text($data): string
    {
        if ($data) return "Verified";
        return "Not Verified";
    }


    /**
     * Function to return URL to seo button according to
     * current open page or URL
     *
     * @return string
     */

    function seoButton(): string
    {
        if (explode('.', Route::current()->getName())[0] == 'service') {
            return route('admin.category.seo', ['categoryId' => getIdFromSlug(Request::route('service'))]);
        } elseif (Route::current()->getName() == 'blog.detail') {
            $blog = Seo::wherePermalink(Route::current()->parameter("slug"))->first();
            if ($blog->blog->isNotEmpty()) {
                return route('admin.blog.seo', ['blog' => $blog->blog[0]->id]);
            }
        } else {
            return route('admin.seo', ['routeName' => Request::route()->getName()]);
        }
        return "#";
    }

    // : Admin Panel Menu Helper Function Starts from Here

    function more_menu($c, $index): string
    {
        if (isset($c['children']) && is_array($c['children'])) {
            $array = "";
            foreach ($c['children'] as $i => $m) {
                $array .= '<li  id="menuItem_' . $i . $index . '" ' . (isset($m['id']) ? 'data-id="' . $m["id"] . '"' : '') . ' ' . (isset($m['type']) ? 'data-type="' . $m["type"] . '"' : '') . ' ' . (isset($m['title']) ? 'data-title="' . $m["title"] . '"' : '') . '>
        <div class="menuDiv">
            <div class="header">
                <span class="head_title">' . get_title($m) . '</span>
                <div class="expandIt float-right">
                    <span>' . ucwords($m['type']) . '</span>
                    <i style="cursor: pointer" d-index="' . $i . $index . '"
                       class="fa fa-caret-down expandEditor"></i>
                </div>
            </div>
            <div id="menuEdit' . $i . $index . '" class="menuEdit">
                <div class="row">
                    <div class="col-md-12">
                        <label for="title">Navigation Label</label><br>
                        <input type="text" class="form-control"
                               ' . ($m['type'] != "link" ? "disabled" : "") . ' value="' . get_title($m) . '" name="title"
                               id="title">
                    </div>
                    <div class="col-md-12">
                        <div class="links">
                            <span>Original: <a href="' . get_link($m) . '">' . get_title($m) . '</a></span>
                        </div>
                    </div>
                    <div class="col-md-12">
                                                                        <span><a href="javascript:void(0)"
                                                                                 style="border-bottom: 1.5px solid;"
                                                                                 class="text-danger remove_li mr-3"
                                                                                 d-index="' . $i . $index . '"> Remove</a>
                                                                            <a style="border-bottom: 1.5px solid;"
                                                                               href="javascript:void(0)" class="cancel_li"
                                                                               d-index="' . $i . $index . '">Cancel</a></span>
                    </div>
                </div>
            </div>
        </div>
        <ol class="inner_listing">' . more_menu($m, $i) . '</ol>
    </li>';


    //            $array .= '<li data-id="'.$i.'"><div>' . $c['title'] . '</div><ol class="inner_listing">'.more_menu($c).'</ol></li>';
            }
            return $array;
        }
        return "";
    }


    /**
     * Return selected menu type
     *
     * @param string $type
     * @return string
     */
    function menu_selected_as(string $type): string
    {
        return $type == "header" ? "(Header)" : ($type == "footer" ? "(Footer)" : "");
    }


    /**
     * Get Title of menu from type provided
     *
     * @param array $m
     * @return string
     */
    function get_title(array $m): string
    {
        switch ($m['type'] ?? "") {
            case "link":
                return $m['title'];
            case "category":
                return ucwords(\App\Models\Categories::where("id", $m['id'])->first("name")->name);
            case "blog":
                return \App\Models\Blog::where("id", $m['id'])->first("title")->title;
            default:
                return "";
        }
    }

    /**
     * Return link/route of the menu by its type
     *
     * @param array $m
     * @return string
     */
    function get_link(array $m): string
    {
        switch ($m['type'] ?? "") {
            case "link":
                return $m['link'];
            case "category":
                return route("service.detail", ["service" => \App\Models\Categories::where("id", $m['id'])->first()->seo[0]->permalink]);
            case "blog":
                return route("blog.detail", ["slug" => \App\Models\Blog::where("id", $m['id'])->first()->seo[0]->permalink]);
            default:
                return "";
        }
    }


    // : Admin Panel Menu Helper Function Ends Here


    /**
     * Get current selected menu on location bases
     *
     * @param string $param
     * @return string
     */
    function get_current_selected_menu(string $param): string
    {
        return \App\Models\Menu::whereLocation($param)->first("title")->title;
    }

    /**
     * Submenu function to return submenu of menu
     *
     * @param string $data
     * @return string
     */
    function sub_menu($m): string
    {

        $array = '<div class="subMenu"><ul>';

        foreach ($m['children'] as $s) {
            $main = get_menu_details($s);
            $array .= '<li><h4 style="cursor:pointer;" onclick="window.location.href=\'' . $main['link'] . '\'">' . $main['title'] . '</h4>' . (isset($s['children']) ? sub_inner($s) : '') . '</li>';
        }

        $array .= '</ul></div>';
        return $array;
    }


    /**
     * Return submenu if children exist in menu
     *
     * @param array $m
     * @return string
     */
    function sub_inner(array $m): string
    {

        $array = '<ul class="subInnerMenu">';

        foreach ($m['children'] as $s) {
            $main = get_menu_details($s);
            $array .= '<li><a href="' . $main['link'] . '">' . $main['title'] . '</a></li>';
        }

        $array .= '</ul>';
        return $array;
    }

    /**
     * Return menu details from its type and link
     *
     * @param array $menu
     * @return array
     */
    function get_menu_details(array $menu): array
    {
        switch ($menu['type']) {
            case "link":
                return array("link" => $menu['link'], "title" => ucwords($menu['title']));
            case "category":
                $catg = \App\Models\Categories::whereId($menu['id'])->first();
                if($menu['id'] == \App\Models\Categories::MATRIMONY_SERVICES){
                    return array("link" => route('matriServices'), "title" => ucwords($catg->name));
                }
                if($catg->parent_id == \App\Models\Categories::MATRIMONY_SERVICES){
                    return array("link" => route('matrimonial-service', ['seo' => $catg->seo[0]->permalink]), "title" => ucwords($catg->name));
                }
                return array("link" => route('service.detail', ['service' => $catg->seo[0]->permalink]), "title" => ucwords($catg->name));
            case "blog":
                $blog = \App\Models\Blog::whereId($menu['id'])->first();
                return array("link" => route('blog.detail', ['slug' => $blog->seo[0]->peramlink]), "title" => ucwords($blog->title));
//            case "page":
//                return array();
            default:
                return array();
        }
    }

    /**
     * Check if service exist according to pandit service list.. in Pandit profile service section
     *
     * @param $category
     */
    function check_service_exist($category){
        return PanditService::wherePanditId(auth()->guard('pandit')->user()->id)
            ->where("category_id",$category)
            ->first();
    }

