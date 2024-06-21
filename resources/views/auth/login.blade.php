<head>

    @include('layouts.header')
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center mx-auto kolom-login">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Sistem Informasi Eksekutif Bidan Dashboard
                                        </h1>
                                    </div>
                                    <form action="{{ route('login.auth') }}" method="post" class="user">
                                        @csrf
                                        @if (session()->has('error'))
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                {{ session('error') }}
                                                <button type="button" class="close" data-dismiss="alert"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                        @endif

                                        <div class="form-group">
                                            <input type="text" name="username" class="form-control form-control-user"
                                                id="name" aria-describedby="emailHelp" placeholder="Username..."
                                                autofocus required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password"
                                                class="form-control form-control-user" id="password"
                                                placeholder="Password" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary btn-user btn-block mt-5">
                                            Login
                                        </button>
                                        <hr>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

        @include('layouts.footer')

</body>

</html>
