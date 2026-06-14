@extends('layouts.teacher')

@section('title', 'Grade Book')

@section('content')
<div class="d-flex">
    <!-- Sidebar -->
    @include('teacher.partials.sidebar')

    <!-- Main Content -->
    <div class="main-content flex-grow-1">
        <!-- Navbar -->
        @include('teacher.partials.navbar')

        <!-- Content -->
        <div class="content-area">
            <div class="container-fluid">
                <h4 class="mb-4"><i class="fas fa-chart-line me-2"></i>Grade Book</h4>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Coming Soon!</strong> Grade book feature is under development.
                    <br>
                    For now, you can grade assignments from the Assignments section.
                </div>

                <!-- Quick Stats -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-primary">0</h3>
                                <p class="mb-0">Total Assignments</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-success">0</h3>
                                <p class="mb-0">Graded</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-warning">0</h3>
                                <p class="mb-0">Pending</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h3 class="text-info">0%</h3>
                                <p class="mb-0">Avg Score</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
