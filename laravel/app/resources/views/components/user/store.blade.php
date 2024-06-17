@if(isset($error))
    <div class="alert alert-danger">Error: {{$error}}</div>
@elseif(isset($users))
    <div class="card">
        <div class="container pt-2 pb-2">
            <div class="row">
                <div class="col-12 pb-2 d-flex justify-content-end">
                    <a href="/user" class="btn btn-primary col-12 col-sm-4">New user</a>
                </div>
            </div>

            <div class="row">
                <div class="col-4">ID</div>
                <div class="col-4">Email</div>
                <div class="col-4">Created at</div>
            </div>
            @foreach($users as $user)
                <div class="row">
                    <div class="col-4">{{$user['id']}}</div>
                    <div class="col-4"><a href="/user/{{$user['id']}}">{{$user['email']}}</a></div>
                    <div class="col-4">{{date('H:i d.m.Y', strtotime($user['created_at']))}}</div>
                </div>
            @endforeach
        </div>
        </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-4">ID</div>
                <div class="col-8">{{$id}}</div>
            </div>
            <div class="row">
                <div class="col-4">Email</div>
                <div class="col-8">{{$email}}</div>
            </div>
            <div class="row">
                <div class="col-4">Name</div>
                <div class="col-8">{{$name}}</div>
            </div>
            <div class="row">
                <div class="col-4">Surname</div>
                <div class="col-8">{{$surname}}</div>
            </div>
            <div class="row">
                <div class="col-4">Created at</div>
                <div class="col-8">{{date('H:i d.m.Y', strtotime($created_at))}}</div>
            </div>
            <div class="row">
                <div class="col-4">Updated at</div>
                <div class="col-8">{{date('H:i d.m.Y', strtotime($updated_at))}}</div>
            </div>

            <div class="row">
                <div class="col-12 d-flex justify-content-end">
                    <a href="/resume/{{$id}}" target="_blank" class="btn btn-primary col-12 col-sm-4">get *.pdf</a>
                </div>
            </div>
        </div>
    </div>
@endif
