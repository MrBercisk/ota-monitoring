@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">

        {{-- Update Profile Information --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">👤 Informasi Profile</h5>
            </div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Update Password --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">🔒 Update Password</h5>
            </div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

    </div>
</div>
@endsection