@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'Manager.layouts.app',
        'user' => 'team.app',
    };
@endphp
@extends($layout)
@section("content")
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Calendar</h1>
                    <span class="ml-3 text-sm text-gray-500">All tasks and deadlines in one place</span>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('manager.tasks.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                        Tasks
                    </a>
                    <a href="{{ route('manager.projects.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                        Projects
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 id="currentMonth" class="text-xl font-bold text-gray-900">
                            {{ \Carbon\Carbon::now()->format('F Y') }}
                        </h2>
                        <div class="flex space-x-1">
                            <button onclick="changeMonth(-1)" class="p-2 hover:bg-gray-100 rounded-lg transition duration-200">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button onclick="changeMonth(1)" class="p-2 hover:bg-gray-100 rounded-lg transition duration-200">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button onclick="showToday()" class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition duration-200 text-left">
                            Go to Today
                        </button>
                        <a href="{{ route('manager.tasks.create') }}" class="block w-full px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition duration-200 text-left">
                            Create Task
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Projects</h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($projects as $project)
                        <div class="flex items-center p-2 rounded-lg hover:bg-gray-50 transition duration-200">
                            <div class="w-3 h-3 rounded-full bg-blue-500 mr-3"></div>
                            <span class="text-sm text-gray-700 truncate">{{ $project->name }}</span>
                        </div>
                        @endforeach

                        @if($projects->isEmpty())
                        <div class="text-center py-4 text-gray-500">
                            <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <p class="text-sm">No projects yet</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Upcoming</h3>
                    <div class="space-y-3" id="upcomingTasks">
                        <div class="text-center py-4">
                            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600 mx-auto"></div>
                            <p class="text-sm text-gray-500 mt-2">Loading upcoming tasks...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                        <div class="p-4 text-center text-sm font-medium text-gray-500">Mon</div>
                        <div class="p-4 text-center text-sm font-medium text-gray-500">Tue</div>
                        <div class="p-4 text-center text-sm font-medium text-gray-500">Wed</div>
                        <div class="p-4 text-center text-sm font-medium text-gray-500">Thu</div>
                        <div class="p-4 text-center text-sm font-medium text-gray-500">Fri</div>
                        <div class="p-4 text-center text-sm font-medium text-gray-500">Sat</div>
                        <div class="p-4 text-center text-sm font-medium text-gray-500">Sun</div>
                    </div>

                    <div id="calendarGrid" class="grid grid-cols-7 auto-rows-fr">
                    </div>
                </div>

                <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex flex-wrap items-center gap-6 text-sm">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-500 rounded mr-2"></div>
                            <span class="text-gray-700">High Priority</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-500 rounded mr-2"></div>
                            <span class="text-gray-700">Medium Priority</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded mr-2"></div>
                            <span class="text-gray-700">Low Priority</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-400 rounded mr-2"></div>
                            <span class="text-gray-700">Project Deadline</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-gray-200 border border-gray-400 rounded mr-2"></div>
                            <span class="text-gray-700">Completed</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded mr-2"></div>
                            <span class="text-gray-700">No Due Date</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="taskModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900"></h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span id="modalProject"></span>
                    </div>

                    <div class="flex items-center text-sm text-gray-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span id="modalAssigned"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-center">
                        <span id="modalPriority" class="px-3 py-1 rounded-full text-sm font-medium"></span>
                    </div>

                    <div class="flex items-center">
                        <span id="modalStatus" class="px-3 py-1 rounded-full text-sm font-medium"></span>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Description</h4>
                    <p id="modalDescription" class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg"></p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span id="modalDueDate"></span>
                    </div>
                    <a id="modalTaskLink" href="#" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200 font-medium">
                        View Task Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentDate = new Date();
let allTasks = [];

document.addEventListener('DOMContentLoaded', function() {
    console.log('Calendar initialized');
    loadCalendarData();
});

async function loadCalendarData() {
    try {
        showLoading();
        console.log('Loading calendar data...');

        await Promise.all([
            loadTasksForCurrentMonth(),
            loadUpcomingTasks()
        ]);

        console.log('Calendar data loaded successfully');

    } catch (error) {
        console.error('Error loading calendar data:', error);
        showError('Failed to load calendar data. Please try again.');
    }
}

async function loadTasksForCurrentMonth() {
    try {
        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

        const startStr = firstDay.toISOString().split('T')[0];
        const endStr = lastDay.toISOString().split('T')[0];

        console.log('Loading tasks for date range:', startStr, 'to', endStr);

        const response = await fetch(`{{ route("manager.calendar.events") }}?start=${startStr}&end=${endStr}`);

        if (response.ok) {
            allTasks = await response.json();
            console.log('Tasks loaded successfully:', allTasks);
            console.log('Total tasks found:', allTasks.length);

            allTasks.forEach((task, index) => {
                console.log(`Task ${index + 1}:`, {
                    id: task.id,
                    title: task.title,
                    status: task.status,
                    is_completed: task.is_completed,
                    priority: task.priority,
                    start: task.start,
                    has_due_date: task.has_due_date
                });
            });

            generateCalendar();
        } else {
            const errorText = await response.text();
            console.error('Server response not OK:', response.status, errorText);
            throw new Error(`Server returned ${response.status}: ${errorText}`);
        }
    } catch (error) {
        console.error('Error loading tasks:', error);
        allTasks = [];
        generateCalendar();
        showEmptyState();
    }
}

async function loadTasksForMonth(year, month) {
    try {
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);

        const startStr = firstDay.toISOString().split('T')[0];
        const endStr = lastDay.toISOString().split('T')[0];

        console.log('Loading tasks for month:', year, month, 'Range:', startStr, 'to', endStr);

        const response = await fetch(`{{ route("manager.calendar.events") }}?start=${startStr}&end=${endStr}`);

        if (response.ok) {
            allTasks = await response.json();
            console.log('Tasks loaded for new month:', allTasks.length);
            generateCalendar();
        } else {
            throw new Error(`Server returned ${response.status}`);
        }
    } catch (error) {
        console.error('Error loading tasks for month:', error);
        allTasks = [];
        generateCalendar();
    }
}

async function loadUpcomingTasks() {
    try {
        console.log('Loading upcoming tasks...');
        const response = await fetch('{{ route("manager.calendar.events") }}?upcoming=true');

        if (response.ok) {
            const upcomingTasks = await response.json();
            console.log('Upcoming tasks loaded:', upcomingTasks.length);
            displayUpcomingTasks(upcomingTasks);
        } else {
            throw new Error('Failed to load upcoming tasks');
        }
    } catch (error) {
        console.error('Error loading upcoming tasks:', error);
        document.getElementById('upcomingTasks').innerHTML = `
            <div class="text-center py-4 text-gray-500">
                <p class="text-sm">Unable to load upcoming tasks</p>
            </div>
        `;
    }
}

function displayUpcomingTasks(tasks) {
    const container = document.getElementById('upcomingTasks');

    if (!tasks || tasks.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4 text-gray-500">
                <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm">No upcoming deadlines</p>
            </div>
        `;
        return;
    }

    const sortedTasks = tasks
        .sort((a, b) => {
            if (a.has_due_date && !b.has_due_date) return -1;
            if (!a.has_due_date && b.has_due_date) return 1;
            if (a.has_due_date && b.has_due_date) {
                return new Date(a.start) - new Date(b.start);
            }
            return 0;
        })
        .slice(0, 5);

    console.log('Displaying upcoming tasks:', sortedTasks);

    container.innerHTML = sortedTasks.map(task => {
        const isCompleted = task.is_completed || task.extendedProps?.is_completed;
        const hasDueDate = task.has_due_date || task.extendedProps?.has_due_date;
        const dueDateText = hasDueDate ? formatDate(task.start) : 'No date';

        const taskClass = isCompleted ?
            'bg-gray-100 border-gray-300 text-gray-500' :
            `bg-gray-50 border-gray-200`;

        return `
        <div class="flex items-start p-3 rounded-lg border cursor-pointer transition duration-200 ${taskClass}"
             onclick="showTaskModal(${JSON.stringify(task).replace(/"/g, '&quot;')})">
            <div class="flex-shrink-0 w-2 h-2 mt-2 rounded-full ${isCompleted ? 'bg-gray-400' : (hasDueDate ? getPriorityDotColor(task.priority) : 'bg-yellow-500')}"></div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium truncate ${isCompleted ? 'line-through text-gray-500' : 'text-gray-900'}">${task.title}</p>
                <p class="text-xs ${isCompleted ? 'text-gray-400' : 'text-gray-500'}">${dueDateText} • ${task.extendedProps?.project || 'No project'}</p>
            </div>
        </div>
    `}).join('');
}

function generateCalendar() {
    const calendarGrid = document.getElementById('calendarGrid');
    const currentMonthEl = document.getElementById('currentMonth');

    calendarGrid.innerHTML = '';

    currentMonthEl.textContent = currentDate.toLocaleDateString('en-US', {
        month: 'long',
        year: 'numeric'
    });

    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
    const totalDays = lastDay.getDate();
    const startingDay = firstDay.getDay();
    const adjustedStart = startingDay === 0 ? 6 : startingDay - 1;

    for (let i = 0; i < adjustedStart; i++) {
        const emptyCell = createDayCell(null, true);
        calendarGrid.appendChild(emptyCell);
    }

    for (let day = 1; day <= totalDays; day++) {
        const dateStr = `${currentDate.getFullYear()}-${(currentDate.getMonth() + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
        const dayCell = createDayCell(day, false, dateStr);
        calendarGrid.appendChild(dayCell);
    }

    const noDateTasks = allTasks.filter(task => !task.has_due_date);
    if (noDateTasks.length > 0) {
        const noDateCell = document.createElement('div');
        noDateCell.className = 'col-span-7 bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4';
        noDateCell.innerHTML = `
            <div class="flex items-center mb-2">
                <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-sm font-medium text-yellow-800">Tasks Without Due Dates (${noDateTasks.length})</h3>
            </div>
            <div class="space-y-2">
                ${noDateTasks.map(task => {
                    const isCompleted = task.is_completed || task.extendedProps?.is_completed;
                    const taskClass = isCompleted ?
                        'bg-gray-100 border-gray-300 text-gray-500 line-through' :
                        `border-yellow-500 bg-yellow-100`;

                    return `
                    <div class="text-xs p-2 rounded border-l-2 cursor-pointer hover:opacity-80 transition duration-200 ${taskClass}"
                         onclick="showTaskModal(${JSON.stringify(task).replace(/"/g, '&quot;')})">
                        <div class="font-medium">${task.title}</div>
                        <div class="text-gray-600">${task.extendedProps?.project || 'No project'} • No due date</div>
                    </div>
                `}).join('')}
            </div>
        `;
        calendarGrid.appendChild(noDateCell);
    }

    if (allTasks.length === 0) {
        showCalendarEmptyState();
    }

    console.log('Calendar generated with', allTasks.length, 'tasks');
}

function createDayCell(day, isEmpty, dateStr = null) {
    const dayCell = document.createElement('div');
    dayCell.className = `min-h-[120px] border-r border-b border-gray-200 p-3 ${
        isEmpty ? 'bg-gray-50' : 'bg-white hover:bg-gray-50 cursor-pointer'
    } transition duration-200`;

    if (isEmpty) {
        return dayCell;
    }

    if (dateStr) {
        dayCell.setAttribute('data-date', dateStr);
    }

    const isToday = isCurrentDay(day);
    const dayTasks = getTasksForDate(dateStr);

    dayCell.innerHTML = `
        <div class="flex justify-between items-start mb-2">
            <span class="text-sm font-medium ${
                isToday ? 'bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs' : 'text-gray-900'
            }">${day}</span>
            ${dayTasks.length > 0 ? `<span class="text-xs text-gray-500">${dayTasks.length}</span>` : ''}
        </div>
        <div class="space-y-1 max-h-20 overflow-y-auto">
            ${dayTasks.map(task => {
                const isCompleted = task.is_completed || task.extendedProps?.is_completed;
                const taskClass = isCompleted ?
                    'bg-gray-100 border-gray-300 text-gray-500 line-through' :
                    `${getPriorityBorderColor(task.priority)}`;

                return `
                <div class="text-xs p-1 rounded border-l-2 cursor-pointer hover:opacity-80 transition duration-200 ${taskClass}"
                     onclick="showTaskModal(${JSON.stringify(task).replace(/"/g, '&quot;')})">
                    <div class="font-medium truncate">${task.title}</div>
                    <div class="truncate">${task.extendedProps?.project || 'No project'}</div>
                </div>
            `}).join('')}
        </div>
    `;

    return dayCell;
}

function isCurrentDay(day) {
    const today = new Date();
    return today.getDate() === day &&
           today.getMonth() === currentDate.getMonth() &&
           today.getFullYear() === currentDate.getFullYear();
}

function getTasksForDate(dateStr) {
    return allTasks.filter(task => task.start === dateStr && task.has_due_date);
}

function getPriorityColor(priority) {
    switch(priority) {
        case 'high': return 'text-red-900';
        case 'medium': return 'text-orange-900';
        case 'low': return 'text-green-900';
        default: return 'text-gray-900';
    }
}

function getPriorityDotColor(priority) {
    switch(priority) {
        case 'high': return 'bg-red-500';
        case 'medium': return 'bg-orange-500';
        case 'low': return 'bg-green-500';
        default: return 'bg-gray-400';
    }
}

function getPriorityBorderColor(priority) {
    switch(priority) {
        case 'high': return 'border-red-500 bg-red-50';
        case 'medium': return 'border-orange-500 bg-orange-50';
        case 'low': return 'border-green-500 bg-green-50';
        default: return 'border-gray-400 bg-gray-50';
    }
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function changeMonth(direction) {
    currentDate.setMonth(currentDate.getMonth() + direction);
    loadTasksForMonth(currentDate.getFullYear(), currentDate.getMonth());
}

function showToday() {
    currentDate = new Date();
    loadTasksForMonth(currentDate.getFullYear(), currentDate.getMonth());
}

function showTaskModal(task) {
    const modal = document.getElementById('taskModal');
    const isCompleted = task.is_completed || task.extendedProps?.is_completed;
    const hasDueDate = task.has_due_date || task.extendedProps?.has_due_date;

    document.getElementById('modalTitle').textContent = task.title;
    document.getElementById('modalProject').textContent = task.extendedProps?.project || 'No project';
    document.getElementById('modalAssigned').textContent = task.extendedProps?.assigned_to || 'Unassigned';

    if (hasDueDate) {
        document.getElementById('modalDueDate').textContent = new Date(task.start).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } else {
        document.getElementById('modalDueDate').textContent = 'No due date set';
    }

    const priorityEl = document.getElementById('modalPriority');
    const priority = task.priority || 'medium';
    priorityEl.textContent = priority.charAt(0).toUpperCase() + priority.slice(1);
    priorityEl.className = 'px-3 py-1 rounded-full text-sm font-medium ' + getPriorityBackgroundColor(priority);

    const statusEl = document.getElementById('modalStatus');
    const status = task.extendedProps?.status || 'todo';
    statusEl.textContent = status.replace('_', ' ').charAt(0).toUpperCase() + status.replace('_', ' ').slice(1);
    statusEl.className = 'px-3 py-1 rounded-full text-sm font-medium ' + getStatusBackgroundColor(status, isCompleted);

    document.getElementById('modalDescription').textContent =
        task.extendedProps?.description || 'No description provided.';

    if (task.id && typeof task.id === 'number') {
        document.getElementById('modalTaskLink').href = `/manager/tasks/${task.id}`;
        document.getElementById('modalTaskLink').style.display = 'block';
    } else {
        document.getElementById('modalTaskLink').style.display = 'none';
    }

    if (isCompleted) {
        document.getElementById('modalTitle').classList.add('line-through', 'text-gray-500');
    } else {
        document.getElementById('modalTitle').classList.remove('line-through', 'text-gray-500');
    }

    modal.classList.remove('hidden');
}

function getPriorityBackgroundColor(priority) {
    switch(priority) {
        case 'high': return 'bg-red-100 text-red-800';
        case 'medium': return 'bg-orange-100 text-orange-800';
        case 'low': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getStatusBackgroundColor(status, isCompleted) {
    if (isCompleted) return 'bg-gray-100 text-gray-800';

    switch(status) {
        case 'todo': return 'bg-blue-100 text-blue-800';
        case 'in_progress': return 'bg-yellow-100 text-yellow-800';
        case 'done': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function closeModal() {
    document.getElementById('taskModal').classList.add('hidden');
}

function showLoading() {
    const calendarGrid = document.getElementById('calendarGrid');
    calendarGrid.innerHTML = `
        <div class="col-span-7 flex items-center justify-center py-12">
            <div class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Loading calendar...</p>
            </div>
        </div>
    `;
}

function showError(message) {
    const calendarGrid = document.getElementById('calendarGrid');
    calendarGrid.innerHTML = `
        <div class="col-span-7 text-center py-12">
            <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Unable to load calendar</h3>
            <p class="text-gray-600 mb-4">${message}</p>
            <button onclick="loadCalendarData()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                Try Again
            </button>
        </div>
    `;
}

function showCalendarEmptyState() {
    const calendarGrid = document.getElementById('calendarGrid');
    const today = new Date();
    const currentMonth = today.getMonth();
    const currentYear = today.getFullYear();

    if (currentDate.getMonth() === currentMonth && currentDate.getFullYear() === currentYear) {
        const todayCell = document.querySelector(`[data-date="${today.toISOString().split('T')[0]}"]`);
        if (todayCell) {
            todayCell.innerHTML += `
                <div class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg text-center">
                    <p class="text-xs text-blue-700">No tasks today</p>
                    <a href="{{ route('manager.tasks.create') }}" class="text-xs text-blue-600 hover:text-blue-800 underline">
                        Create a task
                    </a>
                </div>
            `;
        }
    } else {
        const firstDayCell = document.querySelector('[data-date]');
        if (firstDayCell) {
            calendarGrid.innerHTML += `
                <div class="col-span-7 text-center py-8">
                    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-600">No tasks for ${currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}</p>
                </div>
            `;
        }
    }
}

document.getElementById('taskModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection
