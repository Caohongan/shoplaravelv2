<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">Số thứ tự:{{ $order->no }}</h3>
    <div class="box-tools">
      <div class="btn-group float-right" style="margin-right: 10px">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary"><i class="fa fa-list"></i> List</a>
      </div>
    </div>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tbody>
      <tr>
        <td>Người mua:</td>
        <td>{{ $order->user->name }}</td>
        <td>Thời gian thanh toán:</td>
        <td>{{ $order->paid_at->format('Y-m-d H:i:s') }}</td>
      </tr>
      <tr>
        <td>Phương thức thanh toán:</td>
        <td>{{ $order->payment_method }}</td>
        <td>Số kênh thanh toán:</td>
        <td>{{ $order->payment_no }}</td>
      </tr>
      <tr>
        <td>Địa chỉ giao hàng</td>
        <td colspan="3">{{ $order->address['address'] }} {{ $order->address['zip'] }} {{ $order->address['contact_name'] }} {{ $order->address['contact_phone'] }}</td>
      </tr>
      <tr>
        <td rowspan="{{ $order->items->count() + 1 }}">Danh sách sản phẩm</td>
        <td>Tên sản phẩm</td>
        <td>Đơn giá</td>
        <td>Số lượng</td>
      </tr>
      @foreach($order->items as $item)
        <tr>
          <td>{{ $item->product->title }} {{ $item->productSku->title }}</td>
          <td>￥{{ $item->price }}</td>
          <td>{{ $item->amount }}</td>
        </tr>
      @endforeach
      <tr>
        <td>Số lượng đặt hàng:</td>
        <td>${{ $order->total_amount }}</td>
        <td>Tình trạng giao hàng:</td>
        <td>{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</td>
      </tr>
      <!-- 订单发货开始 -->
      <!-- 如果订单未发货，展示发货表单 -->
      @if($order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING)
        <!-- 加上这个判断条件 -->
        @if($order->refund_status !== \App\Models\Order::REFUND_STATUS_SUCCESS)
        <tr>
          <td colspan="4">
            <form action="{{ route('admin.orders.ship', [$order->id]) }}" method="post" class="form-inline">
              <!-- 别忘了 csrf token 字段 -->
              {{ csrf_field() }}
              <div class="form-group {{ $errors->has('express_company') ? 'has-error' : '' }}">
                <label for="express_company" class="control-label">Công ty hậu cần</label>
                <input type="text" id="express_company" name="express_company" value="" class="form-control" placeholder="Công ty hậu cần">
                @if($errors->has('express_company'))
                  @foreach($errors->get('express_company') as $msg)
                    <span class="help-block">{{ $msg }}</span>
                  @endforeach
                @endif
              </div>
              <div class="form-group {{ $errors->has('express_no') ? 'has-error' : '' }}">
                <label for="express_no" class="control-label">Số hậu cần</label>
                <input type="text" id="express_no" name="express_no" value="" class="form-control" placeholder="Nhập số ghi chú hậu cần">
                @if($errors->has('express_no'))
                  @foreach($errors->get('express_no') as $msg)
                    <span class="help-block">{{ $msg }}</span>
                  @endforeach
                @endif
              </div>
              <button type="submit" class="btn btn-success" id="ship-btn">Vận chuyển</button>
            </form>
          </td>
        </tr>
        <!-- 在 上一个 if 的 else 前放上 endif -->
        @endif
      @else
        <!-- 否则展示物流公司和物流单号 -->
        <tr>
          <td>Công ty hậu cần:</td>
          <td>{{ $order->ship_data['express_company'] }}</td>
          <td>Số hậu cần:</td>
          <td>{{ $order->ship_data['express_no'] }}</td>
        </tr>
      @endif
      <!-- 订单发货结束 -->
      @if($order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
        <tr>
          <td>Tình trạng hoàn tiền:</td>
          <td colspan="2">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}，Lý do：{{ $order->extra['refund_reason'] }}</td>
          <td>
            <!-- 如果订单退款状态是已申请，则展示处理按钮 -->
            @if($order->refund_status === \App\Models\Order::REFUND_STATUS_APPLIED)
              <button class="btn btn-sm btn-success" id="btn-refund-agree">Agree</button>
              <button class="btn btn-sm btn-danger" id="btn-refund-disagree">Disagree</button>
            @endif
          </td>
        </tr>
      @endif
      </tbody>
    </table>
  </div>
</div>

<script>
  $(document).ready(function() {
    // 不同意 按钮的点击事件
    $('#btn-refund-disagree').click(function() {
      // Laravel-Admin 使用的 SweetAlert 版本与我们在前台使用的版本不一样，因此参数也不太一样
      swal({
        title: 'Nhập lý do từ chối hoàn trả',
        input: 'text',
        showCancelButton: true,
        confirmButtonText: "Xác nhận",
        cancelButtonText: "Hủy bỏ",
        showLoaderOnConfirm: true,
        preConfirm: function(inputValue) {
          if (!inputValue) {
            swal('Lý do không thể để trống', '', 'error')
            return false;
          }
          // Laravel-Admin 没有 axios，使用 jQuery 的 ajax 方法来请求
          return $.ajax({
            url: '{{ route('admin.orders.handle_refund', [$order->id]) }}',
            type: 'POST',
            data: JSON.stringify({   // 将请求变成 JSON 字符串
              agree: false,  // 拒绝申请
              reason: inputValue,
              // 带上 CSRF Token
              // Laravel-Admin 页面里可以通过 LA.token 获得 CSRF Token
              _token: LA.token,
            }),
            contentType: 'application/json',  // 请求的数据格式为 JSON
          });
        },
        allowOutsideClick: () => !swal.isLoading()
      }).then(function (ret) {
        // 如果用户点击了『取消』按钮，则不做任何操作
        if (ret.dismiss === 'cancel') {
          return;
        }
        swal({
          title: 'Hoạt động thành công',
          type: 'success'
        }).then(function() {
          // 用户点击 swal 上的按钮时刷新页面
          location.reload();
        });
      });
    });

    // 同意 按钮的点击事件
    $('#btn-refund-agree').click(function() {
      swal({
        title: 'Bạn có chắc chắn muốn trả lại tiền cho người dùng?',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: "Xác nhận",
        cancelButtonText: "Hủy bỏ",
        showLoaderOnConfirm: true,
        preConfirm: function() {
          return $.ajax({
            url: '{{ route('admin.orders.handle_refund', [$order->id]) }}',
            type: 'POST',
            data: JSON.stringify({
              agree: true, // 代表同意退款
              _token: LA.token,
            }),
            contentType: 'application/json',
          });
        }
      }).then(function (ret) {
        // 如果用户点击了『取消』按钮，则不做任何操作
        if (ret.dismiss === 'cancel') {
          return;
        }
        swal({
          title: 'Hoạt động thành công',
          type: 'success'
        }).then(function() {
          // 用户点击 swal 上的按钮时刷新页面
          location.reload();
        });
      });
    });

  });
</script>
