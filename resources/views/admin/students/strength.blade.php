@extends('admin.layouts.horizontal')

@section('title', 'Student Strength')

@push('styles')
<style>
    .strength-table {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .strength-table thead {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
    }
    .strength-table thead th {
        padding: 15px;
        font-weight: 600;
        border: none;
    }
    .strength-table tbody tr {
        transition: background 0.3s;
    }
    .strength-table tbody tr:hover {
        background: #f8f9fc;
    }
    .strength-table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    .class-name {
        font-weight: 600;
        color: #4e73df;
    }
    .section-badge {
        display: inline-block;
        padding: 5px 12px;
        background: #e7f0ff;
        color: #4e73df;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        margin: 2px;
    }
    .count-badge {
        display: inline-block;
        padding: 6px 15px;
        background: #1cc88a;
        color: white;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    .total-row {
        background: #f8f9fc;
        font-weight: bold;
        border-top: 2px solid #4e73df;
    }
    .total-row td {
        padding: 15px;
        font-size: 16px;
    }
    .grand-total-card {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        margin-top: 20px;
    }
    @media print {
        .btn, .content-card-header button {
            display: none;
        }
    }
</style>
@endpush

@section('content')
<div class="content-card">
    <div class="content-card-header">
        <h5><i class="fas fa-chart-bar me-2"></i>Student Strength Report</h5>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Print Report
        </button>
    </div>

    <div class="strength-table">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th style="width: 10%;">S.No</th>
                    <th style="width: 25%;">Class</th>
                    <th style="width: 35%;">Sections</th>
                    <th style="width: 15%;" class="text-center">Total Students</th>
                    <th style="width: 15%;" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    $serialNo = 1;
                @endphp
                
                @foreach($classes as $class)
                    @php
                        $classTotal = 0;
                        $sectionsData = [];
                        
                        foreach($class->sections as $section) {
                            $sectionCount = $section->students->count();
                            $sectionsData[] = $section->section_name . ' (' . $sectionCount . ')';
                            $classTotal += $sectionCount;
                        }
                        
                        $grandTotal += $classTotal;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $serialNo++ }}</td>
                        <td>
                            <span class="class-name">
                                <i class="fas fa-school me-2"></i>{{ $class->class_name }}
                            </span>
                        </td>
                        <td>
                            @foreach($class->sections as $section)
                                @php
                                    $sectionCount = $section->students->count();
                                @endphp
                                <span class="section-badge">
                                    {{ $section->section_name }}: {{ $sectionCount }}
                                </span>
                            @endforeach
                        </td>
                        <td class="text-center">
                            <span class="count-badge">{{ $classTotal }}</span>
                        </td>
                        <td class="text-center">
                            @if($classTotal > 0)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Empty</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                
                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="3" class="text-end">
                        <i class="fas fa-calculator me-2"></i>
                        <strong>GRAND TOTAL</strong>
                    </td>
                    <td class="text-center">
                        <span class="count-badge" style="background: #4e73df;">{{ $grandTotal }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary">All Classes</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Summary Cards -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Classes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $classes->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Sections
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $totalSections = 0;
                                    foreach($classes as $class) {
                                        $totalSections += $class->sections->count();
                                    }
                                @endphp
                                {{ $totalSections }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Students
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $grandTotal }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Average per Class -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Average Students per Class:</strong> 
                {{ $classes->count() > 0 ? number_format($grandTotal / $classes->count(), 2) : 0 }}
            </div>
        </div>
    </div>
</div>
@endsection
