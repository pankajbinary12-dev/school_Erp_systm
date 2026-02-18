@extends('admin.layouts.horizontal')

@section('title', 'Class-Section Test')

@section('content')
<div style="padding: 20px;">
    <h1>TEST PAGE</h1>
    <p>If you can see this, the view is rendering!</p>
    <p>Classes Count: {{ $classes->count() }}</p>
    
    <h3>Classes List:</h3>
    <ul>
        @foreach($classes as $class)
            <li>{{ $class->class_name }} (ID: {{ $class->id }})</li>
        @endforeach
    </ul>
</div>
@endsection
