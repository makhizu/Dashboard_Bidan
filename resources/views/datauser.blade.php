@extends('layouts.main')
@section('content')
    <!-- Page Heading -->



    {{-- Pesan Sukses --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session('success') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    {{-- Pesan Session Error --}}
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('error') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Pesan Error --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4 d-none">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Area Chart</h6>
        </div>
        <div class="card-body">
            <div class="chart-area">
                <div id=""></div>
            </div>

        </div>
    </div>

    <!-- Modal Dialog Import -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tambah Akun</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('datauser.tambah') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Username</label>
                            <input type="text" name="username" class="form-control" id="exampleFormControlInput1"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlSelect1">User Level</label>
                            <select class="form-control" name="level" id="exampleFormControlSelect1">
                                <option value="1">Pimpinan</option>
                                <option value="2">Bidan</option>
                                {{-- <option value="3">Admin</option> --}}
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Password</label>
                            <input type="password" name="pass" class="form-control" id="exampleFormControlInput1"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal Dialog Import -->
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        {{-- <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Data Ibu</h6>
        </div> --}}


        <div class="card-body">
            <div class="table-responsive">
                <div class="mb-3 d-flex justify-content-end">

                    <button type="button" class="btn btn-primary " data-toggle="modal" data-target="#exampleModal">
                        <i class="fas fa-plus-circle"></i> Tambah Akun
                    </button>
                </div>
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Level</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user as $dataUser)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $dataUser->username }}</td>
                                <td>
                                    @if ($dataUser->level == 1)
                                        1 - Pimpinan
                                    @else
                                        2 - Bidan
                                    @endif
                                </td>
                                <td class="d-flex justify-content-center"><button type="button" data-toggle="modal"
                                        data-target="#EditData{{ $dataUser->id }}" class="btn btn-warning border-0"><i
                                            class="far fa-edit"></i></button>
                                    <form action="{{ route('datauser.delete', ['id' => $dataUser->id]) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('delete')
                                        <button class="btn btn-danger border-0 ml-2"
                                            onclick="return confirm('Yakin Hapus Data ?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Modal Edit data -->
                @foreach ($user as $dataUser)
                    <div class="modal fade" id="EditData{{ $dataUser->id }}" data-backdrop="static" data-keyboard="false"
                        tabindex="-1" aria-labelledby="EditDataLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="EditDataLabel">Edit Data User</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('datauser.update', ['id' => $dataUser->id]) }}" method="POST">
                                        @csrf
                                        @method('put')

                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Username</label>
                                            <input type="text" name="username" class="form-control"
                                                id="exampleFormControlInput1" value={{ $dataUser->username }} readonly>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleFormControlSelect1">User Level</label>
                                            <select class="form-control" name="level" id="exampleFormControlSelect1">
                                                <option value="1" {{ $dataUser->level == 1 ? 'selected' : '' }}>
                                                    Pimpinan</option>
                                                <option value="2" {{ $dataUser->level == 2 ? 'selected' : '' }}>Bidan
                                                </option>
                                                {{-- <option value="3">Admin</option> --}}
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Password Lama</label>
                                            <input type="password" name="OldPass" class="form-control"
                                                id="exampleFormControlInput1" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleFormControlInput1">Password Baru</label>
                                            <input type="password" name="NewPass" class="form-control"
                                                id="exampleFormControlInput1" required>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Edit</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
                {{-- End Modal Edit data --}}
            </div>
        </div>
    </div>
@endsection
