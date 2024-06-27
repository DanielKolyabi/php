@if(isset($users))
    <div class="card">
        <div class="container pt-2 pb-2">
            <div class="row">
                <div class="col-2">ID</div>
                <div class="col-4">Email</div>
                <div class="col-1">Is admin</div>
                <div class="col-3">Created at</div>
            </div>
            @foreach($users as $user)
                <div class="row">
                    <div class="col-2">{{$user['id']}}</div>
                    <div class="col-4">{{$user['email']}}</div>
                    <div class="col-1">{{$user['is_admin'] ? 'Yes' : ''}}</div>
                    <div class="col-3">{{date('H:i d.m.Y', strtotime($user['created_at']))}}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif
