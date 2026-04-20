@extends('layouts.app')

@section('title', 'Edit Pasien')

@section('content')
    <livewire:patients.patient-edit :patient="$patient" />
@endsection