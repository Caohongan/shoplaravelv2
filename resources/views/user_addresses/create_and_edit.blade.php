@extends('layouts.app')
@section('title', ($address->id ? 'Sửa': 'Thêm') . ' địa chỉ nhận hàng')

@section('content')
<div class="row">
<div class="col-md-10 offset-lg-1">
<div class="card">
  <div class="card-header">
    <h2 class="text-center">
      {{ $address->id ? 'Sửa': 'Thêm' }} địa chỉ nhận hàng
    </h2>
  </div>
  <div class="card-body">
    <!-- 输出后端报错开始 -->
    @if (count($errors) > 0)
      <div class="alert alert-danger">
        <h4>An error occurred：</h4>
        <ul>
          @foreach ($errors->all() as $error)
            <li><i class="glyphicon glyphicon-remove"></i> {{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    <!-- 输出后端报错结束 -->
    <!-- inline-template 代表通过内联方式引入组件 -->
    <user-addresses-create-and-edit inline-template>
      @if($address->id)
        <form class="form-horizontal" role="form" action="{{ route('user_addresses.update', ['user_address' => $address->id]) }}" method="post">
          {{ method_field('PUT') }}
      @else
        <form class="form-horizontal" role="form" action="{{ route('user_addresses.store') }}" method="post">
      @endif
      {{ csrf_field() }}
      <!-- 注意这里多了 @change -->
        <select-district :init-value="{{ json_encode([$address->province, $address->city, $address->district]) }}" @change="onDistrictChanged" inline-template>
          <div class="form-group row">
            <label class="col-form-label col-sm-2 text-md-right">Vui lòng chọn</label>
            <div class="col-sm-3">
              <select  class="form-control" v-model="provinceId">
                <option value="">Thành Phố/Tỉnh</option>
                <option v-for="(name, id) in provinces" :value="id">@{{ name }}</option>
              </select>
            </div>
            <div class="col-sm-3">
              <select class="form-control" v-model="cityId">
                <option value="">Quận/Huyện</option>
                <option v-for="(name, id) in cities" :value="id">@{{ name }}</option>
              </select>
            </div>
            <div class="col-sm-3">
              <select class="form-control" v-model="districtId">
                <option value="">Khu Vực</option>
                <option v-for="(name, id) in districts" :value="id">@{{ name }}</option>
              </select>
            </div>
          </div>
        </select-district>
        <!-- 插入了 3 个隐藏的字段 -->
        <!-- 通过 v-model 与 user-addresses-create-and-edit 组件里的值关联起来 -->
        <!-- 当组件中的值变化时，这里的值也会跟着变 -->
        <input type="hidden" name="province" v-model="province">
        <input type="hidden" name="city" v-model="city">
        <input type="hidden" name="district" v-model="district">
        <div class="form-group row">
          <label class="col-form-label text-md-right col-sm-2">Địa chỉ cụ thể</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="address" value="{{ old('address', $address->address) }}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-form-label text-md-right col-sm-2">Mã bưu điện</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="zip" value="{{ old('zip', $address->zip) }}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-form-label text-md-right col-sm-2">Họ và Tên</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="contact_name" value="{{ old('contact_name', $address->contact_name) }}">
          </div>
        </div>
        <div class="form-group row">
          <label class="col-form-label text-md-right col-sm-2">Số điện thoại</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="contact_phone" value="{{ old('contact_phone', $address->contact_phone) }}">
          </div>
        </div>
        <div class="form-group row text-center">
          <div class="col-12">
            <button type="submit" class="btn btn-{{ $address->id ? 'warning': 'primary' }}"> {{ $address->id ? 'Sửa': 'Thêm' }} địa chỉ nhận hàng</button>
          </div>
        </div>
      </form>
    </user-addresses-create-and-edit>
  </div>
</div>
</div>
</div>
@endsection
