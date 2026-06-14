<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{

    public function index()
    {
        // $timetables = DB::table('timetables')
        //     ->join('classes', 'timetables.class_id', '=', 'classes.id')
        //     ->join('sections', 'timetables.section_id', '=', 'sections.id')
        //     ->join('subjects', 'timetables.subject_id', '=', 'subjects.id')
        //     ->join('teachers', 'timetables.teacher_id', '=', 'teachers.id')
        //     ->select('timetables.*', 'classes.name as class_name', 'sections.name as section', 
        //              'subjects.name as subject_name', 'teachers.name as teacher_name')
        //     ->orderBy('timetables.day')
        //     ->orderBy('timetables.start_time')
        //     ->get();
        
        // $classes = Classes::where('is_active', 1)->get();
         $classes = Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
        $sections = Section::where('is_active', 'Active')->get();
        $subjects = Subject::where('is_active', 'Active')->get();
        $teachers = Teacher::where('status', 'Active')->get();
        
        return view('admin.academic.timetable', compact( 'classes', 'sections', 'subjects', 'teachers'));
    }
    
    public function store(Request $request)
    {

        //  dd($request->all());
        $request->validate([
            'class_id' => 'required',
            'section_id' => 'required',
            'subject_id' => 'required',
            'teacher_id' => 'required',
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);        
        // Check conflict
        $conflict = $this->checkTeacherConflict($request);
        if ($conflict) {
            return response()->json(['success' => false, 'message' => 'Teacher already has a class at this time!'], 422);
        }
        
        Timetable::create($request->all());
        
        return response()->json(['success' => true, 'message' => 'Timetable added successfully!']);
    }
    
    public function edit($id)
    {
        return response()->json(Timetable::findOrFail($id));
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required',
            'section_id' => 'required',
            'subject_id' => 'required',
            'teacher_id' => 'required',
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        
        $conflict = $this->checkTeacherConflict($request, $id);
        if ($conflict) {
            return response()->json(['success' => false, 'message' => 'Teacher already has a class at this time!'], 422);
        }
        
        $timetable = Timetable::findOrFail($id);
        $timetable->update($request->all());
        
        return response()->json(['success' => true, 'message' => 'Timetable updated successfully!']);
    }
    
    public function destroy($id)
    {
        Timetable::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Timetable deleted successfully!']);
    }
    
    public function checkConflict(Request $request)
    {
        $conflict = $this->checkTeacherConflict($request, $request->id);
        return response()->json(['conflict' => $conflict]);
    }
    
    public function filter(Request $request)
    {
        $query = DB::table('timetables')
            ->join('classes', 'timetables.class_id', '=', 'classes.id')
            ->join('sections', 'timetables.section_id', '=', 'sections.id')
            ->join('subjects', 'timetables.subject_id', '=', 'subjects.id')
            ->join('teachers', 'timetables.teacher_id', '=', 'teachers.id')
            ->select('timetables.*', 'classes.name as class_name', 'sections.name as section', 
                     'subjects.name as subject_name', 'teachers.name as teacher_name');
        
        if ($request->class_id) {
            $query->where('timetables.class_id', $request->class_id);
        }
        if ($request->day) {
            $query->where('timetables.day', $request->day);
        }
        if ($request->teacher_id) {
            $query->where('timetables.teacher_id', $request->teacher_id);
        }
        
        $timetables = $query->orderBy('timetables.start_time')->get();
        
        $html = view('admin.timetable.partials.table_rows', compact('timetables'))->render();
        
        return response()->json(['html' => $html]);
    }
    
    private function checkTeacherConflict($request, $id = null)
    {
    $query = Timetable::where('teacher_id', $request->teacher_id)
            ->where('day', $request->day)
            ->where(function($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('start_time', '<=', $request->start_time)
                         ->where('end_time', '>=', $request->end_time);
                  });
            });
        
        if ($id) {
            $query->where('id', '!=', $id);
        }
        
        return $query->exists();
    }
}