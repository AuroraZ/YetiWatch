// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProxmoxController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

Route::get('/dashboard', [ProxmoxController::class, 'index'])->name('dashboard');
Route::post('/start-vm', [ProxmoxController::class, 'tartVM'])->name('start-vm');
Route::post('/stop-vm', [ProxmoxController::class, 'topVM'])->name('stop-vm');
Route::post('/delete-vm', [ProxmoxController::class, 'deleteVM'])->name('delete-vm');
Route::post('/upload-iso', [ProxmoxController::class, 'uploadISO'])->name('upload-iso');
Route::get('/vnc/{vmid}', [ProxmoxController::class, 'vnc'])->name('vnc');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
