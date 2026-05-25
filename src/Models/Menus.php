<?php

namespace Harimayco\Menu\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menus extends Model
{
    protected $table = 'menus';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('menu.table_prefix') . config('menu.table_name_menus');
    }

    public static function byName(string $name): ?self
    {
        return self::where('name', $name)->first();
    }

    public function items(): HasMany
    {
        return $this->hasMany(MenuItems::class, 'menu')->with('child')->where('parent', 0)->orderBy('sort', 'ASC');
    }
}
