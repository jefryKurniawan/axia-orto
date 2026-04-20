@extends('layouts.app')

@section('title', 'Edit Item Inventori')

@section('content')
    <livewire:inventory.inventory-edit :item="$item" />
@endsection
