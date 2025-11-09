<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .report-tab {
            transition: all 0.2s ease-in-out;
        }
        .active-tab {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            background-color: white;
            color: #2563eb;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <h1 class="text-3xl font-bold text-gray-900">Project Analytics</h1>
                    <span class="ml-3 text-sm text-gray-500">Progress, workload, and performance analytics</span>
                </div>
                <button type="button" id="refreshBtn" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200">
                    <i class="fas fa-refresh mr-2"></i>Refresh Data
                </button>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Projects Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-project-diagram text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Total Projects</h3>
                        <p id="totalProjects" class="text-2xl font-bold text-gray-900">--</p>
                        <p class="text-sm text-green-600" id="projectGrowth">Active projects</p>
                    </div>
                </div>
            </div>

            <!-- Tasks Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tasks text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Completed Tasks</h3>
                        <p id="completedTasks" class="text-2xl font-bold text-gray-900">--</p>
                        <p class="text-sm text-green-600" id="taskCompletionRate">All tasks</p>
                    </div>
                </div>
            </div>

            <!-- Team Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Team Members</h3>
                        <p id="activeTeam" class="text-2xl font-bold text-gray-900">--</p>
                        <p class="text-sm text-gray-600" id="teamProductivity">Active users</p>
                    </div>
                </div>
            </div>

            <!-- Performance Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Completion Rate</h3>
                        <p id="avgPerformance" class="text-2xl font-bold text-gray-900">--</p>
                        <p class="text-sm text-green-600" id="performanceTrend">Overall progress</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Navigation -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                <button type="button" id="progressTab" class="flex-1 py-3 px-4 text-center font-medium rounded-md transition duration-200 report-tab active-tab">
                    <i class="fas fa-chart-bar mr-2"></i>Progress
                </button>
                <button type="button" id="workloadTab" class="flex-1 py-3 px-4 text-center font-medium rounded-md transition duration-200 report-tab">
                    <i class="fas fa-user-check mr-2"></i>Workload
                </button>
                <button type="button" id="performanceTab" class="flex-1 py-3 px-4 text-center font-medium rounded-md transition duration-200 report-tab">
                    <i class="fas fa-trophy mr-2"></i>Performance
                </button>
            </div>
        </div>

        <!-- Report Content -->
        <div id="reportContent">
            <div class="text-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Loading analytics...</p>
            </div>
        </div>
    </div>

    <script>
        let currentReport = 'progress';

        document.addEventListener('DOMContentLoaded', function() {
            initDashboard();
        });

        function initDashboard() {
            document.getElementById('refreshBtn').addEventListener('click', refreshAllReports);
            document.getElementById('progressTab').addEventListener('click', () => showReport('progress'));
            document.getElementById('workloadTab').addEventListener('click', () => showReport('workload'));
            document.getElementById('performanceTab').addEventListener('click', () => showReport('performance'));

            loadQuickStats();
            loadReport('progress');
        }

        async function loadQuickStats() {
            try {
                const response = await fetch('/admin/reports/quick-stats');
                if (response.ok) {
                    const data = await response.json();
                    document.getElementById('totalProjects').textContent = data.total_projects || '0';
                    document.getElementById('completedTasks').textContent = data.completed_tasks || '0';
                    document.getElementById('activeTeam').textContent = data.active_team || '0';
                    document.getElementById('avgPerformance').textContent = data.avg_performance || '0%';
                }
            } catch (error) {
                console.error('Error loading quick stats:', error);
            }
        }

        async function loadReport(reportType) {
            currentReport = reportType;

            // Update active tab
            document.querySelectorAll('.report-tab').forEach(tab => {
                tab.classList.remove('active-tab');
            });
            document.getElementById(reportType + 'Tab').classList.add('active-tab');

            // Show loading
            const reportContent = document.getElementById('reportContent');
            reportContent.innerHTML = `
                <div class="text-center py-12">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">Loading ${reportType} report...</p>
                </div>
            `;

            try {
                const response = await fetch(`/admin/reports/data/${reportType}`);
                if (response.ok) {
                    const data = await response.json();
                    renderReport(reportType, data);
                } else {
                    throw new Error('Failed to load report');
                }
            } catch (error) {
                reportContent.innerHTML = `
                    <div class="text-center py-12 fade-in">
                        <i class="fas fa-exclamation-triangle text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Unable to load report</h3>
                        <p class="text-gray-600 mb-4">${error.message}</p>
                        <button type="button" onclick="loadReport('${reportType}')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                            Try Again
                        </button>
                    </div>
                `;
            }
        }

        function renderReport(reportType, data) {
            const reportContent = document.getElementById('reportContent');

            switch(reportType) {
                case 'progress':
                    renderProgressReport(data, reportContent);
                    break;
                case 'workload':
                    renderWorkloadReport(data, reportContent);
                    break;
                case 'performance':
                    renderPerformanceReport(data, reportContent);
                    break;
            }
        }

        function renderProgressReport(data, container) {
            const projects = data.projects || {};
            const tasks = data.tasks || {};
            const recentProjects = data.recent_projects || [];

            container.innerHTML = `
                <div class="space-y-6 fade-in">
                    <h2 class="text-2xl font-bold text-gray-900">Project Progress Analytics</h2>

                    <!-- Project Progress -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Projects Overview</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Total Projects</span>
                                    <span class="font-bold">${projects.total || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Completed</span>
                                    <span class="font-bold text-green-600">${projects.completed || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">In Progress</span>
                                    <span class="font-bold text-blue-600">${projects.in_progress || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Planning</span>
                                    <span class="font-bold text-yellow-600">${projects.planning || 0}</span>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium">Completion Rate</span>
                                        <span class="font-bold text-blue-600">${projects.completion_rate || 0}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: ${projects.completion_rate || 0}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tasks Overview</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Total Tasks</span>
                                    <span class="font-bold">${tasks.total || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Completed</span>
                                    <span class="font-bold text-green-600">${tasks.completed || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">In Progress</span>
                                    <span class="font-bold text-yellow-600">${tasks.in_progress || 0}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">To Do</span>
                                    <span class="font-bold text-gray-600">${tasks.todo || 0}</span>
                                </div>
                                <div class="pt-4 border-t border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="font-medium">Completion Rate</span>
                                        <span class="font-bold text-green-600">${tasks.completion_rate || 0}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: ${tasks.completion_rate || 0}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Projects -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Projects</h3>
                        <div class="space-y-3">
                            ${recentProjects.map(project => `
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full ${getProjectStatusColor(project.status)} mr-3"></div>
                                        <div>
                                            <div class="font-medium text-gray-900">${project.name}</div>
                                            <div class="text-sm text-gray-500">${project.completed_tasks}/${project.total_tasks} tasks completed</div>
                                        </div>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-medium rounded-full ${getProjectStatusBadgeColor(project.status)}">
                                        ${project.status || 'Unknown'}
                                    </span>
                                </div>
                            `).join('')}
                            ${recentProjects.length === 0 ? '<p class="text-center text-gray-500 py-4">No projects found</p>' : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderWorkloadReport(data, container) {
            const teamWorkload = data.team_workload || [];
            const summary = data.summary || {};

            container.innerHTML = `
                <div class="space-y-6 fade-in">
                    <h2 class="text-2xl font-bold text-gray-900">Team Workload Distribution</h2>

                    <!-- Summary -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg p-4 text-center border border-gray-200">
                            <div class="text-2xl font-bold text-blue-600">${summary.total_team_members || 0}</div>
                            <div class="text-sm text-gray-600">Team Members</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center border border-gray-200">
                            <div class="text-2xl font-bold text-green-600">${summary.total_assigned_tasks || 0}</div>
                            <div class="text-sm text-gray-600">Total Tasks</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center border border-gray-200">
                            <div class="text-2xl font-bold text-purple-600">${summary.total_completed || 0}</div>
                            <div class="text-sm text-gray-600">Completed</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center border border-gray-200">
                            <div class="text-2xl font-bold text-yellow-600">${summary.total_in_progress || 0}</div>
                            <div class="text-sm text-gray-600">In Progress</div>
                        </div>
                    </div>

                    <!-- Team Workload -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Team Workload</h3>
                        <div class="space-y-4">
                            ${teamWorkload.map(member => `
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">${member.user.name.charAt(0)}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">${member.user.name}</div>
                                            <div class="text-sm text-gray-500">${member.total_tasks || 0} total tasks</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-6">
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-green-600">${member.completed_tasks || 0}</div>
                                            <div class="text-xs text-gray-500">Done</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-yellow-600">${member.in_progress_tasks || 0}</div>
                                            <div class="text-xs text-gray-500">In Progress</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-gray-600">${member.todo_tasks || 0}</div>
                                            <div class="text-xs text-gray-500">To Do</div>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full ${getWorkloadLevelColor(member.workload_level)}">
                                            ${member.workload_level}
                                        </span>
                                    </div>
                                </div>
                            `).join('')}
                            ${teamWorkload.length === 0 ? '<p class="text-center text-gray-500 py-4">No team members found</p>' : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        function renderPerformanceReport(data, container) {
            const userPerformance = data.user_performance || [];
            const qualityMetrics = data.quality_metrics || {};

            container.innerHTML = `
                <div class="space-y-6 fade-in">
                    <h2 class="text-2xl font-bold text-gray-900">Team Performance Metrics</h2>

                    <!-- Quality Metrics -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white rounded-lg p-4 text-center border border-gray-200">
                            <div class="text-2xl font-bold text-blue-600">${qualityMetrics.total_tasks || 0}</div>
                            <div class="text-sm text-gray-600">Total Tasks</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center border border-gray-200">
                            <div class="text-2xl font-bold text-green-600">${qualityMetrics.completed_tasks || 0}</div>
                            <div class="text-sm text-gray-600">Completed</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center border border-gray-200">
                            <div class="text-2xl font-bold text-yellow-600">${qualityMetrics.in_progress_tasks || 0}</div>
                            <div class="text-sm text-gray-600">In Progress</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 text-center border border-gray-200">
                            <div class="text-2xl font-bold text-purple-600">${Math.round(qualityMetrics.team_productivity || 0)}%</div>
                            <div class="text-sm text-gray-600">Team Productivity</div>
                        </div>
                    </div>

                    <!-- Individual Performance -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Individual Performance</h3>
                        <div class="space-y-4">
                            ${userPerformance.map(performance => `
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">${performance.user.name.charAt(0)}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">${performance.user.name}</div>
                                            <div class="text-sm text-gray-500">${performance.total_tasks || 0} assigned tasks</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-6">
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-green-600">${performance.completed_tasks || 0}</div>
                                            <div class="text-xs text-gray-500">Completed</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-bold ${getCompletionRateColor(performance.completion_rate || 0)}">${performance.completion_rate || 0}%</div>
                                            <div class="text-xs text-gray-500">Completion Rate</div>
                                        </div>
                                        <span class="px-3 py-1 text-xs font-medium rounded-full ${getPerformanceLevelColor(performance.performance_level)}">
                                            ${performance.performance_level}
                                        </span>
                                    </div>
                                </div>
                            `).join('')}
                            ${userPerformance.length === 0 ? '<p class="text-center text-gray-500 py-4">No performance data available</p>' : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        // Utility functions
        function getProjectStatusColor(status) {
            const colors = {
                'completed': 'bg-green-500',
                'pending': 'bg-yellow-500',
                'planning': 'bg-blue-500'
            };
            return colors[status] || 'bg-gray-400';
        }

        function getProjectStatusBadgeColor(status) {
            const colors = {
                'completed': 'bg-green-100 text-green-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'planning': 'bg-blue-100 text-blue-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        function getWorkloadLevelColor(level) {
            const colors = {
                'Low': 'bg-green-100 text-green-800',
                'Normal': 'bg-blue-100 text-blue-800',
                'High': 'bg-yellow-100 text-yellow-800',
                'Overloaded': 'bg-red-100 text-red-800'
            };
            return colors[level] || 'bg-gray-100 text-gray-800';
        }

        function getPerformanceLevelColor(level) {
            const colors = {
                'Excellent': 'bg-green-100 text-green-800',
                'Very Good': 'bg-blue-100 text-blue-800',
                'Good': 'bg-yellow-100 text-yellow-800',
                'Average': 'bg-orange-100 text-orange-800',
                'Needs Improvement': 'bg-red-100 text-red-800'
            };
            return colors[level] || 'bg-gray-100 text-gray-800';
        }

        function getCompletionRateColor(rate) {
            if (rate >= 80) return 'text-green-600';
            if (rate >= 60) return 'text-yellow-600';
            return 'text-red-600';
        }

        function refreshAllReports() {
            loadQuickStats();
            loadReport(currentReport);
        }

        function showReport(reportType) {
            loadReport(reportType);
        }
    </script>
</body>
</html>
