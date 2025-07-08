@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manajemen User</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($user as $u)
            <tr>
                <td>{{ $u->name }}</td>
                <td>{{ $u->email }}</td>
                <td>{{ $u->role }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
