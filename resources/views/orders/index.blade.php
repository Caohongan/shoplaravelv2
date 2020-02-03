@extends('layouts.app')
@section('title', 'Danh sách đặt hàng')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header">Danh sách đặt hàng</div>
  <div class="card-body">
    <ul class="list-group">
      @foreach($orders as $order)
        <li class="list-group-item">
          <div class="card">
            <div class="card-header">
            Mã sản phẩm : {{ $order->no }}
              <span class="float-right">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
            </div>
            <div class="card-body">
              <table class="table">
                <thead>
                <tr>
                  <th>Thông tin sản phẩm</th>
                  <th class="text-center">Đơn giá</th>
                  <th class="text-center">Số lượng</th>
                  <th class="text-center">Tổng giá đặt hàng</th>
                  <th class="text-center">Tình trạng</th>
                  <th class="text-center">Hoạt động</th>
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
                    <td class="sku-price text-center">${{ $item->price }}</td>
                    <td class="sku-amount text-center">{{ $item->amount }}</td>
                    @if($index === 0)
                      <td rowspan="{{ count($order->items) }}" class="text-center total-amount">${{ $order->total_amount }}</td>
                      <td rowspan="{{ count($order->items) }}" class="text-center">
                        @if($order->paid_at)
                          @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                            Không hoàn trả hàng <i class="fas fa-handshake"></i>
                          @else
                            Hoản trả hàng <i class="fas fa-times-circle"></i>
                          @endif
                        @elseif($order->closed)
                          Đã đóng
                        @else
                        Chưa trả<br>
                        Xin vui lòng tại {{ $order->created_at->addSeconds(config('app.order_ttl'))->format('H:i') }} Thanh toán trước khi hoàn thành<br>
                        Nếu không, lệnh sẽ được đóng tự động
                        @endif
                      </td>
                      <td rowspan="{{ count($order->items) }}" class="text-center">
                        <a class="btn btn-danger btn-sm" href="{{ route('orders.show', ['order' => $order->id]) }}">Kiểm tra sản phẩm</a>
                        <!-- 评价入口开始 -->
                        @if($order->paid_at)
                          <a class="btn btn-success btn-sm" href="{{ route('orders.review.show', ['order' => $order->id]) }}">
                            {{ $order->reviewed ? 'Xem đánh giá' : 'Đánh giá' }}
                          </a>
                        @endif
                        <!-- 评价入口结束 -->
                      </td>
                    @endif
                  </tr>
                @endforeach
              </table>
            </div>
          </div>
        </li>
      @endforeach
    </ul>
    <div class="float-right">{{ $orders->render() }}</div>
  </div>
</div>
</div>
</div>
@endsection
