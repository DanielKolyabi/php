@if(isset($error))
    <div class="alert alert-danger">Error: {{$error}}</div>
@else
    <div class="card">
        <table>
            <tr>
                <td>ID:</td>
                <td>{{$id}}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{$email}}</td>
            </tr>
            <tr>
                <td>Name:</td>
                <td>{{$name}}</td>
            </tr>
            <tr>
                <td>Surname:</td>
                <td>{{$surname}}</td>
            </tr>
            <tr>
                <td>Created at:</td>
                <td>{{date('H:i d.m.Y', strtotime($created_at))}}</td>
            </tr>
            <tr>
                <td>Updated at:</td>
                <td>{{date('H:i d.m.Y', strtotime($updated_at))}}</td>
            </tr>
        </table>
    </div>
@endif
