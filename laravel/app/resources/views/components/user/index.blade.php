<div class="card">
    <div class="card-body">
        <form action="{{url('store-user')}}" method="POST">
            @csrf

            <div class="form-section mb-3">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-section mb-3">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-section mb-3">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="form-section mb-3">
                <label for="surname">Surname</label>
                <input type="text" id="surname" name="surname" class="form-control" required>
            </div>

            <div class="form-section d-flex justify-content-end">
                <button type="submit" class="btn btn-primary col-12 col-sm-4">Submit</button>
            </div>
        </form>
    </div>
</div>
