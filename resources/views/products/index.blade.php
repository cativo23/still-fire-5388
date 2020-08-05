@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>Products</h2>
                        <a class="btn btn-success" href="{{ route('products.create') }}"> Create New Product</a>
                    </div>
                    <div class="card-body">

                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                <p>{{ $message }}</p>
                            </div>
                        @endif
                        <form action="{{route('products.index')}}" method="get">
                            <div class="row">
                                <div class="col-8"><input class="form-control form-control-dark w-100" type="search"
                                                          placeholder="Search" value="{{$param}}" name="term"
                                                          aria-label="Search"></div>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>

                            </div>
                        </form>
                        <br>
                        <div class="row">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>No</th>
                                        <th>SKU</th>
                                        <th>Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th width="280px">Action</th>
                                    </tr>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ $product->sku }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->quantity }}</td>
                                            <td>$ {{ $product->price }}</td>
                                            <td>
                                                <form action="{{ route('products.destroy',$product->id) }}"
                                                      method="POST">

                                                    <a class="btn btn-info"
                                                       href="{{ route('products.show',$product->id) }}">Show</a>

                                                    <a class="btn btn-primary"
                                                       href="{{ route('products.edit',$product->id) }}">Edit</a>

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        {!! $products->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
