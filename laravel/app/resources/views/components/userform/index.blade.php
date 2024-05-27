<div class="card">
    <div class="card-body">
        <h4 class="m-0">
            {{__('Вход')}}
        </h4>
    </div>
    <div class="card-body">
        <form action="{{route('store_form')}}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="required">{{__('E-mail')}}</label>
                <input type="email" name="email" class="form-control" autofocus>
            </div>
            <div class="mb-3">
                <label class="required">{{__('Password')}}</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input type="checkbox" value="1" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">{{__('Запомнить меня')}}</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                {{__('Войти')}}
            </button>
        </form>
    </div>
</div>
