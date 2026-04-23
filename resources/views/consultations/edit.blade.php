@extends('layouts.app')

@section('title', 'Edit Konsultasi')

@section('content')
    <livewire:consultations.consultation-edit :consultation="$consultation" />
@endsection