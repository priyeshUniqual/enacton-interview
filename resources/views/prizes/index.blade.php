@extends('default')

@section('content')


    @include('prob-notice')


    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('prizes.create') }}" class="btn btn-info">Create</a>
                </div>
                <h1>Prizes</h1>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Probability</th>
                            <th>Awarded</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prizes as $prize)
                            <tr>
                                <td>{{ $prize->id }}</td>
                                <td>{{ $prize->title }}</td>
                                <td>{{ $prize->probability }}</td>
                                <td>{{ $prize->awarded }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('prizes.edit', [$prize->id]) }}" class="btn btn-primary">Edit</a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['prizes.destroy', $prize->id]]) !!}
                                        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                        {!! Form::close() !!}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Simulate</h3>
                    </div>
                    <div class="card-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['simulate']]) !!}
                        <div class="form-group">
                            {!! Form::label('number_of_prizes', 'Number of Prizes') !!}
                            {!! Form::number('number_of_prizes', 50, ['class' => 'form-control']) !!}
                        </div>
                        {!! Form::submit('Simulate', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>

                    <br>

                    <div class="card-body">
                        {!! Form::open(['method' => 'POST', 'route' => ['reset']]) !!}
                        {!! Form::submit('Reset', ['class' => 'btn btn-primary']) !!}
                        {!! Form::close() !!}
                    </div>

                </div>
            </div>
        </div>
    </div>



    <div class="container  mb-4">
        <div class="row">
            <div class="col-md-6">
                <h2>Probability Settings</h2>
                <canvas id="probabilityChart"></canvas>
            </div>
            <div class="col-md-6">
                <h2>Actual Rewards</h2>
                <canvas id="awardedChart"></canvas>
            </div>
        </div>
    </div>


@stop


@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script>
        var doughnutChartOptions = {
            responsive: true,
            plugins: {
                datalabels: {
                    color: 'white',
                    formatter: function(value, context) {
                        console.log(context.chart.data.datasets[0].data);
                        return context.chart.data.labels[
                            context.dataIndex
                        ] + ' (' + context.chart.data.datasets[0].data[context.dataIndex] + '%)';
                    },
                },
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    display: false,
                    ticks: {
                        display: false,
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    display: false,
                    ticks: {
                        display: false,
                    },
                    grid: {
                        display: false
                    }

                }
            },
            elements: {
                point: {
                    radius: 0
                }
            },
        }
        var ctx = document.getElementById('probabilityChart').getContext('2d');
        var myChart = new Chart(ctx, {
            plugins: [ChartDataLabels],
            type: 'doughnut',
            data: {
                labels: @json($probabilityData['labels']),
                datasets: [{
                    label: 'probability',
                    data: @json($probabilityData['data']),
                    backgroundColor: [
                        '#FF0000',
                        '#0000FF',
                        '#800080',
                        '#FFFF00',
                        '#008000',
                        '#FFA500',
                        '#000000',
                        '#800000',
                        '#00008B',
                    ],
                    borderWidth: 1
                }]
            },
            options: doughnutChartOptions
        });

        var ctx = document.getElementById('awardedChart').getContext('2d');
        var myChart = new Chart(ctx, {
            plugins: [ChartDataLabels],
            type: 'doughnut',
            data: {
                labels: @json($actualRewards['labels']),
                datasets: [{
                    label: 'probability',
                    data: @json($actualRewards['data']),
                    backgroundColor: [
                        '#FF0000',
                        '#0000FF',
                        '#800080',
                        '#FFFF00',
                        '#008000',
                        '#FFA500',
                        '#000000',
                        '#800000',
                        '#00008B',
                    ],
                    borderWidth: 1
                }]
            },
            options: doughnutChartOptions
        });
    </script>
@endpush
