@extends('app')

@section('content')
    <h1>{{ $lesson->name }}</h1>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            @foreach($lesson->problems as $problem)
                <th>{{ $problem->name }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($students as $student)
        <tr>
            <td>{{ $student->student_id }}</td>
            <td>{{ $student->name }}</td>
            @foreach($lesson->problems as $problem)
                <td>{{ $student['score'][$problem->name] }}</td>
            @endforeach
        </tr>
        @endforeach
        </tbody>
    </table>
@endsection