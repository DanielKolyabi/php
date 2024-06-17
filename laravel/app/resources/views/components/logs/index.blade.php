@if(isset($error))
    <div class="alert alert-danger">Error: {{$error}}</div>
@elseif(isset($logs))
    <div class="card">
        <div class="container pt-2 pb-2">
            <div class="row">
                <div class="col-2 col-md-1 text-center">Method</div>
                <div class="col-7 col-md-3 text-center">Url</div>
                <div class="d-none d-md-block col-4 text-center">Input</div>
                <div class="col-3 col-md-2 text-center">IP</div>
                <div class="d-none d-md-block col-1 text-center">Time</div>
                <div class="d-none d-md-block col-1 text-center">Duration</div>
            </div>
            @foreach($logs as $log)
                <div class="row">
                        <?php
                        $class = '';
                        switch (strtoupper($log['method'])) {
                            case 'GET':
                                $class = 'text-success';
                                break;
                            case 'POST':
                                $class = 'text-primary';
                                break;
                            case 'PUT':
                            case 'PATCH':
                                $class = 'text-warning';
                                break;
                            case 'DELETE':
                                $class = 'text-danger';
                                break;
                        }
                        ?>
                    <div class="col-2 col-md-1 font-weight-bold text-center {{$class}}">{{$log['method']}}</div>
                    <div class="col-7 col-md-3">{{$log['url']}}</div>
                    <div class="d-none d-md-block col-4">{{$log['input']}}</div>
                    <div class="col-3 col-md-2">{{$log['ip']}}</div>
                    <div class="d-none d-md-block col-1 text-center" title="{{$log['time']}}">
                        {{date('H:i:s', strtotime($log['time']))}}
                    </div>
                    <div class="d-none d-md-block col-1 text-center">{{$log['duration']}}</div>
                </div>
            @endforeach

            <div class="row">
                <div class="col-6 pt-2 d-flex justify-content-start">
                    @if($page - 1 > 0)
                        <a href="/logs/{{$page - 1}}" class="btn btn-primary col-12 col-sm-8">Prev</a>
                    @endif
                </div>
                <div class="col-6 pt-2 d-flex justify-content-end">
                    @if(count($logs) >= 50)
                        <a href="/logs/{{$page + 1}}" class="btn btn-primary col-12 col-sm-8">Next</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-danger">Error: request error</div>
@endif
