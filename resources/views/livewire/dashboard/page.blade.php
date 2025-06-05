<section class="w-full" x-data="dashboardCharts()" x-init="initializeAllCharts()">
    <x-contents.heading title="Dashboard" />

    <x-contents.layout>
        <div class="p-4 sm:p-6 lg:p-8 space-y-6">

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Customers Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Customers</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalCustomers) }}</p>
                        </div>
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Orders Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Orders</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($totalOrders) }}</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pending Orders Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending Orders</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($pendingOrders) }}</p>
                        </div>
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue Card -->
                <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Monthly Revenue</p>
                            <p class="text-3xl font-bold text-gray-900">₱{{ number_format($monthlyRevenue, 2) }}</p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row 1 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Monthly Orders Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Orders</h3>
                    <div id="monthlyOrdersChart"></div>
                </div>

                <!-- Order Status Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Status Distribution</h3>
                    <div id="orderStatusChart"></div>
                </div>
            </div>

            <!-- Charts Row 2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Revenue Trend Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trend (Last 30 Days)</h3>
                    <div id="revenueChart"></div>
                </div>

                <!-- Top Services Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Services</h3>
                    <div id="topServicesChart"></div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Orders</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order #</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                            $recentOrders = \App\Models\Order::with('customer')
                            ->latest()
                            ->limit(5)
                            ->get();
                            @endphp
                            @foreach($recentOrders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $order->order_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $order->customer->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($order->status->value === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status->value === 'completed') bg-green-100 text-green-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ ucfirst($order->status->value) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </x-contents.layout>
</section>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    function dashboardCharts() {
    return {
        charts: {},
        
        initializeAllCharts() {
            this.$nextTick(() => {
                this.initMonthlyOrdersChart();
                this.initOrderStatusChart();
                this.initRevenueChart();
                this.initTopServicesChart();
                console.log('All charts initialized via Alpine.js');
            });
        },

        destroyChart(chartKey) {
            if (this.charts[chartKey]) {
                this.charts[chartKey].destroy();
                delete this.charts[chartKey];
            }
        },

        initMonthlyOrdersChart() {
            this.destroyChart('monthlyOrders');
            
            const monthlyOrdersOptions = {
                series: [{
                    name: 'Orders',
                    data: @json($monthlyOrdersData['data'])
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: '55%',
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: @json($monthlyOrdersData['categories']),
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Number of Orders'
                    }
                },
                fill: {
                    opacity: 1,
                    colors: ['#3B82F6']
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " orders"
                        }
                    }
                },
                grid: {
                    borderColor: '#f3f4f6',
                    strokeDashArray: 3
                }
            };

            if (document.querySelector("#monthlyOrdersChart")) {
                this.charts.monthlyOrders = new ApexCharts(document.querySelector("#monthlyOrdersChart"), monthlyOrdersOptions);
                this.charts.monthlyOrders.render();
            }
        },

        initOrderStatusChart() {
            this.destroyChart('orderStatus');
            
            const orderStatusOptions = {
                series: @json($orderStatusData['data']),
                chart: {
                    type: 'pie',
                    height: 350
                },
                labels: @json($orderStatusData['labels']),
                colors: ['#F59E0B', '#10B981', '#EF4444', '#8B5CF6', '#06B6D4'],
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " orders"
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            if (document.querySelector("#orderStatusChart")) {
                this.charts.orderStatus = new ApexCharts(document.querySelector("#orderStatusChart"), orderStatusOptions);
                this.charts.orderStatus.render();
            }
        },

        initRevenueChart() {
            this.destroyChart('revenue');
            
            const revenueOptions = {
                series: [{
                    name: 'Revenue',
                    data: @json($revenueData['data'])
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    colors: ['#10B981']
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 90, 100]
                    },
                    colors: ['#10B981']
                },
                xaxis: {
                    categories: @json($revenueData['categories']),
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Amount (₱)'
                    },
                    labels: {
                        formatter: function (val) {
                            return '₱' + val.toLocaleString()
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return '₱' + val.toLocaleString()
                        }
                    }
                },
                grid: {
                    borderColor: '#f3f4f6',
                    strokeDashArray: 3
                }
            };

            if (document.querySelector("#revenueChart")) {
                this.charts.revenue = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
                this.charts.revenue.render();
            }
        },

        initTopServicesChart() {
            this.destroyChart('topServices');
            
            const topServicesOptions = {
                series: [{
                    name: 'Orders',
                    data: @json($topServicesData['data'])
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: @json($topServicesData['labels']),
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                fill: {
                    colors: ['#8B5CF6']
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " orders"
                        }
                    }
                },
                grid: {
                    borderColor: '#f3f4f6',
                    strokeDashArray: 3
                }
            };

            if (document.querySelector("#topServicesChart")) {
                this.charts.topServices = new ApexCharts(document.querySelector("#topServicesChart"), topServicesOptions);
                this.charts.topServices.render();
            }
        }
    }
}
</script>
@endpush