<nav class="navbar navbar-expand-sm navbar-light bg-light">
    <a class="navbar-brand" href="#">{{config('app.name')}}</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav collapse navbar-collapse" id="navbarNav">
        <a class="nav-item nav-link {{active_link('/')}}" href="{{route('home')}}">
            {{__('Главная')}}
        </a>
        <!--
        <a class="nav-item nav-link {{active_link('/userform')}}" href="{{route('userform')}}">
            {{__('UserForm')}}
        </a>
        <a class="nav-item nav-link {{active_link('/contacts')}}" href="{{route('contacts')}}">
            {{__('Contacts')}}
        </a>
        <a class="nav-item nav-link {{active_link('/book')}}" href="{{route('book')}}">
            {{__('Add Book')}}
        </a>
        <a class="nav-item nav-link {{active_link('/user/all')}}" href="{{url('/user/all')}}">
            {{__('Users')}}
        </a>
        <a class="nav-item nav-link {{active_link('/logs')}}" href="{{url('/logs')}}">
            {{__('Logs')}}
        </a>
        -->
        <a class="nav-item nav-link {{active_link('/users')}}" href="{{url('/users')}}">
            {{__('Users')}}
        </a>
        @guest
            <a class="nav-item nav-link {{active_link('/login')}}" href="{{url('/login')}}">
                {{__('Login')}}
            </a>
            <a class="nav-item nav-link {{active_link('/register')}}" href="{{url('/register')}}">
                {{__('Register')}}
            </a>
        @endguest
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                                       onclick="event.preventDefault();
                                        this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        @endauth
    </div>
</nav>
