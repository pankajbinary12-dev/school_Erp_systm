<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\StaffMember;
use Carbon\Carbon;

class LibraryController extends Controller
{
    // Book Categories
    public function categories()
    {
        return view('admin.library.categories');
    }

    public function getCategoriesData()
    {
        $categories = BookCategory::withCount('books')->orderBy('category_name')->get();
        return response()->json(['data' => $categories]);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|unique:book_categories,category_name',
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        $category = BookCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category added successfully!',
            'data' => $category
        ]);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = BookCategory::findOrFail($id);

        $validated = $request->validate([
            'category_name' => 'required|string|unique:book_categories,category_name,' . $id,
            'description' => 'nullable|string',
            'status' => 'required|in:Active,Inactive'
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully!',
            'data' => $category
        ]);
    }

    public function deleteCategory($id)
    {
        $category = BookCategory::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!'
        ]);
    }

    // Books
    public function books()
    {
        return view('admin.library.books');
    }

    public function getBooksData()
    {
        $books = Book::with('category')->orderBy('book_no')->get();
        return response()->json(['data' => $books]);
    }

    public function storeBook(Request $request)
    {
        $validated = $request->validate([
            'book_no' => 'required|string|unique:books,book_no',
            'title' => 'required|string',
            'category_id' => 'required|exists:book_categories,id',
            'author' => 'required|string',
            'publisher' => 'nullable|string',
            'isbn' => 'nullable|string|unique:books,isbn',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'rack_no' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:Available,Issued,Lost,Damaged'
        ]);

        $validated['available_quantity'] = $validated['quantity'];

        $book = Book::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Book added successfully!',
            'data' => $book->load('category')
        ]);
    }

    public function updateBook(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $validated = $request->validate([
            'book_no' => 'required|string|unique:books,book_no,' . $id,
            'title' => 'required|string',
            'category_id' => 'required|exists:book_categories,id',
            'author' => 'required|string',
            'publisher' => 'nullable|string',
            'isbn' => 'nullable|string|unique:books,isbn,' . $id,
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'rack_no' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|in:Available,Issued,Lost,Damaged'
        ]);

        $book->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Book updated successfully!',
            'data' => $book->load('category')
        ]);
    }

    public function deleteBook($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Book deleted successfully!'
        ]);
    }

    // Book Issue
    public function issue()
    {
        return view('admin.library.issue');
    }

    public function getIssuesData()
    {
        $issues = BookIssue::with('book')->orderBy('issue_date', 'desc')->get();
        
        // Add member name to each issue
        $issues->each(function($issue) {
            $issue->member_name = $issue->member_name;
        });

        return response()->json(['data' => $issues]);
    }

    public function getMembers(Request $request)
    {
        $type = $request->input('type');
        $members = [];

        if ($type === 'Student') {
            $members = Student::select('id', 'first_name', 'last_name', 'admission_no as code')
                ->where('status', 'Active')
                ->get();
        } elseif ($type === 'Teacher') {
            $members = Teacher::select('id', 'first_name', 'last_name', 'employee_id as code')
                ->where('status', 'Active')
                ->get();
        } elseif ($type === 'Staff') {
            $members = StaffMember::select('id', 'first_name', 'last_name', 'employee_id as code')
                ->where('status', 'Active')
                ->get();
        }

        return response()->json(['data' => $members]);
    }

    public function getAvailableBooks()
    {
        $books = Book::where('available_quantity', '>', 0)
            ->where('status', 'Available')
            ->with('category')
            ->get();

        return response()->json(['data' => $books]);
    }

    public function issueBook(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'member_type' => 'required|in:Student,Teacher,Staff',
            'member_id' => 'required|integer',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'remarks' => 'nullable|string'
        ]);

        // Check if book is available
        $book = Book::findOrFail($validated['book_id']);
        if ($book->available_quantity <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Book is not available!'
            ], 400);
        }

        // Create issue record
        $issue = BookIssue::create($validated);

        // Update book available quantity
        $book->decrement('available_quantity');
        if ($book->available_quantity == 0) {
            $book->update(['status' => 'Issued']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Book issued successfully!',
            'data' => $issue->load('book')
        ]);
    }

    public function returnBook(Request $request, $id)
    {
        $issue = BookIssue::findOrFail($id);

        $validated = $request->validate([
            'return_date' => 'required|date',
            'fine_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string'
        ]);

        $validated['status'] = 'Returned';

        $issue->update($validated);

        // Update book available quantity
        $book = $issue->book;
        $book->increment('available_quantity');
        if ($book->available_quantity > 0) {
            $book->update(['status' => 'Available']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Book returned successfully!',
            'data' => $issue
        ]);
    }
}
