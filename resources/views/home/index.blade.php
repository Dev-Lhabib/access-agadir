@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-gradient-to-b from-violet-50 to-white">
    <div class="text-center px-4">
        <h1 class="text-4xl md:text-5xl font-bold text-violet-800 mb-4">
            AccessAgadir
        </h1>
        <p class="text-xl text-gray-600 mb-8">
            Une ville accessible pour tous
        </p>
        <a href="/map"
           class="inline-block bg-violet-600 text-white px-8 py-3 rounded-lg text-lg font-medium hover:bg-violet-700 transition">
            Explorer la carte
        </a>
    </div>
</div>
@endsection