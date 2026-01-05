<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('grade')) {
            $query->where('grade_level', $request->grade);
        }

        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        $query->orderBy($sort, $direction);

        $users = $query->paginate(10)->withQueryString();

        return Inertia::render('Users/UserIndex', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'grade', 'sort', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Users/UserCreate');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['student', 'teacher', 'librarian'])],
            'grade_level' => 'nullable|integer|min:1|max:12', // Basic validation
        ]);

        // Conditional Requirement
        if ($validated['role'] === 'student' && empty($validated['grade_level'])) {
            return back()->withErrors(['grade_level' => 'Grade level is required for students.']);
        }
        if ($validated['role'] !== 'student') {
            $validated['grade_level'] = null;
        }

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('Users/UserEdit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email_prefix' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9.]+$/'],
            'role' => ['required', Rule::in(['student', 'teacher', 'librarian'])],
            'grade_level' => 'nullable|integer|min:1|max:12',
            'password' => 'nullable|string|min:8',
        ]);

        $email = $data['email_prefix'].'@'.$data['role'].'.libratech.com';

        // Check uniqueness ignoring current user
        if (User::where('email', $email)->where('id', '!=', $user->id)->exists()) {
            return back()->withErrors(['email_prefix' => 'This username is already taken.']);
        }

        if ($data['role'] === 'student' && empty($data['grade_level'])) {
            return back()->withErrors(['grade_level' => 'Grade level is required for students.']);
        }
        if ($data['role'] !== 'student') {
            $data['grade_level'] = null;
        }

        $updateData = [
            'name' => $data['name'],
            'email' => $email,
            'role' => $data['role'],
            'grade_level' => $data['grade_level'],
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('message', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        // Prevent self-deletion if included (though UI should prevent it)
        $ids = array_diff($request->ids, [Auth::id()]);

        User::whereIn('id', $ids)->delete();

        return redirect()->route('users.index')->with('message', count($ids).' users deleted successfully.');
    }

    private function generateUniqueEmail($firstName, $middleName, $lastName, $role)
    {
        $first = strtolower(preg_replace('/[^a-z0-9]/', '', $firstName));
        $last = strtolower(preg_replace('/[^a-z0-9]/', '', $lastName));

        $middle = strtolower(trim($middleName));
        $middleParts = array_filter(explode(' ', $middle));

        if (empty($middleParts)) {
            // Case 3 (1 name/No middle): maria.cruz
            $base = $first.'.'.$last;
        } elseif (count($middleParts) === 1) {
            // Case 2 (2 names/1 middle): maria.l.cruz
            $initial = substr(reset($middleParts), 0, 1);
            $base = $first.'.'.$initial.'.'.$last;
        } else {
            // Case 1 (3+ names/Multiple middle): marialt.cruz
            $initials = '';
            foreach ($middleParts as $part) {
                $initials .= substr($part, 0, 1);
            }
            $base = $first.$initials.'.'.$last;
        }

        $domain = '@'.$role.'.libratech.com';
        $email = $base.$domain;

        $counter = 1;
        while (User::where('email', $email)->exists()) {
            $counter++;
            $email = $base.$counter.$domain;
        }

        return $email;
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle); // Skip header

        // Required columns: first_name, middle_name, last_name, role, grade_level
        // Index: 0, 1, 2, 3, 4

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) {
                continue;
            }

            $firstName = trim($row[0]);
            $middleName = trim($row[1]);
            $lastName = trim($row[2]);
            $role = strtolower(trim($row[3]));

            // Strict Validation: Required Fields
            if (empty($firstName) || empty($lastName) || empty($role)) {
                continue; // Skip invalid row
            }

            // Validate Role strictly
            if (! in_array($role, ['student', 'teacher', 'librarian'])) {
                continue; // Skip invalid role
            }
            $gradeRaw = isset($row[4]) ? trim($row[4]) : null;

            // Logic:
            $grade = null;

            if ($role === 'student') {
                // Default to 7 if empty/invalid
                if ($gradeRaw === '' || $gradeRaw === null) {
                    $grade = 7;
                } else {
                    $grade = (int) $gradeRaw;
                    // Ensure valid range
                    if ($grade < 1 || $grade > 12) {
                        $grade = 7;
                    }
                }
            } else {
                // Force null for non-students (teacher, librarian)
                $grade = null;
            }

            // Construct Full Name
            $fullName = $firstName;
            if ($middleName) {
                $fullName .= ' '.$middleName;
            }
            $fullName .= ' '.$lastName;

            // Generate Email
            $email = $this->generateUniqueEmail($firstName, $middleName, $lastName, $role);

            User::create([
                'name' => $fullName,
                'email' => $email,
                'role' => $role,
                'grade_level' => $grade,
                'password' => Hash::make('12345678'),
            ]);
        }

        fclose($handle);

        return redirect()->route('users.index')->with('message', 'Users imported successfully.');
    }
}
