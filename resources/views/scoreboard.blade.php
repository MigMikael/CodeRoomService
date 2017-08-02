@extends('app')

@section('header')
    <script type="text/javascript" src="{{ URL::asset('js/jquery-latest.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/jquery.tablesorter.js') }}"></script>
@endsection

@section('content')
    <h1>{{ $lesson->name }}</h1>
    <table class="table table-hover" id="score-table">
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

    <script>
        $(document).ready(function()
            {
                $("#score-table").tablesorter();
            }
        );
    </script>
@endsection