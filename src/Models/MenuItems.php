<?php

namespace Harimayco\Menu\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Harimayco\Menu\Models\MenuItems;
use Harimayco\Menu\Models\Menus;

class MenuItems extends Model
{

    protected $table = null;

    protected $fillable = ['label', 'link', 'parent', 'sort', 'class', 'menu', 'depth', 'role_id'];

    public function __construct(array $attributes = [])
    {
        //parent::construct( $attributes );
        $this->table = config('menu.table_prefix') . config('menu.table_name_items');
    }

    public function getsons(?int $id)
    {
        return $this->where("parent", $id)->get();
    }
    public function getall(?int $id)
    {
        return $this->where("menu", $id)->orderBy("sort", "asc")->get();
    }

    /**
     * @param int $menu
     * @return int
     */
    public static function getNextSortRoot(int $menu)
    {
        return self::where('menu', $menu)->max('sort') + 1;
    }

    public function parent_menu(): BelongsTo
    {
        return $this->belongsTo(Menus::class, 'menu');
    }

    public function child(): HasMany
    {
        return $this->hasMany(MenuItems::class, 'parent')->orderBy('sort', 'ASC');
    }
}
