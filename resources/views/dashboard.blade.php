@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('header', 'Beranda')

@section('content')
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="brand-icon bg-primary bg-opacity-10 text-primary p-4 rounded-3 me-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total User</h6>
                    <h3 class="fw-bold mb-0">{{ \App\Models\User::count() }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card h-100 p-4">
            <h5 class="fw-bold mb-3">Selamat Datang, {{ auth()->user()->name }}!</h5>
            <p class="text-muted mb-0">Ini adalah sistem manajemen Tahfidz. Gunakan menu di samping untuk mengelola data sistem Anda.</p>
        </div>
    </div>
</div>
@endsection
