<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    // Session Management
    public function sessions()
    {
        return view('admin.masters.sessions');
    }

    public function getSessionsData()
    {
        $sessions = Session::latest()->get();
        return response()->json(['success' => true, 'data' => $sessions]);
    }

    public function storeSession(Request $request)
    {
        $validated = $request->validate([
            'session_name' => 'required|unique:sessions|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'in:Active,Inactive'
        ]);

        if ($request->is_active == 'Active') {
            Session::where('is_active', 'Active')->update(['is_active' => 'Inactive']);
        }

        $session = Session::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Session created successfully!',
            'data' => $session
        ]);
    }

    public function updateSession(Request $request, $id)
    {
        $session = Session::findOrFail($id);

        $validated = $request->validate([
            'session_name' => 'required|unique:sessions,session_name,' . $id . '|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'in:Active,Inactive'
        ]);

        if ($request->is_active == 'Active') {
            Session::where('id', '!=', $id)->where('is_active', 'Active')->update(['is_active' => 'Inactive']);
        }

        $session->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Session updated successfully!',
            'data' => $session
        ]);
    }

    public function deleteSession($id)
    {
        $session = Session::findOrFail($id);
        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Session deleted successfully!'
        ]);
    }

    // Class Management
    public function classes()
    {
        return view('admin.masters.classes');
    }

    public function getClassesData()
    {
        $classes = Classes::withCount('sections', 'students')->latest()->get();
        return response()->json(['success' => true, 'data' => $classes]);
    }

    public function storeClass(Request $request)
    {
        $validated = $request->validate([
            'class_name' => 'required|max:50',
            'class_numeric' => 'nullable|integer',
            'is_active' => 'in:Active,Inactive'
        ]);

        $class = Classes::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Class created successfully!',
            'data' => $class
        ]);
    }

    public function updateClass(Request $request, $id)
    {
        $class = Classes::findOrFail($id);

        $validated = $request->validate([
            'class_name' => 'required|max:50',
            'class_numeric' => 'nullable|integer',
            'is_active' => 'in:Active,Inactive'
        ]);

        $class->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Class updated successfully!',
            'data' => $class
        ]);
    }

    public function deleteClass($id)
    {
        $class = Classes::findOrFail($id);
        $class->delete();

        return response()->json([
            'success' => true,
            'message' => 'Class deleted successfully!'
        ]);
    }

    // Section Management
    public function sections()
    {
        return view('admin.masters.sections');
    }

    public function getSectionsData(Request $request)
    {
        $query = Section::with('class');

        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        $sections = $query->latest()->get();
        return response()->json(['success' => true, 'data' => $sections]);
    }

    public function storeSection(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_name' => 'required|max:10',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'in:Active,Inactive'
        ]);

        $section = Section::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Section created successfully!',
            'data' => $section->load('class')
        ]);
    }

    public function updateSection(Request $request, $id)
    {
        $section = Section::findOrFail($id);

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_name' => 'required|max:10',
            'capacity' => 'required|integer|min:1',
            'is_active' => 'in:Active,Inactive'
        ]);

        $section->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully!',
            'data' => $section->load('class')
        ]);
    }

    public function deleteSection($id)
    {
        $section = Section::findOrFail($id);
        $section->delete();

        return response()->json([
            'success' => true,
            'message' => 'Section deleted successfully!'
        ]);
    }

    // Subject Management
    public function subjects()
    {
        return view('admin.masters.subjects');
    }

    public function getSubjectsData()
    {
        $subjects = Subject::latest()->get();
        return response()->json(['success' => true, 'data' => $subjects]);
    }

    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'subject_name' => 'required|max:100',
            'subject_code' => 'required|unique:subjects|max:20',
            'description' => 'nullable',
            'is_active' => 'in:Active,Inactive'
        ]);

        $subject = Subject::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subject created successfully!',
            'data' => $subject
        ]);
    }

    public function updateSubject(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'subject_name' => 'required|max:100',
            'subject_code' => 'required|unique:subjects,subject_code,' . $id . '|max:20',
            'description' => 'nullable',
            'is_active' => 'in:Active,Inactive'
        ]);

        $subject->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subject updated successfully!',
            'data' => $subject
        ]);
    }

    public function deleteSubject($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subject deleted successfully!'
        ]);
    }

    // Helper methods for dropdowns
    public function getActiveClasses()
    {
        $classes = Classes::where('is_active', 'Active')->get();
        return response()->json(['success' => true, 'data' => $classes]);
    }

    public function getSectionsByClass($classId)
    {
        $sections = Section::where('class_id', $classId)->where('is_active', 'Active')->get();
        return response()->json(['success' => true, 'data' => $sections]);
    }

    public function getActiveSessions()
    {
        $sessions = Session::where('is_active', 'Active')->get();
        return response()->json(['success' => true, 'data' => $sessions]);
    }

    // Class-Section Assignment
    public function classSections()
    {
        try {
            $classes = Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
            return view('admin.masters.class-sections-simple', compact('classes'));
        } catch (\Exception $e) {
            \Log::error('Class-Sections Error: ' . $e->getMessage());
            return back()->with('error', 'Error loading page: ' . $e->getMessage());
        }
    }

    public function getClassSections(Request $request)
    {
        $classId = $request->class_id;
        
        // Get all sections
        $allSections = Section::where('is_active', 'Active')->get();
        
        // Get sections already assigned to this class
        $assignedSections = Section::where('class_id', $classId)->get();
        
        return response()->json([
            'all_sections' => $allSections,
            'assigned_sections' => $assignedSections
        ]);
    }

    public function assignSectionsToClass(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_ids' => 'required|array'
        ]);

        $classId = $request->class_id;
        $sectionIds = $request->section_ids;

        // First, remove all sections from this class
        Section::where('class_id', $classId)->update(['class_id' => null]);

        // Then assign selected sections to this class
        Section::whereIn('id', $sectionIds)->update(['class_id' => $classId]);

        return response()->json([
            'success' => true,
            'message' => 'Sections assigned successfully',
            'assigned_count' => count($sectionIds)
        ]);
    }

    public function quickAddSections(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'sections' => 'required|array',
            'capacity' => 'required|integer|min:1'
        ]);

        $classId = $request->class_id;
        $sections = $request->sections;
        $capacity = $request->capacity;
        $addedCount = 0;

        foreach ($sections as $sectionName) {
            // Check if section already exists for this class
            $exists = Section::where('class_id', $classId)
                            ->where('section_name', $sectionName)
                            ->exists();
            
            if (!$exists) {
                Section::create([
                    'class_id' => $classId,
                    'section_name' => $sectionName,
                    'capacity' => $capacity,
                    'is_active' => 'Active'
                ]);
                $addedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "$addedCount sections added successfully",
            'added_count' => $addedCount
        ]);
    }
}

