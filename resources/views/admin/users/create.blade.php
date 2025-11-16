@extends('admin.layouts.app')
@section('content')

<div class="max-w-lg mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New User</h1>
        <p class="text-gray-600">Add a new user to the system with appropriate role and permissions</p>
    </div>

    <form action="{{ route('users.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('name') border-red-500 @enderror"
                placeholder="Enter full name"
                required
            >
            @error('name')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('email') border-red-500 @enderror"
                placeholder="Enter email address"
                required
            >
            @error('email')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
            <input
                type="password"
                name="password"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('password') border-red-500 @enderror"
                placeholder="Enter password"
                required
            >
            @error('password')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">User Role *</label>
            <select
                name="role"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 @error('role') border-red-500 @enderror"
                required
            >
                <option value="">Select a role</option>
                <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Manager</option>
                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Team Member</option>
                <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Client</option>
            </select>
            @error('role')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>
<div>
    <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
    <input
        type="text"
        name="department"
        value="{{ old('department') }}"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
        placeholder="Enter department name (optional)"
        list="department_suggestions"
    >
    <datalist id="department_suggestions">
        @foreach($departments ?? [] as $dept)
            <option value="{{ $dept }}">
        @endforeach
    </datalist>
    <p class="text-xs text-gray-500 mt-1">Start typing to see existing departments or enter a new one</p>
</div>
        <!-- Client Information (Only show when role is client) -->
        <div id="client-info" class="hidden bg-blue-50 p-4 rounded-lg border border-blue-200">
            <h4 class="text-sm font-medium text-blue-900 mb-3">Client Information</h4>
            <p class="text-sm text-blue-700 mb-3">
                When creating a client user, a new client record will be automatically created using the user's name and email.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                    <input
                        type="text"
                        name="company"
                        value="{{ old('company') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        placeholder="Optional company name"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input
                        type="tel"
                        name="phone"
                        value="{{ old('phone') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        placeholder="Optional phone number"
                    >
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <a href="{{ route('users.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 font-medium">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200 font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create User
            </button>
        </div>
    </form>
</div>

<script>
    // Show/hide client info based on role selection
    document.querySelector('select[name="role"]').addEventListener('change', function() {
        const clientInfo = document.getElementById('client-info');
        if (this.value === 'client') {
            clientInfo.classList.remove('hidden');
        } else {
            clientInfo.classList.add('hidden');
        }
    });

    // Trigger on page load in case of validation errors
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.querySelector('select[name="role"]');
        if (roleSelect.value === 'client') {
            document.getElementById('client-info').classList.remove('hidden');
        }
    });
</script>
<script>
// Department Management
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department_select');
    const customDeptBtn = document.getElementById('custom_department_btn');
    const customDeptInput = document.getElementById('custom_department_input');
    const customDeptField = document.getElementById('custom_department');

    // Toggle custom department input
    if (customDeptBtn && customDeptInput) {
        customDeptBtn.addEventListener('click', function() {
            customDeptInput.classList.toggle('hidden');
            if (!customDeptInput.classList.contains('hidden')) {
                customDeptField.focus();
                departmentSelect.value = '';
            }
        });

        // When custom department is entered, update the select
        customDeptField.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                departmentSelect.value = '';
            }
        });

        // When a department is selected from dropdown, clear custom input
        departmentSelect.addEventListener('change', function() {
            if (this.value !== '') {
                customDeptField.value = '';
                customDeptInput.classList.add('hidden');
            }
        });
    }

    // Professional Filters
    const departmentFilter = document.getElementById('department_filter');
    const roleFilter = document.getElementById('role_filter');
    const resetFilters = document.getElementById('reset_filters');

    function applyFilters() {
        const department = departmentFilter.value;
        const role = roleFilter.value;

        let url = new URL(window.location.href);
        let params = new URLSearchParams();

        if (department !== 'all') {
            params.set('department', department);
        }

        if (role !== 'all') {
            params.set('role', role);
        }

        const queryString = params.toString();
        window.location.href = queryString ? `${url.pathname}?${queryString}` : url.pathname;
    }

    if (departmentFilter) {
        departmentFilter.addEventListener('change', applyFilters);
    }

    if (roleFilter) {
        roleFilter.addEventListener('change', applyFilters);
    }

    if (resetFilters) {
        resetFilters.addEventListener('click', function() {
            window.location.href = "{{ route('users.index') }}";
        });
    }

    // Mobile filter functionality
    const filterTabs = document.querySelectorAll('.filter-tab');
    const userColumns = document.querySelectorAll('.user-column');

    function initMobileView() {
        if (window.innerWidth < 1024) {
            userColumns.forEach((col, index) => {
                if (index === 0) {
                    col.style.display = 'block';
                } else {
                    col.style.display = 'none';
                }
            });
        } else {
            userColumns.forEach(col => {
                col.style.display = 'block';
            });
        }
    }

    if (filterTabs.length > 0) {
        filterTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const role = this.getAttribute('data-role');

                // Update active tab
                filterTabs.forEach(t => {
                    t.classList.remove('active', 'bg-purple-100', 'text-purple-800');
                    t.classList.add('text-gray-600', 'hover:bg-gray-100');
                });
                this.classList.remove('text-gray-600', 'hover:bg-gray-100');
                this.classList.add('active', 'bg-purple-100', 'text-purple-800');

                // Show selected column, hide others on mobile
                if (window.innerWidth < 1024) {
                    userColumns.forEach(col => {
                        if (col.getAttribute('data-role') === role) {
                            col.style.display = 'block';
                        } else {
                            col.style.display = 'none';
                        }
                    });
                }
            });
        });

        window.addEventListener('resize', initMobileView);
        initMobileView();
    }
});

// Form submission - handle custom department
document.querySelector('form')?.addEventListener('submit', function(e) {
    const customDeptField = document.getElementById('custom_department');
    const departmentSelect = document.getElementById('department_select');

    if (customDeptField && customDeptField.value.trim() !== '') {
        // Create a hidden input to submit the custom department
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'department';
        hiddenInput.value = customDeptField.value.trim();
        this.appendChild(hiddenInput);

        // Disable the original select to avoid conflict
        if (departmentSelect) {
            departmentSelect.disabled = true;
        }
    }
});
</script>
@endsection
