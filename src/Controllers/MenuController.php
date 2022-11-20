<?php

namespace Harimayco\Menu\Controllers;

use Harimayco\Menu\Facades\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Harimayco\Menu\Models\Menus;
use Harimayco\Menu\Models\MenuItems;

class MenuController extends Controller
{
    /**
     * @param Request $request
     * @return false|string
     */
    public function createnewmenu(Request $request)
    {

        $menu = new Menus();
        $menu->name = $request->input("menuname");
        $menu->save();

        return json_encode(["resp" => $menu->id]);
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function deleteitemmenu(Request $request)
    {
        $menuitem = MenuItems::find($request->input("id"));

        $menuitem->delete();
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function deletemenug(Request $request)
    {
        $menus = new MenuItems();
        $getall = $menus->getall($request->input("id"));

        if (count($getall) == 0) {
            $menudelete = Menus::find($request->input("id"));
            $menudelete->delete();

            return json_encode(["resp" => "Вы удалили этот пункт"]);
        } else {
            return json_encode(["resp" => "Сначала нужно удалить все элементы", "error" => 1]);
        }
    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function updateitem(Request $request)
    {
        $arraydata = $request->input("arraydata");

        if (is_array($arraydata)) {
            foreach ($arraydata as $value) {
                $menuitem = MenuItems::find($value['id']);
                $menuitem->label = $value['label'];
                $menuitem->link = $value['link'];
                $menuitem->class = $value['class'];

                if (config('menu.use_roles')) {
                    $menuitem->role_id = $value['role_id'] ? $value['role_id'] : 0 ;
                }

                $menuitem->save();
            }
        } else {
            $menuitem = MenuItems::find($request->input("id"));
            $menuitem->label = $request->input("label");
            $menuitem->link = $request->input("url");
            $menuitem->class = $request->input("clases");

            if (config('menu.use_roles')) {
                $menuitem->role_id = $request->input("role_id") ? $request->input("role_id") : 0 ;
            }

            $menuitem->save();
        }
    }

    /**
     * @param Request $request
     * @return void
     */
    public function addcustommenu(Request $request): void
    {
        $menuitem = new MenuItems();
        $menuitem->label = $request->input("labelmenu");
        $menuitem->link = $request->input("linkmenu");

        if (config('menu.use_roles')) {
            $menuitem->role_id = $request->input("rolemenu") ? $request->input("rolemenu")  : 0 ;
        }

        $menuitem->menu = $request->input("idmenu");
        $menuitem->sort = MenuItems::getNextSortRoot($request->input("idmenu"));
        $menuitem->save();

    }

    /**
     * @param Request $request
     * @return false|string
     */
    public function generatemenucontrol(Request $request)
    {
        $menu = Menus::find($request->input("idmenu"));
        $menu->name = $request->input("menuname");

        $menu->save();

        if (is_array($request->input("arraydata"))) {
            foreach ($request->input("arraydata") as $value) {

                $menuitem = MenuItems::find($value["id"]);
                $menuitem->parent = $value["parent"];
                $menuitem->sort = $value["sort"];
                $menuitem->depth = $value["depth"];

                if (config('menu.use_roles')) {
                    $menuitem->role_id = $request->input("role_id");
                }

                $menuitem->save();
            }
        }

        echo json_encode(["resp" => 1]);

    }
}
