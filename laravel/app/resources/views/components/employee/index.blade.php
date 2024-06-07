<div class="card">
    <div class="card-body">
        <form action="{{route('employee_store')}}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="required">{{__('ФИО работника')}}</label>
                <input type="text" name="name" class="form-control" autofocus required>
            </div>
            <div class="mb-3">
                <label class="required">{{__('Должность')}}</label>
                <input type="text" name="position" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="required">{{__('Адрес')}}</label>
                <input type="text" name="address" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="required">{{__('Email')}}</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="required">{{__('workData')}}</label>
                <textarea name="workData" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                {{__('Submit')}}
            </button>
        </form>
    </div>
</div>
