@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h2>Edit Product {{$product->name}}</h2>
                        <a class="btn btn-primary" href="{{ route('products.index') }}"> Back</a>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-3">
                                    <div class="form-group">
                                        <label for="sku"><strong>SKU:</strong></label>
                                        <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku', isset($product) ? $product->sku : '') }}" placeholder="Product SKU">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-3">
                                    <div class="form-group">
                                        <label for="name"><strong>Name:</strong></label>
                                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', isset($product) ? $product->name : '') }}" placeholder="Product Name">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-3">
                                    <div class="form-group">
                                        <label for="quantity"><strong>Quantity:</strong></label>
                                        <input type="number" min="0.00" max="1000000.00" step="1" name="quantity" id="quantity" class="form-control" value="{{ old('quantity', isset($product) ? $product->quantity : '') }}" placeholder="Product Quantity">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-3">
                                    <div class="form-group">
                                        <label for="price"><strong>Price:</strong></label>
                                        <input type="number" min="0.00" max="1000000.00" step="0.01" name="price" id="price" class="form-control" value="{{ old('price', isset($product) ? $product->price : '') }}" placeholder="Product Price">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="description"><strong>Description:</strong></label>
                                        <textarea class="form-control" id="description" style="height:150px" name="description" placeholder="Product description...">{{ old('description', isset($product) ? $product->description : '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <label for="image">Choose Image</label>
                                        <input type="file" name="image" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
