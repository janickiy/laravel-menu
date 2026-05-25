<?php

use Harimayco\Menu\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

$path = trim((string) config('menu.route_path', 'harimayco'), '/');

Route::middleware(config('menu.middleware', []))->prefix($path)->group(function (): void {
    Route::post('addcustommenu', [MenuController::class, 'addcustommenu'])->name('haddcustommenu');
    Route::post('deleteitemmenu', [MenuController::class, 'deleteitemmenu'])->name('hdeleteitemmenu');
    Route::post('deletemenug', [MenuController::class, 'deletemenug'])->name('hdeletemenug');
    Route::post('createnewmenu', [MenuController::class, 'createnewmenu'])->name('hcreatenewmenu');
    Route::post('generatemenucontrol', [MenuController::class, 'generatemenucontrol'])->name('hgeneratemenucontrol');
    Route::post('updateitem', [MenuController::class, 'updateitem'])->name('hupdateitem');
});
