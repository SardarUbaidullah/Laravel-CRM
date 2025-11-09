@extends('admin.layouts.app')

@section('title', 'Time Tracking Reports')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <h1 class="text-3xl font-bold text-gray-900">Time Tracking Reports</h1>
                    <span class="ml-3 text-sm text-gray-500">Track and analyze team time</span>
                </div>
                <div class="flex space-x-3">
                    <button id="refreshBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-refresh mr-2"></i>Refresh
                    </button>
                    <select id="dateRange" class="block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg">
                        <option value="7">Last 7 Days</option>
                        <option value="30" selected>Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                        <option value="365">Last Year</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Timer Alert -->
    <div id="runningTimerAlert" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Dynamic content -->
    </div>

    <!-- Quick Stats -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-blue-600" id="totalTime">--</div>
                <div class="text-sm text-gray-600 mt-1">Total Time</div>
                <div class="text-xs text-gray-400" id="timePeriod">Last 30 days</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-green-600" id="totalTasks">--</div>
                <div class="text-sm text-gray-600 mt-1">Tasks Tracked</div>
                <div class="text-xs text-gray-400">With time entries</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-purple-600" id="teamMembers">--</div>
                <div class="text-sm text-gray-600 mt-1">Team Members</div>
                <div class="text-xs text-gray-400">Active trackers</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <div class="text-2xl font-bold text-orange-600" id="avgDaily">--</div>
                <div class="text-sm text-gray-600 mt-1">Avg. Daily</div>
                <div class="text-xs text-gray-400">Time spent</div>
            </div>
        </div>

        <!-- Kanban Style Reports -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
            <!-- Time by User -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-users text-blue-500 mr-2"></i>
                        Time by Team
                    </h3>
                </div>
                <div class="p-4" id="timeByUser">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                        <p class="text-gray-500 text-sm">Loading team data...</p>
                    </div>
                </div>
            </div>

            <!-- Time by Project -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-project-diagram text-green-500 mr-2"></i>
                        Time by Project
                    </h3>
                </div>
                <div class="p-4" id="timeByProject">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600 mx-auto mb-2"></div>
                        <p class="text-gray-500 text-sm">Loading project data...</p>
                    </div>
                </div>
            </div>

            <!-- Top Tasks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-tasks text-purple-500 mr-2"></i>
                        Top Tasks
                    </h3>
                </div>
                <div class="p-4" id="timeByTask">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto mb-2"></div>
                        <p class="text-gray-500 text-sm">Loading tasks data...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Reports Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Detailed Time Entries</h3>
                    <div class="flex space-x-2">
                        <button id="exportBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                            <i class="fas fa-download mr-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div id="detailedReport">
                    <div class="text-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600">Loading detailed report...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentDateRange = '30';

    // Initialize
    loadRunningTimer();
    loadTimeSummary();
    loadDetailedReport();

    // Event listeners
    document.getElementById('refreshBtn').addEventListener('click', refreshAll);
    document.getElementById('dateRange').addEventListener('change', filterReports);
    document.getElementById('exportBtn').addEventListener('click', exportReport);

    // Load running timer
    async function loadRunningTimer() {
        try {
            const response = await fetch('/admin/time-tracking/running-timer');
            const data = await response.json();

            const alertDiv = document.getElementById('runningTimerAlert');

            if (data.has_running_timer && data.timer) {
                alertDiv.innerHTML = `
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 fade-in">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-play text-white text-sm"></i>
                                </div>
                                <div>
                                    <div class="font-semibold text-yellow-800">Timer Running</div>
                                    <div class="text-sm text-yellow-600">${data.timer.task.title} - ${data.timer.user.name}</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="text-yellow-700 font-mono" id="globalTimer">00:00:00</span>
                                <button onclick="stopGlobalTimer(${data.timer.id})"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                                    <i class="fas fa-stop mr-2"></i>Stop Timer
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                startGlobalTimer(data.timer.start_time);
            } else {
                alertDiv.innerHTML = '';
            }
        } catch (error) {
            console.error('Error loading timer:', error);
        }
    }

    // Global timer functions
    function startGlobalTimer(startTime) {
        const start = new Date(startTime);
        setInterval(() => {
            const now = new Date();
            const diff = now - start;

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            const timerElement = document.getElementById('globalTimer');
            if (timerElement) {
                timerElement.textContent =
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
        }, 1000);
    }

    window.stopGlobalTimer = async function(timeLogId) {
        try {
            const response = await fetch('/admin/time-tracking/stop-timer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ time_log_id: timeLogId })
            });

            const data = await response.json();
            if (data.success) {
                showNotification('Timer stopped: ' + data.duration, 'success');
                refreshAll();
            }
        } catch (error) {
            showNotification('Error stopping timer', 'error');
        }
    };

    // Load time summary
    async function loadTimeSummary() {
        try {
            const response = await fetch(`/admin/time-reports/summary?range=${currentDateRange}`);
            const data = await response.json();

            if (data.error) {
                throw new Error(data.message);
            }

            // Update summary cards
            document.getElementById('totalTime').textContent = data.summary.formatted_total_time;
            document.getElementById('timePeriod').textContent = `Last ${data.summary.period}`;
            document.getElementById('totalTasks').textContent = data.summary.total_tasks_tracked;
            document.getElementById('teamMembers').textContent = data.summary.team_members;
            document.getElementById('avgDaily').textContent = data.summary.avg_daily_formatted;

            // Render Kanban cards
            renderTimeByUser(data.time_by_user);
            renderTimeByProject(data.time_by_project);
            renderTimeByTask(data.time_by_task);

        } catch (error) {
            console.error('Error loading time summary:', error);
            showNotification('Failed to load summary data', 'error');
        }
    }

    // Render Time by User
    function renderTimeByUser(users) {
        const container = document.getElementById('timeByUser');

        if (!users || users.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-users text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">No time data available</p>
                </div>
            `;
            return;
        }

        container.innerHTML = users.map(user => `
            <div class="flex items-center justify-between p-3 mb-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <span class="text-xs font-medium text-white">${user.user.name.charAt(0)}</span>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900 text-sm">${user.user.name}</div>
                        <div class="text-xs text-gray-500">${user.total_hours} hours</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-blue-600 text-sm">${user.formatted_time}</div>
                </div>
            </div>
        `).join('');
    }

    // Render Time by Project
    function renderTimeByProject(projects) {
        const container = document.getElementById('timeByProject');

        if (!projects || projects.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-project-diagram text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">No project data available</p>
                </div>
            `;
            return;
        }

        container.innerHTML = projects.map(project => `
            <div class="flex items-center justify-between p-3 mb-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-3 flex-1">
                    <div class="w-2 h-8 bg-green-500 rounded-full"></div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-900 text-sm truncate">${project.project.name}</div>
                        <div class="text-xs text-gray-500">${project.total_hours} hours</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-bold text-green-600 text-sm">${project.formatted_time}</div>
                </div>
            </div>
        `).join('');
    }

    // Render Time by Task
    function renderTimeByTask(tasks) {
        const container = document.getElementById('timeByTask');

        if (!tasks || tasks.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-tasks text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm">No task data available</p>
                </div>
            `;
            return;
        }

        container.innerHTML = tasks.map(task => `
            <div class="p-3 mb-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="font-medium text-gray-900 text-sm mb-1 truncate">${task.task.title}</div>
                <div class="text-xs text-gray-500 mb-2">${task.task.project?.name || 'No Project'}</div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-600">${task.total_hours} hours</span>
                    <span class="font-bold text-purple-600 text-sm">${task.formatted_time}</span>
                </div>
            </div>
        `).join('');
    }

    // Load detailed report
    async function loadDetailedReport() {
        try {
            const response = await fetch('/admin/time-reports/detailed');
            const data = await response.json();

            if (data.error) {
                throw new Error(data.message);
            }

            renderDetailedReport(data.time_logs);

        } catch (error) {
            console.error('Error loading detailed report:', error);
            document.getElementById('detailedReport').innerHTML = `
                <div class="text-center py-8 text-red-600">
                    <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                    <p>Failed to load detailed report</p>
                </div>
            `;
        }
    }

    // Render detailed report
    function renderDetailedReport(timeLogs) {
        const container = document.getElementById('detailedReport');

        if (!timeLogs || timeLogs.data.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-clock text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500">No time entries found</p>
                </div>
            `;
            return;
        }

        container.innerHTML = `
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task & Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${timeLogs.data.map(entry => `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">${entry.user_name}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">${entry.task_name}</div>
                                    <div class="text-sm text-gray-500">${entry.project_name}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">${entry.description || 'No description'}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${entry.date}</div>
                                    <div class="text-sm text-gray-500">${entry.start_time.split(' ')[1]}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-green-600">${entry.formatted_duration}</div>
                                    <div class="text-sm text-gray-500">${entry.duration_hours} hours</div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
            ${timeLogs.links ? `
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing ${timeLogs.from} to ${timeLogs.to} of ${timeLogs.total} entries
                </div>
                <div class="flex space-x-2">
                    ${timeLogs.links.map(link => `
                        <a href="${link.url}" class="px-3 py-1 text-sm ${link.active ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'} rounded">
                            ${link.label.replace('&laquo;', '«').replace('&raquo;', '»')}
                        </a>
                    `).join('')}
                </div>
            </div>
            ` : ''}
        `;
    }

    // Utility functions
    function filterReports() {
        currentDateRange = document.getElementById('dateRange').value;
        loadTimeSummary();
    }

    function refreshAll() {
        loadRunningTimer();
        loadTimeSummary();
        loadDetailedReport();
        showNotification('Data refreshed successfully', 'success');
    }

    async function exportReport() {
        try {
            const response = await fetch('/admin/time-reports/export', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ type: 'detailed' })
            });

            const data = await response.json();
            if (data.success) {
                showNotification('Report exported successfully', 'success');
                // Here you can trigger download if needed
            }
        } catch (error) {
            showNotification('Error exporting report', 'error');
        }
    }

    function showNotification(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 fade-in ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-triangle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>

<style>
.fade-in {
    animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.hover-lift:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease-in-out;
}


</style>
@endsection
