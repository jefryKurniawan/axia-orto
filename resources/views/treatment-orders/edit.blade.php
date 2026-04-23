@extends('layouts.app')

@section('title', 'Edit Pesanan')

@section('content')
    <livewire:treatment-orders.treatment-order-edit :treatment_order="$treatment_order" />
@endsection