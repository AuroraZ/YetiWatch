// app/Http/Controllers/ProxmoxController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VM;
use App\Models\ProxmoxServer;

class ProxmoxController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $vms = VM::all();
        return view('dashboard', compact('vms'));
    }

    public function startVM(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $vmid = $request->input('vmid');
        $vm = VM::find($vmid);

        // implementation of startVM method
        //...

        return redirect()->route('dashboard');
    }

    public function stopVM(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $vmid = $request->input('vmid');
        $vm = VM::find($vmid);

        // implementation of stopVM method
        //...

        return redirect()->route('dashboard');
    }

    public function deleteVM(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $vmid = $request->input('vmid');
        $vm = VM::find($vmid);

        // implementation of deleteVM method
        //...

        return redirect()->route('dashboard');
    }

    public function assignPorts(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $vmid = $request->input('vmid');
        $start_port = $request->input('start_port');
        $end_port = $request->input('end_port');

        // implementation of assignPorts method
        //...

        return redirect()->route('dashboard');
    }

    public function assignIPv6(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $vmid = $request->input('vmid');
        $ipv6 = $request->input('ipv6');

        // implementation of assignIPv6 method
        //...

        return redirect()->route('dashboard');
    }

    public function uploadISO(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // implementation of uploadISO method
        //...

        return redirect()->route('dashboard');
    }

    public function vnc(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $vmid = $request->input('vmid');

        // implementation of vnc method
        //...

        return view('vnc', compact('vmid'));
    }
}// app/Http/Controllers/ProxmoxController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ProxmoxController extends Controller
{
    protected $client;
    protected $ticket;

    public function __construct()
    {
        $this->client = new Client(['base_uri' => config('proxmox.host')]);
        $this->authenticate();
    }

    private function authenticate()
    {
        $response = $this->client->post('/api2/json/access/ticket', [
            'form_params' => [
                'username' => config('proxmox.username'),
                'password' => config('proxmox.password')
            ]
        ]);
        $data = json_decode($response->getBody()->getContents(), true);
        $this->ticket = $data['data']['ticket'];
        $this->client = new Client([
            'base_uri' => config('proxmox.host'),
            'headers' => [
                'Cookie' => "PVEAuthCookie={$this->ticket}"
            ]
        ]);
    }

    public function listVMs()
    {
        $response = $this->client->get('/api2/json/nodes/pve/qemu');
        return json_decode($response->getBody()->getContents(), true)['data'];
    }

    public function startVM($vmid)
    {
        $response = $this->client->post("/api2/json/nodes/pve/qemu/$vmid/status/start");
        return json_decode($response->getBody()->getContents(), true);
    }

    public function stopVM($vmid)
    {
        $response = $this->client->post("/api2/json/nodes/pve/qemu/$vmid/status/stop");
        return json_decode($response->getBody()->getContents(), true);
    }

    public function deleteVM($vmid)
    {
        $response = $this->client->delete("/api2/json/nodes/pve/qemu/$vmid");
        return json_decode($response->getBody()->getContents(), true);
    }

    public function assignPorts($vmid, $start_port, $end_port)
    {
        $ports = range($start_port, $end_port);
        foreach ($ports as $port) {
            $command = "iptables -t nat -A PREROUTING -p tcp --dport $port -j DNAT --to-destination 192.168.1.$vmid:$port";
            shell_exec($command);
        }
        return ['status' =>'success', 'ports' => $ports];
    }

    public function assignIPv6($vmid, $ipv6)
    {
        $command = "ip -6 addr add $ipv6 dev tap$vmid";
        shell_exec($command);
        return ['status' =>'success', 'ipv6' => $ipv6];
    }
}
