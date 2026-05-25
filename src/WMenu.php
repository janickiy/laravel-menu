<?php

namespace Harimayco\Menu;

use Harimayco\Menu\Models\Menus;
use Harimayco\Menu\Models\MenuItems;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WMenu
{

    public function render(): View
    {
        $menu = new Menus();
        $menuitems = new MenuItems();
        $menulist = $menu->select(['id', 'name'])->get();
        $menulist = $menulist->pluck('name', 'id')->prepend($this->translate('select_menu'), 0)->all();

        if ((request()->has('action') && empty(request()->input('menu'))) || request()->input('menu') == '0') {
            return view('wmenu::menu-html')->with('menulist', $menulist);
        }

        $menu = Menus::find(request()->input('menu'));
        $menus = $menuitems->getall((int) request()->input('menu'));

        $data = ['menus' => $menus, 'indmenu' => $menu, 'menulist' => $menulist];
        if ((bool) config('menu.use_roles', false)) {
            $data['roles'] = DB::table(config('menu.roles_table'))->select([config('menu.roles_pk'), config('menu.roles_title_field')])->get();
            $data['role_pk'] = config('menu.roles_pk');
            $data['role_title_field'] = config('menu.roles_title_field');
        }

        return view('wmenu::menu-html', $data);
    }

    public function scripts(): View
    {
        return view('wmenu::scripts');
    }

    public function select(string $name = 'menu', array $menulist = []): string
    {
        $html = '<select name="' . e($name) . '">';

        foreach ($menulist as $key => $val) {
            $active = '';
            if (request()->input('menu') == $key) {
                $active = 'selected="selected"';
            }
            $html .= '<option ' . $active . ' value="' . e((string) $key) . '">' . e((string) $val) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }


    /**
     * Returns empty array if menu not found now.
     * Thanks @sovichet
     *
     * @return array
     */
    public static function getByName(string $name): array
    {
        $menu = Menus::byName($name);
        return is_null($menu) ? [] : self::get($menu->id);
    }

    public static function get(int|string $menu_id): array
    {
        $menuItem = new MenuItems();
        $menuId = (int) $menu_id;
        $menu_list = $menuItem->getall($menuId);

        $roots = $menu_list->where('menu', $menuId)->where('parent', 0);

        $items = self::tree($roots, $menu_list);
        return $items;
    }

    private static function tree(Collection $items, Collection $all_items): array
    {
        $data_arr = [];
        $i = 0;
        foreach ($items as $item) {
            $data_arr[$i] = $item->toArray();
            $find = $all_items->where('parent', $item->id);

            $data_arr[$i]['child'] = [];

            if ($find->count()) {
                $data_arr[$i]['child'] = self::tree($find, $all_items);
            }

            $i++;
        }

        return $data_arr;
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
