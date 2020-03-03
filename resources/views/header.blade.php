<header>
    <nav class="w-full py-4 px-4 bg-white shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <div class="">
              <a class="logo" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
              </a>
            </div>
            <div class="">
                <!-- Right Side Of Navbar -->
                <ul>
                    @guest
                        <li class="inline-block mx-3">
                            <a class="text-blue-400" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        <li class="inline-block mx-3">
                            <a class="text-blue-400" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @else
                        <li class="inline-block mx-3">
                            <a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>
                        </li>
                        <li>
                          <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                              @csrf
                          </form>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
  </header>