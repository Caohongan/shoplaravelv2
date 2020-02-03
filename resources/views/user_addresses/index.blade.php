@extends('layouts.app')
@section('title', 'Danh sách địa chỉ nhận hàng')

@section('content')
  <div class="row">
    <div class="col-md-12 offset-md">
      <div class="card panel-default">
        <div class="card-header">
         Danh sách địa chỉ nhận hàng
          <a href="{{ route('user_addresses.create') }}" class="btn btn-success float-right">Thêm địa chỉ nhận hàng</a>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Người nhận</th>
              <th>Địa chỉ</th>
              <th>Mã bưu điện</th>
              <th>Số điện thoại</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($addresses as $address)
              <tr>
                <td>{{ $address->contact_name }}</td>
                <td>{{ $address->full_address }}</td>
                <td>{{ $address->zip }}</td>
                <td>{{ $address->contact_phone }}</td>
                <td>
                  <a href="{{ route('user_addresses.edit', ['user_address' => $address->id]) }}" class="btn btn-primary">Sửa</a>
                  <!-- 把之前删除按钮的表单替换成这个按钮，data-id 属性保存了这个地址的 id，在 js 里会用到 -->
                  <button class="btn btn-danger btn-del-address" type="button" data-id="{{ $address->id }}">Xóa</button>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scriptsAfterJs')
  <script>
    $(document).ready(function() {
      // 删除按钮点击事件
      $('.btn-del-address').click(function() {
        // 获取按钮上 data-id 属性的值，也就是地址 ID
        var id = $(this).data('id');
        // 调用 sweetalert
        swal({
          title: "Bạn có chắc muốn xóa địa chị này không?",
          icon: "warning",
          buttons: ['Không', 'Có'],
          dangerMode: true,
        })
          .then(function(willDelete) { // 用户点击按钮后会触发这个回调函数
            // 用户点击确定 willDelete 值为 true， 否则为 false
            // 用户点了取消，啥也不做
            if (!willDelete) {
              return;
            }
            // 调用删除接口，用 id 来拼接出请求的 url
            axios.delete('/user_addresses/' + id)
              .then(function () {
                // 请求成功之后重新加载页面
                location.reload();
              })
          });
      });
    });
  </script>
@endsection
