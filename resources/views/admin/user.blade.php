@extends('layouts.master')
@section('titulo' , 'Usuarios  :: SSO-AUTH')

@section('content')
    <div id="app">
        <user-vue
            :role-granted="{{ json_encode($roleGranted) }}"
            :permissions-granted="{{ json_encode($permissionsGranted) }}"
        />
    </div>
@endsection
