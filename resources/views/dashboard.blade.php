
@extends('gentelella::page')

@section('title', 'Dashboard')
@section('page_key', 'dashboard')
@section('breadcrumb', 'Home > Dashboard')

@section('content')
    <x-gentelella::page-header
        title="Dashboard"
        pretitle="Overview"
    />

    <x-gentelella::card title="Maxy Purchasing & Sales Cycle">
        <p>
            Selamat datang di aplikasi Purchasing & Sales Cycle.
        </p>
        <p>
            Dashboard menggunakan Gentelella v4.2.0
            dengan Laravel 13.
        </p>
    </x-gentelella::card>
@endsection
