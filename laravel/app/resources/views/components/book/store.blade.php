@if(isset($error))
    <div class="alert alert-danger">Error: {{$error}}</div>
@else
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-4">Title</div>
                <div class="col-8">{{$title}}</div>
            </div>
            <div class="row">
                <div class="col-4">Author</div>
                <div class="col-8">{{$author}}</div>
            </div>
            <div class="row">
                <div class="col-4">Genre</div>
                <div class="col-8">{{$genre}}</div>
            </div>
            <div class="row">
                <div class="col-4">Created at</div>
                <div class="col-8">{{date('H:i d.m.Y', strtotime($created_at))}}</div>
            </div>
            <div class="row">
                <div class="col-4">Updated at</div>
                <div class="col-8">{{date('H:i d.m.Y', strtotime($updated_at))}}</div>
            </div>
        </div>
    </div>
@endif
