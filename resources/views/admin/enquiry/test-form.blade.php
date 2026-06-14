@extends('admin.layouts.horizontal')

@section('title', 'Test Enquiry Form')

@section('content')
<div class="container">
    <h2>Test Enquiry Form</h2>
    
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <form action="{{ route('admin.enquiry.store') }}" method="POST">
        @csrf
        
        <div class="card mb-3">
            <div class="card-body">
                <h5>Basic Info</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>First Name *</label>
                        <input type="text" name="first_name" class="form-control" value="Test" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" class="form-control" value="Student" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Date of Birth *</label>
                        <input type="date" name="date_of_birth" class="form-control" value="2010-01-01" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Gender *</label>
                        <select name="gender" class="form-control" required>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Phone *</label>
                        <input type="text" name="phone" class="form-control" value="9876543210" required>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5>Address</h5>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label>Address *</label>
                        <input type="text" name="address" class="form-control" value="Test Address" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>City *</label>
                        <input type="text" name="city" class="form-control" value="Test City" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>State *</label>
                        <input type="text" name="state" class="form-control" value="Test State" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Pincode *</label>
                        <input type="text" name="pincode" class="form-control" value="123456" required>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5>Academic</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Class *</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Session *</label>
                        <select name="session_id" class="form-control" required>
                            <option value="">Select Session</option>
                            @foreach($sessions as $session)
                            <option value="{{ $session->id }}">{{ $session->session_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5>Parent Info</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Father Name *</label>
                        <input type="text" name="father_name" class="form-control" value="Test Father" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Father Phone *</label>
                        <input type="text" name="father_phone" class="form-control" value="9876543210" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Mother Name *</label>
                        <input type="text" name="mother_name" class="form-control" value="Test Mother" required>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-body">
                <h5>Fee</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Registration Fee *</label>
                        <input type="number" step="0.01" name="registration_fee" class="form-control" value="500" required>
                    </div>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary btn-lg">Submit Test Enquiry</button>
    </form>
</div>
@endsection
