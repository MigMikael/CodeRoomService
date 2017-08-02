@extends('app')

@section('header')
    <script type="text/javascript" src="{{ URL::asset('js/jquery-latest.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/jquery.tablesorter.js') }}"></script>
@endsection

@section('content')
    <h1>{{ $lesson->name }}</h1>
    <table class="table table-hover table-responsive table-bordered" id="score-table">
        <thead>
        <tr>
            <th>ID</th>
            <th>StudentID</th>
            <th>Name</th>
            @foreach($lesson->problems as $problem)
                <th>{{ $problem->name }}</th>
            @endforeach
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students as $student)
        <tr>
            <td>{{ $student->id }}</td>
            <td>{{ $student->student_id }}</td>
            <td>{{ $student->name }}</td>
            @foreach($lesson->problems as $problem)
                <td>{{ $student['score'][$problem->name] }}</td>
            @endforeach
            <td>{{ $student['score']['total'] }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>

    <script>
        $(document).ready(function()
            {
                $("#score-table").tablesorter({
                    // sort on the first column and third column, order asc
                    sortList: [[0,0]]
                });
            }
        );
    </script>
@endsection