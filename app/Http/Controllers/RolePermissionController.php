<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\StaffMember;

class RolePermissionController extends Controller
{
    // Roles Management
    public function rolesIndex(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $roles = Role::withCount('permissions')->get();
            return response()->json(['roles' => $roles]);
        }
        
        $roles = Role::with('permissions')->get();
        return view('admin.settings.roles', compact('roles'));
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        return response()->json(['role' => $role]);
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'display_name' => 'required',
            'description' => 'nullable',
            'is_active' => 'nullable'
        ]);

        $role = Role::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') || $request->is_active == 1 ? true : false
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Role created successfully!',
            'role' => $role
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'display_name' => 'required',
            'description' => 'nullable',
            'is_active' => 'nullable'
        ]);

        $role->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') || $request->is_active == 1 ? true : false
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully!',
            'role' => $role
        ]);
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        
        // Check if role is assigned to any users
        $hasUsers = $role->admins()->count() + $role->teachers()->count() + $role->staffMembers()->count();
        
        if ($hasUsers > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role. It is assigned to ' . $hasUsers . ' user(s).'
            ], 400);
        }
        
        $role->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully!'
        ]);
    }

    // Permissions Management
    public function permissionsIndex(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $permissions = Permission::all();
            $modules = Permission::distinct()->pluck('module');
            return response()->json([
                'permissions' => $permissions,
                'modules' => $modules
            ]);
        }
        
        $permissions = Permission::orderBy('module')->get();
        $modules = Permission::distinct()->pluck('module');
        return view('admin.settings.permissions', compact('permissions', 'modules'));
    }

    public function editPermission($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json(['permission' => $permission]);
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'display_name' => 'required',
            'module' => 'required',
            'description' => 'nullable',
            'is_active' => 'nullable'
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'module' => $request->module,
            'description' => $request->description,
            'is_active' => $request->has('is_active') || $request->is_active == 1 ? true : false
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully!',
            'permission' => $permission
        ]);
    }

    public function updatePermission(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
            'display_name' => 'required',
            'module' => 'required',
            'description' => 'nullable',
            'is_active' => 'nullable'
        ]);

        $permission->update([
            'name' => $request->name,
            'display_name' => $request->display_name,
            'module' => $request->module,
            'description' => $request->description,
            'is_active' => $request->has('is_active') || $request->is_active == 1 ? true : false
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully!',
            'permission' => $permission
        ]);
    }

    public function deletePermission($id)
    {
        $permission = Permission::findOrFail($id);
        
        // Check if permission is assigned to any roles
        $hasRoles = $permission->roles()->count();
        
        if ($hasRoles > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete permission. It is assigned to ' . $hasRoles . ' role(s).'
            ], 400);
        }
        
        $permission->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Permission deleted successfully!'
        ]);
    }

    // Assign Permissions to Role
    public function assignPermissions()
    {
        $roles = Role::all();
        $permissions = Permission::orderBy('module')->get();
        $modules = Permission::distinct()->pluck('module');
        
        return view('admin.settings.assign-permissions', compact('roles', 'permissions', 'modules'));
    }

    public function updateRolePermissions(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $permissions = $request->input('permissions', []);
        
        $role->permissions()->sync($permissions);
        
        return redirect()->back()->with('success', 'Permissions assigned successfully!');
    }

    // Assign Roles to Users
    public function assignRoles()
    {
        $roles = Role::all();
        $admins = Admin::all();
        $teachers = Teacher::all();
        $staff = StaffMember::all();
        
        return view('admin.settings.assign-roles', compact('roles', 'admins', 'teachers', 'staff'));
    }

    public function assignRoleToAdmin(Request $request, $adminId)
    {
        $admin = Admin::findOrFail($adminId);
        $roles = $request->input('roles', []);
        
        $admin->roles()->sync($roles);
        
        return redirect()->back()->with('success', 'Roles assigned to admin successfully!');
    }

    public function assignRoleToTeacher(Request $request, $teacherId)
    {
        $teacher = Teacher::findOrFail($teacherId);
        $roles = $request->input('roles', []);
        
        $teacher->roles()->sync($roles);
        
        return redirect()->back()->with('success', 'Roles assigned to teacher successfully!');
    }

    public function assignRoleToStaff(Request $request, $staffId)
    {
        $staff = StaffMember::findOrFail($staffId);
        $roles = $request->input('roles', []);
        
        $staff->roles()->sync($roles);
        
        return redirect()->back()->with('success', 'Roles assigned to staff successfully!');
    }
}
