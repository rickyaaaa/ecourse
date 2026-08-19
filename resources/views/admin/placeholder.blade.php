@extends('layouts.admin')

@section('title', $title . ' — Panel Admin')
@section('page-title', $title)

@section('content')
<div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">
    <p class="text-sm text-gray-500">Halaman "{{ $title }}" sedang dalam pengembangan.</p>
</div>
@endsection
