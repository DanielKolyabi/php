<div class="card">
    <div class="card-body">
        <form action="{{route('book_store')}}" method="POST">
            @csrf

            <div class="form-section mb-3">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>

            <div class="form-section mb-3">
                <label for="author">Author</label>
                <input type="text" id="author" name="author" class="form-control" required>
            </div>

            <div class="form-section mb-3">
                <label for="genre">Choose Genre:</label>
                <select class="form-select" name="genre" id="genre" required>
                    <option value="fantasy">Fantasy</option>
                    <option value="sci-fi">Sci-Fi</option>
                    <option value="mystery">Mystery</option>
                    <option value="drama">Drama</option>
                </select>
            </div>

            <div class="form-section d-flex justify-content-end">
                <button type="submit" class="btn btn-primary col-12 col-sm-4">Submit</button>
            </div>
        </form>
    </div>
</div>
