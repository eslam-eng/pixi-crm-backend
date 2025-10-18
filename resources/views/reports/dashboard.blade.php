<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Reports Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .chart-container {
            position: relative;
            height: 400px;
            width: 100%;
        }

        .report-card {
            transition: transform 0.2s ease-in-out;
        }

        .report-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <h1 class="text-2xl font-bold text-gray-900">CRM Reports Dashboard</h1>
                    <div class="flex space-x-4">
                        <button id="createReportBtn"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Create Report
                        </button>
                        <button id="refreshBtn" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Report Categories -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Report Categories</h2>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="report-card bg-white p-4 rounded-lg shadow cursor-pointer"
                        data-category="sales_performance">
                        <div class="text-center">
                            <div class="text-2xl mb-2">📊</div>
                            <h3 class="font-medium">Sales Performance</h3>
                            <p class="text-sm text-gray-600">Deals & Revenue</p>
                        </div>
                    </div>
                    <div class="report-card bg-white p-4 rounded-lg shadow cursor-pointer"
                        data-category="lead_management">
                        <div class="text-center">
                            <div class="text-2xl mb-2">🎯</div>
                            <h3 class="font-medium">Lead Management</h3>
                            <p class="text-sm text-gray-600">Leads & Conversion</p>
                        </div>
                    </div>
                    <div class="report-card bg-white p-4 rounded-lg shadow cursor-pointer"
                        data-category="team_performance">
                        <div class="text-center">
                            <div class="text-2xl mb-2">👥</div>
                            <h3 class="font-medium">Team Performance</h3>
                            <p class="text-sm text-gray-600">Team Metrics</p>
                        </div>
                    </div>
                    <div class="report-card bg-white p-4 rounded-lg shadow cursor-pointer"
                        data-category="task_completion">
                        <div class="text-center">
                            <div class="text-2xl mb-2">✅</div>
                            <h3 class="font-medium">Task Completion</h3>
                            <p class="text-sm text-gray-600">Tasks & Productivity</p>
                        </div>
                    </div>
                    <div class="report-card bg-white p-4 rounded-lg shadow cursor-pointer"
                        data-category="revenue_analysis">
                        <div class="text-center">
                            <div class="text-2xl mb-2">💰</div>
                            <h3 class="font-medium">Revenue Analysis</h3>
                            <p class="text-sm text-gray-600">Financial Reports</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Stats</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="text-2xl text-blue-600 mr-3">📈</div>
                            <div>
                                <p class="text-sm text-gray-600">Total Pipeline Value</p>
                                <p class="text-2xl font-bold" id="pipelineValue">$0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="text-2xl text-green-600 mr-3">🎯</div>
                            <div>
                                <p class="text-sm text-gray-600">Active Leads</p>
                                <p class="text-2xl font-bold" id="activeLeads">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="text-2xl text-purple-600 mr-3">✅</div>
                            <div>
                                <p class="text-sm text-gray-600">Completed Tasks</p>
                                <p class="text-2xl font-bold" id="completedTasks">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <div class="flex items-center">
                            <div class="text-2xl text-orange-600 mr-3">👥</div>
                            <div>
                                <p class="text-sm text-gray-600">Team Members</p>
                                <p class="text-2xl font-bold" id="teamMembers">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Pipeline Funnel Chart -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Pipeline Funnel</h3>
                    <div class="chart-container">
                        <canvas id="pipelineChart"></canvas>
                    </div>
                </div>

                <!-- Revenue Trend Chart -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h3 class="text-lg font-semibold mb-4">Revenue Trend</h3>
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Reports</h2>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Report Name</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Last Run</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody id="reportsTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Reports will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Create Report Modal -->
    <div id="createReportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Create New Report</h3>
                    <form id="createReportForm">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Report Name</label>
                            <input type="text" id="reportName"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="reportDescription"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                rows="3"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select id="reportCategory"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">Select Category</option>
                                <option value="sales_performance">Sales Performance</option>
                                <option value="lead_management">Lead Management</option>
                                <option value="team_performance">Team Performance</option>
                                <option value="task_completion">Task Completion</option>
                                <option value="revenue_analysis">Revenue Analysis</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                            <select id="reportType"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">Select Type</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancelCreateBtn"
                                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Create
                                Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let pipelineChart, revenueChart;
        const API_BASE_URL = '/api/reports';

        // Initialize the dashboard
        document.addEventListener('DOMContentLoaded', function() {
            initializeDashboard();
            setupEventListeners();
            loadDashboardData();
        });

        function initializeDashboard() {
            // Initialize charts
            initializePipelineChart();
            initializeRevenueChart();
        }

        function setupEventListeners() {
            // Category cards
            document.querySelectorAll('.report-card').forEach(card => {
                card.addEventListener('click', function() {
                    const category = this.dataset.category;
                    loadCategoryReports(category);
                });
            });

            // Create report button
            document.getElementById('createReportBtn').addEventListener('click', function() {
                document.getElementById('createReportModal').classList.remove('hidden');
            });

            // Cancel create button
            document.getElementById('cancelCreateBtn').addEventListener('click', function() {
                document.getElementById('createReportModal').classList.add('hidden');
            });

            // Create report form
            document.getElementById('createReportForm').addEventListener('submit', function(e) {
                e.preventDefault();
                createReport();
            });

            // Category change handler
            document.getElementById('reportCategory').addEventListener('change', function() {
                loadReportTypes(this.value);
            });

            // Refresh button
            document.getElementById('refreshBtn').addEventListener('click', function() {
                loadDashboardData();
            });
        }

        function loadDashboardData() {
            // Load quick stats
            loadQuickStats();
            
            // Load recent reports
            loadRecentReports();
            
            // Load chart data
            loadChartData();
        }

        async function loadQuickStats() {
            try {
                // This would make API calls to get actual data
                // For demo purposes, using mock data
                document.getElementById('pipelineValue').textContent = '$125,000';
                document.getElementById('activeLeads').textContent = '45';
                document.getElementById('completedTasks').textContent = '128';
                document.getElementById('teamMembers').textContent = '12';
            } catch (error) {
                console.error('Error loading quick stats:', error);
            }
        }

        async function loadRecentReports() {
            try {
                const response = await fetch(`${API_BASE_URL}`);
                const data = await response.json();
                
                const tbody = document.getElementById('reportsTableBody');
                tbody.innerHTML = '';
                
                if (data.data && data.data.length > 0) {
                    data.data.forEach(report => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${report.name}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${report.category}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${report.last_run_at || 'Never'}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${report.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${report.is_active ? 'Active' : 'Inactive'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="executeReport(${report.id})" class="text-blue-600 hover:text-blue-900 mr-3">Execute</button>
                                <button onclick="exportReport(${report.id})" class="text-green-600 hover:text-green-900">Export</button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No reports found</td></tr>';
                }
            } catch (error) {
                console.error('Error loading recent reports:', error);
            }
        }

        function loadChartData() {
            // Mock data for charts
            updatePipelineChart([
                { stage: 'Prospecting', count: 25, value: 50000 },
                { stage: 'Qualification', count: 18, value: 35000 },
                { stage: 'Proposal', count: 12, value: 25000 },
                { stage: 'Negotiation', count: 8, value: 15000 },
                { stage: 'Closed Won', count: 5, value: 10000 }
            ]);

            updateRevenueChart([
                { month: 'Jan', revenue: 25000 },
                { month: 'Feb', revenue: 32000 },
                { month: 'Mar', revenue: 28000 },
                { month: 'Apr', revenue: 35000 },
                { month: 'May', revenue: 42000 },
                { month: 'Jun', revenue: 38000 }
            ]);
        }

        function initializePipelineChart() {
            const ctx = document.getElementById('pipelineChart').getContext('2d');
            pipelineChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Deals Count',
                        data: [],
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function initializeRevenueChart() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            revenueChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Revenue',
                        data: [],
                        borderColor: 'rgba(34, 197, 94, 1)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function updatePipelineChart(data) {
            pipelineChart.data.labels = data.map(item => item.stage);
            pipelineChart.data.datasets[0].data = data.map(item => item.count);
            pipelineChart.update();
        }

        function updateRevenueChart(data) {
            revenueChart.data.labels = data.map(item => item.month);
            revenueChart.data.datasets[0].data = data.map(item => item.revenue);
            revenueChart.update();
        }

        async function loadCategoryReports(category) {
            try {
                const response = await fetch(`${API_BASE_URL}/sales-performance/deals-performance`);
                const data = await response.json();
                
                // Update charts with category-specific data
                console.log('Category reports loaded:', data);
            } catch (error) {
                console.error('Error loading category reports:', error);
            }
        }

        async function loadReportTypes(category) {
            try {
                const response = await fetch(`${API_BASE_URL}/types?category=${category}`);
                const data = await response.json();
                
                const select = document.getElementById('reportType');
                select.innerHTML = '<option value="">Select Type</option>';
                
                if (data[category]) {
                    Object.entries(data[category]).forEach(([key, value]) => {
                        const option = document.createElement('option');
                        option.value = key;
                        option.textContent = value;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading report types:', error);
            }
        }

        async function createReport() {
            try {
                const formData = {
                    name: document.getElementById('reportName').value,
                    description: document.getElementById('reportDescription').value,
                    category: document.getElementById('reportCategory').value,
                    report_type: document.getElementById('reportType').value,
                    is_active: true,
                    is_scheduled: false
                };

                const response = await fetch(`${API_BASE_URL}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + getAuthToken()
                    },
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    document.getElementById('createReportModal').classList.add('hidden');
                    document.getElementById('createReportForm').reset();
                    loadRecentReports();
                    alert('Report created successfully!');
                } else {
                    alert('Error creating report');
                }
            } catch (error) {
                console.error('Error creating report:', error);
                alert('Error creating report');
            }
        }

        async function executeReport(reportId) {
            try {
                const response = await fetch(`${API_BASE_URL}/${reportId}/execute`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + getAuthToken()
                    }
                });

                if (response.ok) {
                    alert('Report executed successfully!');
                    loadRecentReports();
                } else {
                    alert('Error executing report');
                }
            } catch (error) {
                console.error('Error executing report:', error);
                alert('Error executing report');
            }
        }

        async function exportReport(reportId) {
            try {
                const response = await fetch(`${API_BASE_URL}/${reportId}/export`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + getAuthToken()
                    },
                    body: JSON.stringify({ format: 'excel' })
                });

                if (response.ok) {
                    const data = await response.json();
                    if (data.download_url) {
                        window.open(data.download_url, '_blank');
                    }
                    alert('Report exported successfully!');
                } else {
                    alert('Error exporting report');
                }
            } catch (error) {
                console.error('Error exporting report:', error);
                alert('Error exporting report');
            }
        }

        function getAuthToken() {
            // This would get the actual auth token from your auth system
            return 'your-auth-token-here';
        }
    </script>
</body>

</html>