<?php

namespace Harimayco\Menu\Controllers;

use App\Http\Controllers\Controller;
use Harimayco\Menu\Models\MenuItems;
use Harimayco\Menu\Models\Menus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MenuController extends Controller
{

    public function createnewmenu(Request $request): JsonResponse
    {
        $menu = new Menus();
        $menu->name = (string) $request->input('menuname', '');
        $menu->save();

        return response()->json(['resp' => $menu->id]);
    }

    public function deleteitemmenu(Request $request): JsonResponse
    {
        $menuitem = MenuItems::findOrFail((int) $request->input('id'));

        $menuitem->delete();

        return response()->json(['resp' => 1]);
    }

    public function deletemenug(Request $request): JsonResponse
    {
        $menuId = (int) $request->input('id');
        $menus = new MenuItems();
        $getall = $menus->getall($menuId);

        if ($getall->isNotEmpty()) {
            return response()->json([
                'resp' => $this->translate('delete_items_first'),
                'error' => 1,
            ]);
        }

        $menudelete = Menus::findOrFail($menuId);
        $menudelete->delete();

        return response()->json(['resp' => $this->translate('menu_deleted')]);
    }

    public function updateitem(Request $request): JsonResponse
    {
        $arraydata = $request->input('arraydata');
        if (is_array($arraydata)) {
            foreach ($arraydata as $value) {
                if (! is_array($value) || ! isset($value['id'])) {
                    continue;
                }

                $menuitem = MenuItems::findOrFail((int) $value['id']);
                $menuitem->label = (string) ($value['label'] ?? '');
                $menuitem->link = (string) ($value['link'] ?? '');
                $menuitem->class = $value['class'] ?? null;
                if ((bool) config('menu.use_roles', false)) {
                    $menuitem->role_id = (int) ($value['role_id'] ?? 0);
                }
                $menuitem->save();
            }
        } else {
            $menuitem = MenuItems::findOrFail((int) $request->input('id'));
            $menuitem->label = (string) $request->input('label', '');
            $menuitem->link = (string) $request->input('url', '');
            $menuitem->class = $request->input('clases');
            if ((bool) config('menu.use_roles', false)) {
                $menuitem->role_id = (int) $request->input('role_id', 0);
            }
            $menuitem->save();
        }

        return response()->json(['resp' => 1]);
    }

    public function addcustommenu(Request $request): JsonResponse
    {
        $menuId = (int) $request->input('idmenu');
        $menuitem = new MenuItems();
        $menuitem->label = (string) $request->input('labelmenu', '');
        $menuitem->link = (string) $request->input('linkmenu', '');
        if ((bool) config('menu.use_roles', false)) {
            $menuitem->role_id = (int) $request->input('rolemenu', 0);
        }
        $menuitem->menu = $menuId;
        $menuitem->sort = MenuItems::getNextSortRoot($menuId);
        $menuitem->save();

        return response()->json(['resp' => $menuitem->id]);
    }

    public function generatemenucontrol(Request $request): JsonResponse
    {
        $menu = Menus::findOrFail((int) $request->input('idmenu'));
        $menu->name = (string) $request->input('menuname', '');

        $menu->save();
        $arraydata = $request->input('arraydata');
        if (is_array($arraydata)) {
            foreach ($arraydata as $value) {
                if (! is_array($value) || ! isset($value['id'])) {
                    continue;
                }

                $menuitem = MenuItems::findOrFail((int) $value['id']);
                $menuitem->parent = (int) ($value['parent'] ?? 0);
                $menuitem->sort = (int) ($value['sort'] ?? 0);
                $menuitem->depth = (int) ($value['depth'] ?? 0);
                if ((bool) config('menu.use_roles', false) && $request->has('role_id')) {
                    $menuitem->role_id = (int) $request->input('role_id', 0);
                }
                $menuitem->save();
            }
        }

        return response()->json(['resp' => 1]);
    }

    private function translate(string $key): string
    {
        return (string) trans("wmenu::messages.{$key}", [], $this->locale());
    }

    private function locale(): string
    {
        $locale = (string) config('menu.locale', 'en');
        $supportedLocales = (array) config('menu.supported_locales', ['en']);

        return in_array($locale, $supportedLocales, true) ? $locale : 'en';
    }
}
