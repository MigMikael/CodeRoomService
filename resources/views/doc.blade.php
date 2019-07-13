@extends('app')

@section('header')

@endsection

@section('content')
    <div class="col-md-12 text-center">
        <h1>CodeRoomService API Doc</h1>
    </div>

    
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#1</b></span>  /login
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> UserAuthController:login
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#2</b></span>  /logout
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> UserAuthController:logout
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#3</b></span>  /register
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> UserAuthController:registerUser
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#4</b></span>  /image/show/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> ImageController:show
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#5</b></span>  /file/show/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> FileController:showResource
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#6</b></span>  /problem/{id}/question
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> FileController:question
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#7</b></span>  /api/user/home
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:index
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#8</b></span>  /student/dashboard
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> StudentController:dashboard
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#9</b></span>  /student/profile/{student_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> StudentController:profile
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#10</b></span>  /student/profile/edit
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> StudentController:updateProfile
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#11</b></span>  /student/change_password
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> StudentController:changePassword
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#12</b></span>  /student/course/{id}/member
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:member
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#13</b></span>  /student/course/join
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> CourseController:join
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#14</b></span>  /student/course/{student_id}/{course_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:showStudent
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#15</b></span>  /student/course/{id}/lesson/progress
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:sumProgress
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#16</b></span>  /student/lesson/{lesson_id}/{student_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> LessonController:showStudent
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#17</b></span>  /student/announcement/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> AnnouncementController:show
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#18</b></span>  /student/submission/{problem_id}/{student_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> SubmissionController:result
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#19</b></span>  /student/submission
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> SubmissionController:store2
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#20</b></span>  /teacher/dashboard
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> TeacherController:dashboard
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#21</b></span>  /teacher/profile/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> TeacherController:profile
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#22</b></span>  /teacher/profile/edit
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> TeacherController:updateProfile
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#23</b></span>  /teacher/change_password
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> TeacherController:changePassword
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#24</b></span>  /teacher/course/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:showTeacher
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#25</b></span>  /teacher/course/{id}/mode
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:changeMode
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#26</b></span>  /teacher/course/{id}/status
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:changeStatus
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#27</b></span>  /teacher/course/{id}/member
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:member
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#28</b></span>  /teacher/course/{id}/detail/progress
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:progressDetail
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#29</b></span>  /teacher/course/{lesson_id}/detail/summary
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:summaryDetail
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#30</b></span>  /teacher/course/{lesson_id}/{problem_id}/detail
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:problemDetail
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#31</b></span>  /teacher/course/{id}/lesson/progress
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:sumProgress
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#32</b></span>  /teacher/course/token/reset/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:resetToken
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#33</b></span>  /teacher/lesson/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> LessonController:showTeacher
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#34</b></span>  /teacher/lesson/edit
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> LessonController:update
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#35</b></span>  /teacher/lesson/store
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> LessonController:store
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#36</b></span>  /teacher/lesson/delete/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-danger">DELETE</span> LessonController:delete
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#37</b></span>  /teacher/lesson/change_order
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> LessonController:changeOrder
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#38</b></span>  /teacher/lesson/change_submit/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> LessonController:changeSubmit
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#39</b></span>  /teacher/lesson/change_status/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> LessonController:changeStatus
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#40</b></span>  /teacher/lesson/scoreboard/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> LessonController:scoreboard
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#41</b></span>  /teacher/lesson/export/score/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> LessonController:exportScore
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#42</b></span>  /teacher/lesson/{id}/export/by_problem/{problem_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> LessonController:exportByProblem
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#43</b></span>  /teacher/lesson/{id}/export/by_student/{student_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> LessonController:exportByStudent
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#44</b></span>  /teacher/lesson/{id}/guide/change_status
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> LessonController:changeGuide
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#45</b></span>  /teacher/lesson/{id}/resource/visible/{status}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> ResourceController:changeVisible
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#46</b></span>  /teacher/problem/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> ProblemController:show
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#47</b></span>  /teacher/problem/edit
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> ProblemController:update
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#48</b></span>  /teacher/problem/store
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> ProblemController:store
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#49</b></span>  /teacher/problem/store_score
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> ProblemController:storeScore
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#50</b></span>  /teacher/problem/delete/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-danger">DELETE</span> ProblemController:delete
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#51</b></span>  /teacher/problem/change_order
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> ProblemController:changeOrder
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#52</b></span>  /teacher/problem/{id}/status
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> ProblemController:changeStatus
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#53</b></span>  /teacher/problem/{id}/submission
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> ProblemController:submission
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#54</b></span>  /teacher/problem/in_sol/store
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> ProblemController:storeInputAndOutput
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#55</b></span>  /teacher/problem/input/edit
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> ProblemController:updateInput
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#56</b></span>  /teacher/problem/output/edit
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> ProblemController:updateOutput
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#57</b></span>  /teacher/problem/input/{id}/delete
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-danger">DELETE</span> ProblemController:destroyInput
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#58</b></span>  /teacher/problem/output/{id}/delete
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-danger">DELETE</span> ProblemController:destroyOutput
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#59</b></span>  /teacher/problem/delete_input/{problem_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-danger">DELETE</span> ProblemController:destroyAllInput
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#60</b></span>  /teacher/problem/delete_output/{problem_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-danger">DELETE</span> ProblemController:destroyAllOutput
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#61</b></span>  /teacher/problem/resource/store
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> ResourceController:store
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#62</b></span>  /teacher/problem/resource/edit
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> ResourceController:update
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#63</b></span>  /teacher/problem/resource/{id}/delete
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-danger">DELETE</span> ResourceController:destroy
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#64</b></span>  /teacher/problem/resource/{id}/visible
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> ResourceController:changeStatus
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#65</b></span>  /teacher/submission/{id}/code
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> SubmissionController:code
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#66</b></span>  /teacher/announcement/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> AnnouncementController:show
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#67</b></span>  /teacher/announcement/store
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> AnnouncementController:store
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#68</b></span>  /teacher/announcement/edit
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> AnnouncementController:update
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#69</b></span>  /teacher/announcement/delete/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-danger">DELETE</span> AnnouncementController:delete
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#70</b></span>  /teacher/announcement/{id}/status
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> AnnouncementController:changeStatus
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#71</b></span>  /teacher/student/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> StudentController:profile
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#72</b></span>  /teacher/student/store
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> StudentController:addMember
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#73</b></span>  /teacher/students/store
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> StudentController:addMembers
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#74</b></span>  /teacher/student/change_status/{student_id}/{course_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> StudentController:disable
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#75</b></span>  /teacher/student/all/{course_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> StudentController:getAll
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#76</b></span>  /teacher/student/submission/{id}/code
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> StudentController:submissionCode
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#77</b></span>  /teacher/submission
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> SubmissionController:store2
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#78</b></span>  /teacher/remove/ip/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> StudentController:removeIP
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#79</b></span>  /teacher/remove/ip_all/{course_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> StudentController:removeAllIP
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#80</b></span>  /admin/course
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:getAll
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#81</b></span>  /admin/course/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:showAdmin
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#82</b></span>  /admin/course/store
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> CourseController:store
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#83</b></span>  /admin/course/edit
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> CourseController:update
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#84</b></span>  /admin/course/status/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:changeStatus
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#85</b></span>  /admin/course/add/teacher
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> CourseController:addTeacher
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#86</b></span>  /admin/course/clone
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> CourseController:cloneCourse
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#87</b></span>  /admin/course/{id}/delete
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-danger">DELETE</span> CourseController:destroy
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#88</b></span>  /admin/teacher
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> TeacherController:getAll
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#89</b></span>  /admin/teacher/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> TeacherController:showAdmin
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#90</b></span>  /admin/teacher/store
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> TeacherController:store
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#91</b></span>  /admin/teacher/edit
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-warning">POST</span> TeacherController:update
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#92</b></span>  /admin/teacher/status/{teacher_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> TeacherController:changeStatus
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#93</b></span>  /admin/teacher/course/{course_id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> CourseController:teacherMember
            </div>
        </div>
    </div>
    <br>

    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    <span class="label label-default"><b>#94</b></span>  /export_score/{id}
                </h2>
            </div>
            <div class="panel-body">
                <span class="label label-success">GET</span> LessonController:exportScore
            </div>
        </div>
    </div>
    <br>

@endsection