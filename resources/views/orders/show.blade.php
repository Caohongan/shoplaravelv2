@extends('layouts.app')
@section('title', 'Xem thứ tự')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header">
    <h4>Chi tiết đặt hàng</h4>
  </div>
  <div class="card-body">
    <table class="table">
      <thead>
      <tr>
        <th>Thông tin sản phẩm</th>
        <th class="text-center">Đơn giá</th>
        <th class="text-center">Số lượng</th>
        <th class="text-right item-amount">Tổng phụ</th>
      </tr>
      </thead>
      @foreach($order->items as $index => $item)
        <tr>
          <td class="product-info">
            <div class="preview">
              <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">
                <img src="{{ $item->product->image_url }}">
              </a>
            </div>
            <div>
              <span class="product-title">
                 <a target="_blank" href="{{ route('products.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
              </span>
              <span class="sku-title">{{ $item->productSku->title }}</span>
            </div>
          </td>
          <td class="sku-price text-center vertical-middle">￥{{ $item->price }}</td>
          <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
          <td class="item-amount text-right vertical-middle">￥{{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
        </tr>
      @endforeach
      <tr><td colspan="4"></td></tr>
    </table>
    <div class="order-bottom row">
      <div class="order-info col-lg-8 ">
        <div>
          <li>Địa chỉ giao hàng: </li>
          <b class="line-value">--{{ join(' ', $order->address) }}</b>
        </div>
        <div >
          <li>Lưu ý đặt hàng:</li>
          <b class="line-value">--{{ $order->remark ?: '-' }}</b>
        </div>
        <div>
          <li>Số thứ tự:</li>
          <b class="line-value">--{{ $order->no }}</b>
        </div>
        <!-- 输出物流状态 -->
        <div >
          <li>Tình trạng hậu cần:</li>
          <b class="line-value">--{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</b>
        </div>
        <!-- 如果有物流信息则展示 -->
        @if($order->ship_data)
          <div>
            <li>Thông tin hậu cần:</li>
            <b class="line-value">{{ $order->ship_data['express_company'] }} {{ $order->ship_data['express_no'] }}</b>
          </div>
        @endif
        <!-- 订单已支付，且退款状态不是未退款时展示退款信息 -->
        @if($order->paid_at && $order->refund_status !== \App\Models\Order::REFUND_STATUS_PENDING)
          <div">
            <li>Tình trạng hoàn tiền:</li>
            <b class="line-value">{{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}</b>
          </div>
          <div>
            <li>Lý do hoàn tiền:</li>
            <b class="line-value">{{ $order->extra['refund_reason'] }}</b>
          </div>
        @endif
      </div>
      <div class="order-summary text-right col-lg-3">
        <!-- 展示优惠信息开始 -->
        @if($order->couponCode)
          <div class="text-primary">
            <span>Thông tin ưu đãi:</span>
            <div class="value">{{ $order->couponCode->description }}</div>
          </div>
        @endif
        <!-- 展示优惠信息结束 -->
        <div class="total-amount">
          <span>Tổng giá đặt hàng:</span>
          <div class="value">￥{{ $order->total_amount }}</div>
        </div>
        <div>
          <span>Tình trạng đặt hàng:</span>
          <div class="value">
            @if($order->paid_at)
              @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
              Trả tiền khi nhận hàng
              @else
                {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
              @endif
            @elseif($order->closed)
              Đã đóng
            @else
              Chưa trả
            @endif
          </div>
          @if(isset($order->extra['refund_disagree_reason']))
            <div>
              <span>Lý do từ chối hoàn tiền:</span>
              <div class="value">{{ $order->extra['refund_disagree_reason'] }}</div>
            </div>
          @endif
        </div>
        <!-- 支付按钮开始 -->
        @if(!$order->paid_at && !$order->closed)
          <div class="payment-buttons">
            <a class="btn btn-primary btn-sm" href="{{ route('payment.alipay', ['order' => $order->id]) }}">支付宝支付</a>
            <!-- 把之前的微信支付按钮换成这个 -->
            <button class="btn btn-sm btn-success" id='btn-wechat'>Thanh toán WeChat</button>
          </div>
        @endif
        <!-- 支付按钮结束 -->
        <!-- 如果订单的发货状态为已发货则展示确认收货按钮 -->
        @if($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
          <div class="receive-button">
            <button type="button" id="btn-receive" class="btn btn-sm btn-success">Xác nhận đã nhận</button>
          </div>
        @endif
        <!-- 订单已支付，且退款状态是未退款时展示申请退款按钮 -->
        @if($order->paid_at && $order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
          <div class="refund-button">
            <button class="btn btn-sm btn-danger" id="btn-apply-refund">Yêu cầu hoàn lại tiền</button>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
  <script>
    $(document).ready(function() {
      // 微信支付按钮事件
      $('#btn-wechat').click(function() {
        swal({
          // content 参数可以是一个 DOM 元素，这里我们用 jQuery 动态生成一个 img 标签，并通过 [0] 的方式获取到 DOM 元素
          content: $('<img src="{{ route('payment.wechat', ['order' => $order->id]) }}" />')[0],
          // buttons 参数可以设置按钮显示的文案
          buttons: ['Đóng', 'Đã hoàn tất thanh toán'],
        })
          .then(function(result) {
            // 如果用户点击了 已完成付款 按钮，则重新加载页面
            if (result) {
              location.reload();
            }
          })
      });
      // 确认收货按钮点击事件
      $('#btn-receive').click(function() {
        // 弹出确认框
        swal({
          title: "Xác nhận đã nhận được hàng",
          icon: "warning",
          dangerMode: true,
          buttons: ['No', 'Xác nhận đã nhận'],
        })
          .then(function(ret) {
            // 如果点击取消按钮则不做任何操作
            if (!ret) {
              return;
            }
            // ajax 提交确认操作
            axios.post('{{ route('orders.received', [$order->id]) }}')
              .then(function () {
                // 刷新页面
                location.reload();
              })
          });
      });
      // 退款按钮点击事件
      $('#btn-apply-refund').click(function () {
        swal({
          text: 'Vui lòng nhập một lý do cho việc hoàn trả',
          content: "input",
        }).then(function (input) {
          // 当用户点击 swal 弹出框上的按钮时触发这个函数
          if(!input) {
            swal('Lý do hoàn tiền không thể để trống', '', 'error');
            return;
          }
          // 请求退款接口
          axios.post('{{ route('orders.apply_refund', [$order->id]) }}', {reason: input})
            .then(function () {
              swal('Yêu cầu hoàn trả đã được gửi đi', '', 'success').then(function () {
                // 用户点击弹框上按钮时重新加载页面
                location.reload();
              });
            });
        });
      });

    });
  </script>
@endsection
