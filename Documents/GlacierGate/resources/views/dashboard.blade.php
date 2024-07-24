// resources/views/dashboard.blade.php

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - GlacierGate</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <div class="container">
        <h1>Welcome to GlacierGate, {{ Auth::user()->username }}!</h1>
        <a href="{{ route('logout') }}">Logout</a>
        <h2>Your VPS Instances</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vms as $vm)
                    <tr>
                        <td>{{ $vm['vmid'] }}</td>
                        <td>{{ $vm['name'] }}</td>
                        <td>{{ $vm['status'] }}</td>
                        <td>
                            <form action="{{ route('start-vm') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                <button type="submit">Start</button>
                            </form>
                            <form action="{{ route('stop-vm') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                <button type="submit">Stop</button>
                            </form>
                            <form action="{{ route('delete-vm') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                <button type="submit">Delete</button>
                            </form>
                            <form action="{{ route('assign-ports') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                <input type="number" name="start_port" placeholder="Start Port" required>
                                <input type="number" name="end_port" placeholder="End Port" required>
                                <button type="submit">Assign Ports</button>
                            </form>
                            <form action="{{ route('assign-ipv6') }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="vmid" value="{{ $vm['vmid'] }}">
                                <input type="text" name="ipv6" placeholder="IPv6 Address" required>
                                <button type="submit">Assign IPv6</button>
                            </form>
                            <a href="{{ route('vnc', ['vmid' => $vm['vmid']]) }}" target="_blank">VNC</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <h2>Upload Custom ISO</h2>
        <form action="{{ route('upload-iso') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="iso" accept=".iso" required>
            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
