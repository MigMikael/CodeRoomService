@extends('app')

@section('header')
    <script type="text/javascript" src="{{ URL::asset('js/jquery-latest.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/jquery.tablesorter.js') }}"></script>
@endsection

@section('content')
    <h1>
        {{ $lesson->name }}
        @if(isset($_SESSION['userRole']))
            @if($_SESSION['userRole'] == 'teacher' || $_SESSION['userRole'] == 'admin' || Request::is('test4/*'))
                <a class="btn btn-primary" href="{{ url('export_score/'.$lesson->id) }}">Export Score</a>
            @endif
        @endif
    </h1>

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
            <th>Time</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students as $student)
        <tr @if($student['score']['complete'] == 'true') class="success" @endif>
            <td>{{ $student->id }}</td>
            <td>{{ $student->student_id }}</td>
            <td>{{ $student->name }}</td>
            @foreach($lesson->problems as $problem)
                <td>{{ $student['score'][$problem->name] }}</td>
            @endforeach
            <td>{{ $student['score']['total'] }}</td>
            <td>{{ $student['score']['time'] }}</td>
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