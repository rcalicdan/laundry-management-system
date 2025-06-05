function initializeMonthlyOrdersChart(data) {
    const options = {
        series: [{
            name: 'Orders',
            data: data.data
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
            categories: data.categories,
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

    const chart = new ApexCharts(document.querySelector("#monthlyOrdersChart"), options);
    chart.render();
}

// Order Status Pie Chart
function initializeOrderStatusChart(data) {
    const options = {
        series: data.data,
        chart: {
            type: 'pie',
            height: 350
        },
        labels: data.labels,
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

    const chart = new ApexCharts(document.querySelector("#orderStatusChart"), options);
    chart.render();
}

// Revenue Trend Chart
function initializeRevenueChart(data) {
    const options = {
        series: [{
            name: 'Revenue',
            data: data.data
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
            categories: data.categories,
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

    const chart = new ApexCharts(document.querySelector("#revenueChart"), options);
    chart.render();
}

// Top Services Chart
function initializeTopServicesChart(data) {
    const options = {
        series: [{
            name: 'Orders',
            data: data.data
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
            categories: data.labels,
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

    const chart = new ApexCharts(document.querySelector("#topServicesChart"), options);
    chart.render();
}