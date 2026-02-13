<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'web',
    'prefix' => \Helper::getSubdirectory(),
    'namespace' => 'Modules\RecurringTickets\Http\Controllers'
], function () {
    Route::get('/recurring-tickets', ['uses' => 'RecurringTicketsController@index'])->name('recurringtickets.index');
    Route::get('/recurring-tickets/create', ['uses' => 'RecurringTicketsController@create'])->name('recurringtickets.create');
    Route::post('/recurring-tickets', ['uses' => 'RecurringTicketsController@store'])->name('recurringtickets.store');
});
