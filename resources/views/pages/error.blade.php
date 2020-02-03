@extends('layouts.app')
@section('title', 'Lỗi Hệ Thống')

@section('content')
  <div class="card">
    <div class="card-header">Lỗi đường dẫn</div>
    <div class="card-body text-center">
      <h1>{{ $msg }}</h1>
      <a class="btn btn-primary" href="{{ route('root') }}">Vui lòng quay lại</a>
    </div>
  </div>
@endsection
