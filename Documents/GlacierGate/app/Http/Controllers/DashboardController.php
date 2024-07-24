// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VM;

class DashboardController extends Controller
{
    public function index()
    {
        $vms = VM::all();
        return view('dashboard', compact('vms'));
    }

    public function startVM(Request $request)
    {
        // implementation of startVM method
    }

    public function stopVM(Request $request)
    {
        // implementation of stopVM method
    }

    public function deleteVM(Request $request)
    {
        // implementation of deleteVM method
    }

    public function assignPorts(Request $request)
    {
        // implementation of assignPorts method
    }

    public function assignIPv6(Request $request)
    {
        // implementation of assignIPv6 method
    }

    public function uploadISO(Request $request)
    {
        // implementation of uploadISO method
    }
}
