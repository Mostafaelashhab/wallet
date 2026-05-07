<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PushController;
use App\Http\Controllers\RecurringController;
use App\Http\Controllers\SettleUpController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect()->route('dashboard') : redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}', [GroupController::class, 'show'])->middleware('group.member')->name('groups.show');
    Route::post('/groups/{group}/members', [GroupController::class, 'addMember'])->middleware('group.member')->name('groups.members.add');
    Route::delete('/groups/{group}/members/{user}', [GroupController::class, 'removeMember'])->middleware('group.member')->name('groups.members.remove');

    Route::get('/friends', [FriendController::class, 'index'])->name('friends.index');
    Route::post('/friends', [FriendController::class, 'store'])->name('friends.store');
    Route::get('/friends/{user}', [FriendController::class, 'show'])->name('friends.show');

    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}', [ExpenseController::class, 'show'])->name('expenses.show');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    Route::post('/expenses/{expense}/comments', [ExpenseController::class, 'comment'])->name('expenses.comments.store');

    Route::get('/activity', [ActivityController::class, 'index'])->name('activity');

    Route::get('/groups/{group}/settle', [SettleUpController::class, 'show'])->middleware('group.member')->name('groups.settle');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/groups/{group}/goals/create', [GoalController::class, 'create'])->middleware('group.member')->name('goals.create');
    Route::post('/groups/{group}/goals', [GoalController::class, 'store'])->middleware('group.member')->name('goals.store');
    Route::post('/goals/{goal}/contribute', [GoalController::class, 'contribute'])->name('goals.contribute');

    Route::get('/groups/{group}/recurring/create', [RecurringController::class, 'create'])->middleware('group.member')->name('recurring.create');
    Route::post('/groups/{group}/recurring', [RecurringController::class, 'store'])->middleware('group.member')->name('recurring.store');
    Route::delete('/recurring/{recurring}', [RecurringController::class, 'destroy'])->name('recurring.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/push/subscribe', [PushController::class, 'subscribe']);
});
