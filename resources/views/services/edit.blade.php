@extends('layouts.app')

@section('title', 'Edit Layanan')

@section('content')
    <livewire:services.services-edit :service="$service" />
@endsection